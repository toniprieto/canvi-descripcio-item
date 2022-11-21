<?php

namespace UPCSBPA\CanviDescripcioItem\conversor;

/*
 * Classe utilitat per convertir el camp descripcio d'un item a una nova descripció + nota publica
 */

use UPCSBPA\CanviDescripcioItem\helpers\StringHelper;

class ItemDescription
{

    /**
     * Converteix els mateixos paràmetres de l'entrada en forma d'array
     *
     * @param $in array amb els valors description i public_note
     * @return array amb els mateixos valors de l'entrada transformats i un booleà indicant si s'ha trobat
     */
    public static function convert($in) {

        $patronsDescartats = [
            "/^1A\.? *r?e?impr?\.?,?$/i",
            "/^(.*) 1A\.? *\(?r?e?impr?\.?,? *([0-9]{4}|[0-9]{4}-[0-9]{4})\)?$/i",
            "/^(?:v|volu?m?)\.? *([0-9]+),? 1A\.? *(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})\)?$/i",
        ];

        $patronsSimpleVolum = [
            "/^(?:v|volu?m?)\.? *([0-9]+)$/i",
        ];

        $patronsSimpleReimpressio = [
            "/^(?:r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})$/i",
            //Amb ordinals
            "/^[0-9]{1,2}A\.? (?:r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})$/i",
            // Ordinals Sense el any
            "/^[0-9]{1,2}A\.? (?:r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ)$/i",
        ];

        $patronsDobles = [
            // No ordinals
            "/^(?:v|volu?m?)\.? *([0-9]+)[[:punct:]]? *(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})\)?$/i",
            //Amb ordinals
            "/^(?:v|volu?m?)\.? *([0-9]+)[[:punct:]]? [0-9]{1,2}A\.? *(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})\)?$/i",
            // No ordinals sense any
            "/^(?:v|volu?m?)\.? *([0-9]+)[[:punct:]]? *(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ)\)?$/i",
            //Amb ordinals sense any
            "/^(?:v|volu?m?)\.? *([0-9]+)[[:punct:]]? [0-9]{1,2}A\.? *(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ)\)?$/i",
        ];

        $patronsDoblesInvers = [
            "/^(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})\)?[[:punct:]]? *(?:v|volu?m?)\.? *([0-9]+)$/i",
            //Amb ordinals
            "/^[0-9]{1,2}A\.? *(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})\)?[[:punct:]]? (?:v|volu?m?)\.? *([0-9]+)$/i",
            // No ordinals sense any
            "/^(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ)\)?[[:punct:]]? * (?:v|volu?m?)\.? *([0-9]+)$/i",
            //Amb ordinals sense any
            "/^[0-9]{1,2}A\.? *(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ)\)?[[:punct:]]? (?:v|volu?m?)\.? *([0-9]+)$/i",
        ];

        $patronsGeneralsReimpressio = [
            "/^(.*)[[:punct:]] *(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})\)?$/i",
            "/^(.*) +(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})\)?$/i",
            // Reimpresio al final Sense any
            "/^(.*)[[:punct:]] *(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ)$/i",
            "/^(.*) +(?:r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ)$/i",
        ];

        $patronsGeneralsReimpressioInvers = [
            // Reimpressio al inici
            "/^(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})\)? ?[[:punct:]] *(.*)$/i",
            "/^(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) *([0-9]{4}|[0-9]{4}-[0-9]{4})\)? +(.*)$/i",
            // Reimpressio al inici sense any
            "/^(?:\(?r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ)[[:punct:]] *(.*)$/i",
            "/^(?:r?e?impr?\.?,?|reimpressió|REIMPRESSIÓ) +(.*)$/i",
        ];


        // Processem primer patrons que no volem convertir ara per ara
        foreach($patronsDescartats as $patron) {
            if (preg_match($patron,trim($in["description"]), $matches)) {
                return ["found" => false];
            }
        }

        foreach($patronsSimpleVolum as $patron) {
            if (preg_match($patron,trim($in["description"]), $matches)) {
                return ["description" => $matches[1], "public_note" => $in["public_note"], "found" => true];
            }
        }

        foreach($patronsSimpleReimpressio as $patron) {
            if (preg_match($patron,trim($in["description"]), $matches)) {
                if (isset($matches[1])) {
                    $partReimpressio = "Reimpressió " . $matches[1];
                } else {
                    $partReimpressio = "Reimpressió";
                }
                if ($in["public_note"] != "") {
                    $publicNote = trim($in["public_note"]) . " | " .  $partReimpressio;
                } else {
                    $publicNote = $partReimpressio;
                }
                return ["description" => "", "public_note" => $publicNote, "found" => true];
            }
        }

        foreach($patronsDobles as $patron) {
            if (preg_match($patron,trim($in["description"]), $matches)) {
                if (isset($matches[2])) {
                    $partReimpressio = "Reimpressió " . $matches[2];
                } else {
                    $partReimpressio = "Reimpressió";
                }
                if ($in["public_note"] != "") {
                    $publicNote = trim($in["public_note"]) . " | " .  $partReimpressio;
                } else {
                    $publicNote = $partReimpressio;
                }

                return ["description" => $matches[1], "public_note" => $publicNote, "found" => true];
            }
        }

        foreach($patronsDoblesInvers as $patron) {
            if (preg_match($patron,trim($in["description"]), $matches)) {
                if (isset($matches[2])) {
                    $partReimpressio = "Reimpressió " . $matches[1];
                    $novaDescripcio = $matches[2];
                } else {
                    $partReimpressio = "Reimpressió";
                    $novaDescripcio =  $matches[1];
                }
                if ($in["public_note"] != "") {
                    $publicNote = trim($in["public_note"]) . " | " .  $partReimpressio;
                } else {
                    $publicNote = $partReimpressio;
                }

                return ["description" => $novaDescripcio, "public_note" => $publicNote, "found" => true];
            }
        }

        foreach($patronsGeneralsReimpressio as $patron) {
            if (preg_match($patron,trim($in["description"]), $matches)) {
                if (isset($matches[2])) {
                    $partReimpressio = "Reimpressió " .$matches[2];
                } else {
                    $partReimpressio = "Reimpressió";
                }
                if ($in["public_note"] != "") {
                    $publicNote = trim($in["public_note"]) . " | " .  $partReimpressio;
                } else {
                    $publicNote = $partReimpressio;
                }

                return ["description" => $matches[1], "public_note" => $publicNote, "found" => true];
            }
        }

        foreach($patronsGeneralsReimpressioInvers as $patron) {
            if (preg_match($patron,trim($in["description"]), $matches)) {
                if (isset($matches[2])) {
                    $partReimpressio = "Reimpressió " . $matches[1];
                    $novaDescripcio = $matches[2];
                } else {
                    $partReimpressio = "Reimpressió";
                    $novaDescripcio =  $matches[1];
                }
                if ($in["public_note"] != "") {
                    $publicNote = trim($in["public_note"]) . " | " .  $partReimpressio;
                } else {
                    $publicNote = $partReimpressio;
                }

                return ["description" => $novaDescripcio, "public_note" => $publicNote, "found" => true];
            }
        }

        $out = $in;
        $out["found"] = false;

        return $out;
    }
}
