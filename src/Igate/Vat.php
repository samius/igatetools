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
     * @param string $charge
     * @return mixed
     */
    public static function getPriceWithVat($price, $charge=self::TARIFF_HIGH)
    {
        return $price * self::getRatio($charge);
    }

    /**
     * @param $price
     * @param string $charge
     * @return float
     */
    public static function getPriceWithoutVat($price, $charge=self::TARIFF_HIGH)
    {
        return $price / self::getRatio($charge);
    }

    /**
     * @param $charge
     * @return float
     */
    private static function getRatio($charge)
    {
        if ($charge == self::TARIFF_HIGH) {
            return self::RATIO_HIGH;
        } else {
            return self::RATIO_LOW;
        }
    }

}
