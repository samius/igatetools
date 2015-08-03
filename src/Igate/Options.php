<?php
namespace Igate;
/**
 * Trida pro rychle naplneni objektu daty
 *
 * @author milan
 * @category Igate
 * @package Options
 * @version $Id$
 */
class Options
{
    /**
     * Pokud je ve vstupnim poli napr . array('xxx'=>'value'), pokusi se zavolat $object->setXxx($value);
     * @param StdClass $object
     * @param array $options
     * @return mixed
     */
    public static function setOptions($object, array $options)
    {
        if (!is_object($object)) {
            return;
        }
        foreach ($options as $key => $value) {
            $method = 'set' . self::normalizeKey($key);
            if (method_exists($object, $method)) {
                $object->$method($value);
            }
        }

        return $object;
    }


    /**
     * @param StdClass $object
     * @param array|Zend_Config $options
     *
     * @return mixed
     */
    public static function setConstructorOptions($object, $options)
    {
        if (is_array($options)) {
            self::setOptions($object, $options);
        }

        return $object;
    }


    /**
     * @param string $key Underscore or dash key.
     * @return string CamelCase key.
     */
    public static function normalizeKey($key)
    {
        $search = array('_', '-');
        $replace = array(' ', ' ');
        $option = str_replace($search, $replace, strtolower($key));
        $option = str_replace(' ', '', ucwords($option));
        return $option;
    }

    /**
     * Budou ponechany pouze ty hodnoty z $options, ktere maji klice v $allowedKeys
     * @param array $options
     * @param array $allowedKeys
     * @return $array
     */
    public static function filterArray(array $options, array $allowedKeys)
    {
        return array_intersect_key($options, array_flip($allowedKeys));
    }
}
