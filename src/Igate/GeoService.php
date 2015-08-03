<?php
namespace Igate;

/**
 * Poskytuje sluzby spojene s geolokaci
 * Ma omezeni 10 000 dotazu za hodinu. Nad tento limit je treba rozbehnout si vlastni instanci
 */
class GeoService
{
    public static $geoipUrl = 'http://freegeoip.net/json/';

    public function getGeoinformationByIpAddress($ipAddress)
    {
        $url = self::$geoipUrl . $ipAddress;
        if (!function_exists('curl_init')) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output);
    }
    
    /**
     * @param $ipAddress
     * @return array|mixed
     */
    public function getTimezoneByIpAddress($ipAddress)
    {
        $res = $this->getGeoinformationByIpAddress($ipAddress);
        if ($res && isset($res->time_zone)) {
            return $res->time_zone;
        } else {
            return false;
        }


    }
}
 