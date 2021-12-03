<?php

namespace Be;

/**
 * 异常
 */
class Exception extends \Exception
{

    private $redirect = null;

    /**
     * Exception constructor.
     *
     * @param string $message 异常信息
     * @param int $code 异常码
     * @param array|null $redirect 跳转信息
     */
    public function __construct($message = "", $code = 0, array $redirect = null)
    {
        if ($redirect !== null) {
            $this->redirect = [
                'url' => $redirect['url'] ?? '',
                'message' => $redirect['message'] ?? '',
                'timeout' => $redirect['timeout'] ?? 3,
            ];
        }

        parent::__construct($message, $code);
    }

    /**
     * 跳转信息
     * @return array
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * 跳转网址
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirect === null ? '' : $this->redirect['url'];
    }

    /**
     * 跳转消息
     *
     * @return string
     */
    public function getRedirectMessage()
    {
        return $this->redirect === null ? '' : $this->redirect['message'];
    }

    /**
     * 跳转倒计时
     *
     * @return int
     */
    public function getRedirectTimeout()
    {
        return $this->redirect === null ? '' : $this->redirect['timeout'];
    }

}
