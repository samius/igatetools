<?php
namespace Igate;


/**
 * Helper class for barcode logic
 */
class Barcode
{
    const SSCC_LENGTH = 17;


    const PREFIX_TRACKITO_TECHNOLOGY_CZ_GS1= '418149'; //international barcode prefix for company
    const PREFIX_CZECH_REPUBLIC = '859'; //international code for CZ

    const DATE_FORMAT = 'ymd'; //format of date for barcodes

    const
        CODE_SSCC = '00', //prefix of SSCC in barcode number (in label in braces)
        CODE_DATE = '11',   //prefix of date of production in barcode number (in label in braces)
        CODE_SERIAL = '21'; //prefix of serial number in barcode number (in label in braces)

    /**
     * @see http://www.gs1sy.org/GS1System/id_keys/CheckDigit.htm
     * @param $sscc
     * @return int
     */
    public static function getSsccCheckDigit($sscc)
    {
        $strlen = strlen($sscc);
        if ($strlen !== 17) {
            throw new \InvalidArgumentException("SSCC length should be 17 numbers, $strlen given");
        }
        $sscc = (string)$sscc;
        $checksum = 0;
        $multipliers = array(1,3);
        $actualMultiplier = $multipliers[1]; //starting with 3
        for ($i = 0; $i < 17; $i++) {
            $checksum += $sscc[$i] * $actualMultiplier;
            if ($actualMultiplier == $multipliers[0]) {
                $actualMultiplier = $multipliers[1];
            } else {
                $actualMultiplier = $multipliers[0];
            }

        }
        return 10 - ($checksum % 10);
    }
}
