<?php
namespace Be;

/**
 * 异常
 */
class Exception extends \Exception
{

    private $redirectUrl = null;

    public function __construct($message = "", $code = 0, $redirectUrl = null)
    {
        $this->redirectUrl = $redirectUrl;

        parent::__construct($message, $code);
    }

    public function getRedirectUrl() {
        return $this->redirectUrl;
    }
}
