<?php
namespace Igate;

/**
 * Module which calculates with VAT rates (DPH in czech)
 * Doesn`t use VAT rate history, contains only actual czech VAT rates.
 *
 */
class Vat
{
    const TARIFF_HIGH = 'high';
    const TARIFF_LOW = 'low';

    const RATIO_HIGH = 1.21;
    const RATIO_LOW = 1.15;

    /**
     * @param $price
     * @param string $tariff
     * @return mixed
     */
    public static function getPriceWithVat($price, $tariff=self::TARIFF_HIGH)
    {
        return $price * self::getRatio($tariff);
    }

    /**
     * @param $price
     * @param string $tariff
     * @return float
     */
    public static function getPriceWithoutVat($price, $tariff=self::TARIFF_HIGH)
    {
        return $price / self::getRatio($tariff);
    }

    /**
     * Returns VAT value in percent.. i.e. 1.21=>21%
     * @param string $tariff
     * @return int
     */
    public static function getPercentVat($tariff = self::TARIFF_HIGH)
    {
        return 100 * self::getRatio($tariff) - 100;
    }


    /**
     * @param $tariff
     * @return float
     */
    private static function getRatio($tariff)
    {
        if ($tariff == self::TARIFF_HIGH) {
            return self::RATIO_HIGH;
        } else {
            return self::RATIO_LOW;
        }
    }

}
