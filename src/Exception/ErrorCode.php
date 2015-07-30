<?php
namespace Igate\Exception;

/**
 * Vyjimka nesouci nas chybovy kod, popr. hodnotu, ktera chybu zpusobila.
 * @package Igate\Exception
 */
abstract class ErrorCode extends \Exception
{

//ve zdedene tride se budou nastavovat konstanty chybovych kodu, napr.
//    const BAD_REQUEST   = 1001,
//          UNKNOWN_LANG  = 1002,


    /**
     * @var string
     */
    private $errorValue;


    /**
     * @param int $code
     * @param string $value
     * @param \Exception $previous
     */
    public function __construct($code, $value='', $previous=null)
    {
        parent::__construct($code.":".$value, $code, $previous);
        $this->errorValue = (string) $value;
    }

    /**
     * @return string
     */
    public function getErrorValue()
    {
        return $this->errorValue;
    }

    /**
     * Seznam kodu, ktere jsou povazovany za fatalni vyjimku
     * V potomcich lze prepsat
     * @return array
     */
    protected function getFatalCodes()
    {
        return array();
    }

    /**
     * Rozhodne, zda je vyjimka fatalni (prestoze je zachycena, mela by se logovat)
     * @return bool
     */
    public function isFatal()
    {
        return in_array($this->getCode(), $this->getFatalCodes());
    }

}
