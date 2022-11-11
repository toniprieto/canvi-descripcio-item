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
            "/^1A *r?e?impr?\.?$/i",
        ];

        $patronsSimpleVolum = [
            "/^v\.? *([0-9]+)$/i",
            "/^volu?m?\.? *([0-9]+)$/i",
        ];

        $patronsSimpleReimpressio = [
            "/^r?e?impr?\.?,? *([0-9]{4})$/i",
            "/^reimpressió +([0-9]{4})$/i",
            "/^REIMPRESSIÓ +([0-9]{4})$/i",
            //Amb ordinals
            "/^[0-9]{1,2}A *r?e?impr?\.? *([0-9]{4})$/i",
            "/^[0-9]{1,2}A *REIMPRESSIÓ *([0-9]{4})$/i",
            "/^[0-9]{1,2}A *reimpressió *([0-9]{4})$/i",
            // Ordinals Sense el any
            "/^[0-9]{1,2}A *r?e?impr?\.?$/i",
            "/^[0-9]{1,2}A *REIMPRESSIÓ$/i",
            "/^[0-9]{1,2}A *reimpressió$/i"
        ];


        $patronsDobles = [
            "/^volu?m?\.? *([0-9]+),? *\(?r?e?impr?\.? *([0-9]{4})\)?$/i",
            "/^volu?m?\.? *([0-9]+),? *reimpressió *([0-9]{4})$/i",
            "/^volu?m?\.? *([0-9]+),? *REIMPRESSIÓ *([0-9]{4})$/i",
            "/^v\.? *([0-9]+),? *\(?r?e?impr?\.? *([0-9]{4})\)?$/i",
            "/^v\.? *([0-9]+),? *reimpressió *([0-9]{4})$/i",
            "/^v\.? *([0-9]+),? *REIMPRESSIÓ *([0-9]{4})$/i",
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
                $partReimpressio = "Reimpressió " . $matches[2];
                if ($in["public_note"] != "") {
                    $publicNote = trim($in["public_note"]) . " | " .  $partReimpressio;
                } else {
                    $publicNote = $partReimpressio;
                }

                return ["description" => $matches[1], "public_note" => $publicNote, "found" => true];
            }
        }

        $out = $in;
        $out["found"] = false;

        return $out;
    }
}