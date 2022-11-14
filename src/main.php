<?php
/**
 * Executable principal:
 *
 * Llançar com:
 *
 * php main.php -i <id_item_set>
 * php main.php -b <id_bib_set>
 * php main.php -m <mms_id>
 * php main.php -p <barcode>
 *
 * Amb opció -a per aplicar canvis.
 */

use UPCSBPA\CanviDescripcioItem\ALMA\AlmaClient;
use UPCSBPA\CanviDescripcioItem\ALMA\exceptions\ALMAException;
use UPCSBPA\CanviDescripcioItem\conversor\ItemDescription;

require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . "/config.php";

// Alma client
$almaClient = new AlmaClient();
$almaClient->configAPI(APIREST_ALMA_URLBASE, APIREST_ALMA_KEY);

$opcions = getopt("i:b:m:p:adh", array("itemset:","bibset:","mmsid:","physicalbarcode","aplicacanvis","debug","help"));

if (isset($opcions["h"])) {
    printHelp();
    exit(0);
}

$debug = false;
if (isset($opcions["d"])) {
    $debug = true;
}

// Per proves
//$opcions["m"] = "991005058576006711";

// Recuperem el set o bibId a processar
$setId = null;
$mmsId = null;
$barcodeId = null;
$mode = null;

if (!isset($opcions["i"]) && !isset($opcions["b"]) && !isset($opcions["m"]) && !isset($opcions["p"])) {
    echo "Cal proporcionar un set d'ítems (-i), un set de bibliogràfics (-b) o un MmsId (-m) o un Codi de barres (-p)\n";
    exit(-1);
} else {
    if (isset($opcions["i"])) {
        $setId = $opcions["i"];
        $mode = "item";
    } else if (isset($opcions["b"])) {
        $setId = $opcions["b"];
        $mode = "bib";
    } else if (isset($opcions["m"])) {
        $mmsId = $opcions["m"];
    } else if (isset($opcions["p"])) {
        $barcodeId = $opcions["p"];
    }
}

// Apliquem els canvis
$ferCanvis = false;
if (isset($opcions['a'])) {
    $ferCanvis = true;
}

//Creem arxiu on registrar els canvis
$outFile = fopen(__DIR__ . "/../logs/canvis-" . date('Y-m-d') . "-". time() . ".csv" , 'w');
fputcsv($outFile, ["mms_id", "holding_id", "item_id", "Codi barres", "Descripció original",
    "Nota original", "Patró trobat", "Descripció nova", "Nota nova", "Auto-cancel·lables","MODIFICAT"]);

try {

    if (isset($setId)) {

        $limitSet = 100;
        $offset = 0;

        $response = $almaClient->getSetMembers($setId, $limitSet, $offset);
        $totalMembers = $response->total_record_count;

        $continua = true;
        while ($continua) {

            foreach ($response->member as $num => $member) {

                if ($mode == "item") {

                    // Obtenim els ids a partir del link
                    if (preg_match("/bibs\/([0-9]+)\/holdings\/([0-9]+)\/items\/([0-9]+)/", $member->link, $matches)) {
                        $mmsId = $matches[1];
                        $holdingId = $matches[2];
                        $pid = $matches[3];

                        $item = $almaClient->getItem($mmsId, $holdingId, $pid);

                        processaItem($mmsId, $item, $ferCanvis);

                    } else {
                        echo "ERROR: no s'ha pogut obtenir les dades de l'ítem: " . $member->id . "\n";
                    }

                } else if ($mode == "bib") {

                    processaBibliografic($member->id, $ferCanvis);

                } else {

                    echo "ERROR: mode incorrecte\n";
                    exit(0);

                }
            }

            // Actualitzem offset
            $offset += $limitSet;

            if ($offset >= $totalMembers) {
                $continua = false;
            } else {
                $response = $almaClient->getSetMembers($setId, $limitSet, $offset);
            }
        }

    } else if (isset($mmsId)) {
        processaBibliografic($mmsId, $ferCanvis);
    } else if (isset($barcodeId)) {
        processaItemDesdeCodiBarres($barcodeId, $ferCanvis);
    }

} catch (ALMAException $e) {
    printException("Error processant canvis", $e);
}

// Tanquem l'arxiu
fclose($outFile);


/**
 * Processa un registre de bibliogràfic, crida a processaItem per tots els seus ítems
 *
 * @param $mmsId
 * @param $ferCanvis
 * @throws \UPCSBPA\CanviDescripcioItem\ALMA\exceptions\ALMAHTTPException
 */
function processaBibliografic($mmsId, $ferCanvis) {

    global $almaClient;

    $limit = 100;
    $offset = 0;

    $items = $almaClient->getHoldingItems($mmsId, "ALL", [], ["offset"=> $offset, "limit" => $limit]);
    $total = $items->total_record_count;

    if ($total > 0) {
        $continua = true;
        while ($continua) {

            foreach ($items->item as $item) {
                processaItem($mmsId, $item, $ferCanvis);
            }

            // Actualitzem offset
            $offset += $limit;

            if ($offset >= $total) {
                $continua = false;
            } else {
                $items = $almaClient->getHoldingItems($mmsId, "ALL", [], ["offset"=> $offset, "limit" => $limit]);
            }

        }
    }
}

/**
 * Processa un item des d'un codi de barres. Recupera el item de l'API i criaa a la funció que
 * processa les dades
 *
 * @param $barcode
 * @param $ferCanvis
 * @throws \UPCSBPA\CanviDescripcioItem\ALMA\exceptions\ALMAHTTPException
 */
function processaItemDesdeCodiBarres($barcode, $ferCanvis) {

    global $almaClient;

    // Recuperem el item per codi de barres i el processem
    $item = $almaClient->getItemFromBarcode($barcode);
    processaItem($item->bib_data->mms_id, $item, $ferCanvis);
}

/**
 * Processa un registre d'item
 *
 * @param $mmsId string mmsId d'ALMA del bibliogràfic
 * @param $item mixed amb dades del ítem recuperades de l'API
 * @throws \UPCSBPA\CanviDescripcioItem\ALMA\exceptions\ALMAHTTPException
 */
function processaItem($mmsId, $item, $ferCanvis) {

    global $almaClient, $debug, $outFile;

    $calModificarRegistre = false;
    $modificat = false;
    $holdingId = $item->holding_data->holding_id;
    $barcode = $item->item_data->barcode;

    echo "Processant " . $barcode . " :\n";
    //print_r($item);

    // Preparem els camps per fer la conversió
    $in = [
        "description" => $item->item_data->description,
        "public_note" => $item->item_data->public_note
    ];

    // Fem la conversió
    $out = ItemDescription::convert($in);

    if ($debug) {
        echo "ENTRADA $barcode\n";
        print_r($in);
        echo "RESULTAT\n";
        print_r($out);
    }

    if ($out["found"]) {

        if ($item->item_data->description != $out["description"]) {
            $item->item_data->description = $out["description"];
            $calModificarRegistre = true;
        }

        if ($item->item_data->public_note != $out["public_note"]) {
            $item->item_data->public_note = $out["public_note"];
            $calModificarRegistre = true;
        }

        if ($calModificarRegistre) {

            // Mirem si té reserves que es cancelen al canviar la descripció
            $teReservesAutoCancelables = teReservesAutoCancelables($mmsId, $holdingId, $item);

            if (!$teReservesAutoCancelables) {

                // El marquem que es modifica, per mostrar al csv
                $modificat = true;

                if ($ferCanvis) {
                    echo "\tMODIFICANT " . $barcode . "\n";
                    $almaClient->updateItem($mmsId, $holdingId, $item->item_data->pid, $item);
                } else {
                    echo "\tMODIFICARIA " . $barcode . "\n";
                }

            } else {
                echo "\tNO MODIFIQUEM. Té reserves auto-cancel·lables\n";
            }
        }

    }

    // Guardem les dades processades al csv
    $dades = [];
    $dades[] = $mmsId;
    $dades[] = $holdingId;
    $dades[] = $item->item_data->pid;
    $dades[] = $barcode;
    $dades[] = $in["description"];
    $dades[] = $in["public_note"];
    $dades[] = $out["found"]?"Sí":"No";
    $dades[] = $out["description"];
    $dades[] = $out["public_note"];
    if ($calModificarRegistre) {
        $dades[] = $teReservesAutoCancelables?"Sí":"No";
    } else {
        $dades[] = "No evaluat";
    }
    $dades[] = $modificat?"Sí":"No";
    fputcsv($outFile, $dades);
}

/**
 * Retorna si l'ítem té reserves que es cancelen al canviar la descripció.
 *
 * @param $mmsId
 * @param $holdingId
 * @param $item
 * @return bool
 * @throws \UPCSBPA\CanviDescripcioItem\ALMA\exceptions\ALMAHTTPException
 */
function teReservesAutoCancelables($mmsId, $holdingId, $item) {

    global $almaClient, $debug;

    $requests = $almaClient->getItemRequests($mmsId, $holdingId, $item->item_data->pid);

    if ($debug) {
        echo "RESERVES\n";
        print_r($requests);
        echo "\n";
    }

    if (isset($requests->user_request)) {
        foreach($requests->user_request as $request) {
            if (esReservaAutoCancelable($request)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Retorna si la reserva s'autocancela al canviar la restricció:
 * Només mira si alguna de les reserves té Task_name = "Pickup From Shelf"
 * o request_status = "Not Started"
 *
 * @param $request
 * @return bool
 */
function esReservaAutoCancelable($request) {

    if (isset($request->task_name) && normalitzaValor($request->task_name) == normalitzaValor("Pickup From Shelf")) {
        return true;
    }
    if (isset($request->request_status) && normalitzaValor($request->request_status) == normalitzaValor("Not Started")) {
        return true;
    }

    return false;
}

/**
 * Passa un valor a majúscula i substitueix espais per guions baixos.
 * Ens hem trobat que en algun cas, com el request_status = Not Started
 * ve com NOT_STARTED
 * Fem servir aquesta funció per estar segurs que ve amb el mateix format al comparar
 *
 * @param $valor string amb valor, exemple "Not Started"
 * @return array|string|string[] nou string, exemple NOT_STARTED
 */
function normalitzaValor($valor) {
    return str_replace(" ", "_",strtoupper($valor));
}

/**
 * Imprimeix opcions executable
 */
function printHelp(){

    echo "usage: php main.php\n";

    echo " -i,--itemset" . "\t\t"      . "Set amb els ítems a tractar\n";
    echo " -b,--bibset" . "\t\t"       . "Set amb els bibliogafics a tractar\n";
    echo " -m,--mmsid" . "\t\t"       . "Id MMS d'un bibliogràfic a tractar\n";
    echo " -p,--physicalbarcode" . "\t\t"       . "Codi de barres del ítem a tractar\n";
    echo " -a,--aplicacanvis" . "\t\t"    . "Aplica els canvis, si no, només crea un CSV amb les dades que processa\n";
    echo " -d,--debug" . "\t\t"      . "Imprimeix missatges de debug\n";
    echo " -h,--help" . "\t\t"      . "Imprimeix aquesta ajuda\n";
    echo "\n";
}

/**
 * @param $message
 * @param $e Exception
 */
function printException($message, $e) {
    echo $message . "\n";
    if ($e instanceof ALMAException) {
        echo $e->getPrintMessage(true, true);
    } else {
        echo $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
}
