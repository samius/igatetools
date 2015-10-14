<?php
namespace Igate\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{
    const FLASH_MESSAGE = 'message',
        FLASH_WARNING = 'warning',
        FLASH_ERROR = 'error';

    /**
     * @param $message
     */
    public function addMessage($message)
    {
        $this->addFlash(self::FLASH_MESSAGE, $message);
    }

    /**
     * @param $message
     */
    public function addWarning($message)
    {
        $this->addFlash(self::FLASH_WARNING, $message);
    }

    /**
     * @param $message
     */
    public function addError($message)
    {
        $this->addFlash(self::FLASH_ERROR, $message);
    }
}
