<?php

namespace alexeevdv\yii\zerobounce;

use Throwable;
use yii\httpclient\Response;

class BadResponseException extends \Exception implements Exception
{
    /**
     * @var Response
     */
    private $_response;

    public function __construct(Response $response, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->_response = $response;
    }

    public function getResponse(): Response
    {
        return $this->_response;
    }
}
