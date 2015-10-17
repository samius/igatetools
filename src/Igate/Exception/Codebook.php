<?php
namespace Igate\Exception;

use Symfony\Component\Translation\Translator;

/**
 * Support class for Exception\ErrorCode
 * Provides localized messages for each error code. Messages can have Title and Content (content is optional)
 * If translation contains %v%, error value is included in the message
 */
abstract class Codebook
{
    /**
     * @var Translator
     */
    protected  $t;

    /**
     * @var array
     */
    protected  $messages = array();

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->t = $translator;
        $this->setMessages();
    }

    /**
     * Set messages for all exception codes
     *  $this->messages = array(
     *     ErrorCode::BAD_REQUEST => array('title'=>$this->t->trans("Request is missing these params: %v%")),
     * )
     */
    public abstract function setMessages();

    /**
     * @param ErrorCode $e
     * @return string message for whole exception. For example in flash messages
     */
    public function getExceptionMessage(ErrorCode $e)
    {
        return $this->getMessageTitle($e->getCode(), $e->getErrorValue());
    }

    /**
     * @param $code
     * @param string $value
     * @return string
     */
    public function getMessageTitle($code, $value = '')
    {
        $this->assertCode($code);
        $message = str_replace('%v%', "'$value'", $this->messages[$code]['title']);

        return $message;
    }

    /**
     * @param $code
     * @param string $value
     * @return string
     */
    public function getMessageContent($code, $value = '')
    {
        $this->assertCode($code);
        if (isset($this->messages[$code]['content'])) {
            $value = htmlspecialchars($value);
            $message = str_replace('%v%', "'$value'", $this->messages[$code]['content']);
        } else {
            $message = '';
        }

        return $message;
    }

    /**
     * @return string actual locale of translator
     */
    public function getLang()
    {
        return $this->t->getLocale();
    }

    /**
     * Checks, if that code exists in defined messages
     * @param $code
     */
    private function assertCode($code)
    {
        if (!isset($this->messages[$code])) {
            throw new \LogicException("Error message for code '$code' is not defined");
        }
    }
}
