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
            [ ["description" => "V. 5", "public_note" => ""], ["description" => "5", "public_note" => "", "found" => true]],
            [ ["description" => "v 4", "public_note" => ""], ["description" => "4", "public_note" => "", "found" => true]],

            [ ["description" => "REIMPR.2002", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2002", "found" => true]],
            [ ["description" => "IMPR 2000", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2000", "found" => true]],
            [ ["description" => "REIMPR. 2001", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2001", "found" => true]],
            [ ["description" => "REIMPR., 1988", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1988", "found" => true]],
            [ ["description" => "REIMPRESSIÓ 2001", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2001", "found" => true]],
            [ ["description" => "reimpressió 2001", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2001", "found" => true]],

            [ ["description" => "VOL. 4 REIMPR. 2004", "public_note" => ""], ["description" => "4", "public_note" => "Reimpressió 2004", "found" => true]],
            [ ["description" => "VOL. 3, REIMPR. 2003", "public_note" => "Nota"], ["description" => "3", "public_note" => "Nota | Reimpressió 2003", "found" => true]],
            [ ["description" => "VOL. 5 Reimpr 2005", "public_note" => ""], ["description" => "5", "public_note" => "Reimpressió 2005", "found" => true]],
            [ ["description" => "Vol. 6, REIMPRESSIÓ 2006", "public_note" => ""], ["description" => "6", "public_note" => "Reimpressió 2006", "found" => true]],
            [ ["description" => "VOL.2,REIMPR.2004", "public_note" => ""], ["description" => "2", "public_note" => "Reimpressió 2004", "found" => true]],
            [ ["description" => "VOL. 6 (REIMPR. 1969)", "public_note" => ""], ["description" => "6", "public_note" => "Reimpressió 1969", "found" => true]],
            [ ["description" => "VOL. 6 (REIMPR. 1969)", "public_note" => ""], ["description" => "6", "public_note" => "Reimpressió 1969", "found" => true]],
            [ ["description" => "VOL. 3, REIMP. 2003", "public_note" => "Nota"], ["description" => "3", "public_note" => "Nota | Reimpressió 2003", "found" => true]],
            [ ["description" => "V. 6, REIMPRESSIÓ 2006", "public_note" => ""], ["description" => "6", "public_note" => "Reimpressió 2006", "found" => true]],
            [ ["description" => "V. 6, REIMPRESSIÓ 2006", "public_note" => ""], ["description" => "6", "public_note" => "Reimpressió 2006", "found" => true]],

            [ ["description" => "VOL. 2, REIMPR., 1990", "public_note" => ""], ["description" => "2", "public_note" => "Reimpressió 1990", "found" => true]],
            [ ["description" => "VOL. 1, REIMPR, 1993", "public_note" => ""], ["description" => "1", "public_note" => "Reimpressió 1993", "found" => true]],


            [ ["description" => "2A IMPR.", "public_note" => "Nota"], ["description" => "", "public_note" => "Nota | Reimpressió", "found" => true]],
            [ ["description" => "2A IMPR. 1960", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1960", "found" => true]],
            [ ["description" => "4A REIMP 1981", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1981", "found" => true]],
            [ ["description" => "17A REIMPR. 1988", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1988", "found" => true]],
            [ ["description" => "7A Reimpressió 2021", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2021", "found" => true]],
            [ ["description" => "98A REIMPRESSIÓ 2022", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2022", "found" => true]],
            [ ["description" => "28A reimpressió 2023", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2023", "found" => true]],

            [ ["description" => "2A. IMPR.", "public_note" => "Nota"], ["description" => "", "public_note" => "Nota | Reimpressió", "found" => true]],
            [ ["description" => "2A. IMPR. 1960", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1960", "found" => true]],
            [ ["description" => "4A. REIMP 1981", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1981", "found" => true]],
            [ ["description" => "17A. REIMPR. 1988", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 1988", "found" => true]],
            [ ["description" => "7A. Reimpressió 2021", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2021", "found" => true]],
            [ ["description" => "98A. REIMPRESSIÓ 2022", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2022", "found" => true]],
            [ ["description" => "28A. reimpressió 2023", "public_note" => ""], ["description" => "", "public_note" => "Reimpressió 2023", "found" => true]],

            [ ["description" => "V.2, 4A REIMP 1981", "public_note" => ""], ["description" => "2", "public_note" => "Reimpressió 1981", "found" => true]],
            [ ["description" => "REIMPR. 2008 - CD-ROM 1", "public_note" => ""], ["description" => "CD-ROM 1", "public_note" => "Reimpressió 2008", "found" => true]],
            [ ["description" => "V.2, 2A IMPR. 1989", "public_note" => ""], ["description" => "2", "public_note" => "Reimpressió 1989", "found" => true]],
            [ ["description" => "V. 1 10A IMPR. 1976", "public_note" => ""], ["description" => "1", "public_note" => "Reimpressió 1976", "found" => true]],

            [ ["description" => "V.2, 4A REIMP", "public_note" => ""], ["description" => "2", "public_note" => "Reimpressió", "found" => true]],
            [ ["description" => "V.2, 2A IMPR.", "public_note" => ""], ["description" => "2", "public_note" => "Reimpressió", "found" => true]],
            [ ["description" => "V. 1 10A IMPR.", "public_note" => ""], ["description" => "1", "public_note" => "Reimpressió", "found" => true]],

            [ ["description" => "REIMPR. 2008 - CD-ROM 1", "public_note" => ""], ["description" => "CD-ROM 1", "public_note" => "Reimpressió 2008", "found" => true]],
            [ ["description" => "REIMPR. 1986, ANNEX", "public_note" => ""], ["description" => "ANNEX", "public_note" => "Reimpressió 1986", "found" => true]],
            [ ["description" => "REIMPR.2006 APÈNDIX", "public_note" => ""], ["description" => "APÈNDIX", "public_note" => "Reimpressió 2006", "found" => true]],
            [ ["description" => "REIMPR., CD", "public_note" => ""], ["description" => "CD", "public_note" => "Reimpressió", "found" => true]],
            [ ["description" => "REIMPR. 2001 CD-ROM 4 ", "public_note" => ""], ["description" => "CD-ROM 4", "public_note" => "Reimpressió 2001", "found" => true]],
            [ ["description" => "REIMP. 2001.MANUAL", "public_note" => ""], ["description" => "MANUAL", "public_note" => "Reimpressió 2001", "found" => true]],
            [ ["description" => "REIMP.1986 V.1 T.2 ", "public_note" => ""], ["description" => "V.1 T.2", "public_note" => "Reimpressió 1986", "found" => true]],
            [ ["description" => "REIMP.2006 V.2 T.1 ", "public_note" => ""], ["description" => "V.2 T.1", "public_note" => "Reimpressió 2006", "found" => true]],
            [ ["description" => "REIMPR. 1975-1981, VOL. 1", "public_note" => ""], ["description" => "1", "public_note" => "Reimpressió 1975-1981", "found" => true]],
            [ ["description" => "REIMPR.2007-VOL. 1", "public_note" => ""], ["description" => "1", "public_note" => "Reimpressió 2007", "found" => true]],


            // Patrons generals reimpressió al final
            [ ["description" => "ANNEX, REIMPR. 1986", "public_note" => ""], ["description" => "ANNEX", "public_note" => "Reimpressió 1986", "found" => true]],
            [ ["description" => "APÈNDIX REIMPR.2006", "public_note" => ""], ["description" => "APÈNDIX", "public_note" => "Reimpressió 2006", "found" => true]],
            [ ["description" => "CD, REIMPR.", "public_note" => ""], ["description" => "CD", "public_note" => "Reimpressió", "found" => true]],
            [ ["description" => "CD-ROM 4 REIMPR. 2001", "public_note" => ""], ["description" => "CD-ROM 4", "public_note" => "Reimpressió 2001", "found" => true]],
            [ ["description" => "MANUAL.REIMP. 2001", "public_note" => ""], ["description" => "MANUAL", "public_note" => "Reimpressió 2001", "found" => true]],
            [ ["description" => "V.1 T.2 REIMP.1986", "public_note" => ""], ["description" => "V.1 T.2", "public_note" => "Reimpressió 1986", "found" => true]],
            [ ["description" => "V.2 T.1 REIMP.2006", "public_note" => ""], ["description" => "V.2 T.1", "public_note" => "Reimpressió 2006", "found" => true]],
            [ ["description" => "VOL. 1, REIMPR. 1975-1981", "public_note" => ""], ["description" => "1", "public_note" => "Reimpressió 1975-1981", "found" => true]],
            [ ["description" => "VOL. 1-REIMPR.2007", "public_note" => ""], ["description" => "1", "public_note" => "Reimpressió 2007", "found" => true]],


            // excepció primera impressió
            [ ["description" => "1A impr.", "public_note" => ""], ["found" => false]],
            [ ["description" => "1A. impr.", "public_note" => ""], ["found" => false]],
            [ ["description" => "V.2, 1A IMP 1981", "public_note" => ""], ["found" => false]],
            [ ["description" => "CD-ROM, 1A IMP 1981", "public_note" => ""], ["found" => false]],

            // Provar final diferent
            [ ["description" => "VOL. 2A", "public_note" => ""], ["found" => false]]
        ];
    }
}
