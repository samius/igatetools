<?php
namespace Igate\Text;

/**
 * Trida pro praci s nahrazovanim retezcu v textu
 */
class Replacer 
{

    /**
     * V textu nahradi cele pole ve formatu array('co najit' => 'cim nahradit')
     * @param $string
     * @param array $replacements
     * @return string
     */
    public static function replaceArray($string, array $replacements, $escape=true)
    {
        foreach ($replacements as $search => $replace) {
            if ($escape) {
                $replace = htmlspecialchars($replace);
            }

            $string = str_replace($search, $replace, $string);
        }
        return $string;
    }
}
 