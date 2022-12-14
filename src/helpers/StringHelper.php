<?php
namespace UPCSBPA\CanviDescripcioItem\helpers;


class StringHelper
{
    public static function removeSpaces($input)
    {
        return preg_replace('/\s+/', ' ', $input);
    }

    static function capitalize($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("i"))
    {
        /*
         * Exceptions in lower case are words you don't want converted
         * Exceptions all in upper case are any words you don't want converted to title case
         *   but should be converted to upper case, e.g.:
         *   king henry viii or king henry Viii should be King Henry VIII
         */
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $wordnr => $word) {
                if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtoupper($word, "UTF-8");
                } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtolower($word, "UTF-8");
                } elseif (!in_array($word, $exceptions)) {
                    // convert to uppercase (non-utf8 only)
                    $word = ucfirst($word);
                }
                array_push($newwords, $word);
            }
            $string = join($delimiter, $newwords);
        }//foreach
        return $string;
    }

    static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    static function contains($haystack, $needle) {

        if (strpos($haystack, $needle) !== false) {
            return true;
        } else {
            return false;
        }
    }

    static function substr_after($haystack, $needle) {
        return substr($haystack, strpos($haystack, $needle) + strlen($needle));
    }

    static function substr_before($haystack, $needle) {
        return substr($haystack, 0, strpos($haystack, $needle));
    }

}