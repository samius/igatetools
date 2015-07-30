<?php
namespace Igate\Exception;
class NotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = 'not found';
}