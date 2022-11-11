<?php
namespace UPCSBPA\CanviDescripcioItem\tests\conversor;

use UPCSBPA\CanviDescripcioItem\conversor\ItemDescription;
use PHPUnit\Framework\TestCase;

class ItemDescriptionTest extends TestCase
{

    /**
     * @dataProvider itemDescriptionsProvider
     */
    public function testConvert($in, $out)
    {
        $calculatedOut = ItemDescription::convert($in);

        self::assertEquals($out["found"], $calculatedOut["found"]);
        if ($out["found"]) {
            self::assertEquals($out["description"], $calculatedOut["description"]);
            self::assertEquals($out["public_note"], $calculatedOut["public_note"]);
        }
    }

    public function itemDescriptionsProvider()
    {
        return [
            [ ["description" => "VOL 20", "public_note" => ""], ["description" => "20", "public_note" => "", "found" => true]],
            [ ["description" => "Vol 6", "public_note" => ""], ["description" => "6", "public_note" => "", "found" => true]],
            [ ["description" => "VOLUM 7", "public_note" => ""], ["description" => "7", "public_note" => "", "found" => true]],
            [ ["description" => "VOL. 1", "public_note" => ""], ["description" => "1", "public_note" => "", "found" => true]],
            [ ["description" => "VOL. 2", "public_note" => ""], ["description" => "2", "public_note" => "", "found" => true]],
            [ ["description" => "Vol.6", "public_note" => ""], ["description" => "6", "public_note" => "", "found" => true]],

            [ ["description" => "REIMPR.2002", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2002", "found" => true]],
            [ ["description" => "IMPR 2000", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2000", "found" => true]],
            [ ["description" => "REIMPR. 2001", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2001", "found" => true]],
            [ ["description" => "REIMPR., 1988", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1988", "found" => true]],
            [ ["description" => "REIMPRESSIÓ 2001", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2001", "found" => true]],



            [ ["description" => "VOL. 4 REIMPR. 2004", "public_note" => ""], ["description" => "4", "public_note" => "Reimpressió 2004", "found" => true]],
            [ ["description" => "VOL. 3, REIMPR. 2003", "public_note" => "Nota"], ["description" => "3", "public_note" => "Nota | Reimpressió 2003", "found" => true]],
            [ ["description" => "VOL. 5 Reimpr 2005", "public_note" => ""], ["description" => "5", "public_note" => "Reimpressió 2005", "found" => true]],
            [ ["description" => "Vol. 6, REIMPRESSIÓ 2006", "public_note" => ""], ["description" => "6", "public_note" => "Reimpressió 2006", "found" => true]],
            [ ["description" => "VOL.2,REIMPR.2004", "public_note" => ""], ["description" => "2", "public_note" => "Reimpressió 2004", "found" => true]],
            [ ["description" => "VOL. 6 (REIMPR. 1969)", "public_note" => ""], ["description" => "6", "public_note" => "Reimpressió 1969", "found" => true]],
            [ ["description" => "VOL. 6 (REIMPR. 1969)", "public_note" => ""], ["description" => "6", "public_note" => "Reimpressió 1969", "found" => true]],
            [ ["description" => "VOL. 3, REIMP. 2003", "public_note" => "Nota"], ["description" => "3", "public_note" => "Nota | Reimpressió 2003", "found" => true]],

            [ ["description" => "1A IMPR.", "public_note" => "Nota"], ["description" => "IMPR.", "public_note" => "Nota", "found" => true]],
            [ ["description" => "2A IMPR. 1960", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1960", "found" => true]],
            [ ["description" => "1A REIMP 1981", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1981", "found" => true]],
            [ ["description" => "17A REIMPR. 1988", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1988", "found" => true]],
            [ ["description" => "7A Reimpressió 2021", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2021", "found" => true]],
            [ ["description" => "98A REIMPRESSIÓ 2022", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2022", "found" => true]],
            [ ["description" => "28A reimpressió 2023", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2023", "found" => true]],

            // Sense ordinal
            [ ["description" => "A reimpressió 1980", "public_note" => ""], ["found" => false]],
            // Provar final diferent
            [ ["description" => "VOL. 2A", "public_note" => ""], ["found" => false]]
        ];
    }
}