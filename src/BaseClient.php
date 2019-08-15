<?php

namespace alexeevdv\yii\zerobounce;

use yii\base\InvalidConfigException;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Exception as HttpClientException;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class BaseClient
 * @package alexeevdv\yii\zerobounce
 */
abstract class BaseClient extends HttpClient
{
    /**
     * @var string
     */
    public $apiKey;

    /**
     * API response timeout in seconds
     * @var int
     */
    public $timeout = 10;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->apiKey === null) {
            throw new InvalidConfigException('`apiKey` is required.');
        }
    }

    /**
     * @throws TransportException
     */
    protected function sendApiRequest(Request $request): Response
    {
        try {
            $response = $request->send();
            $response->getStatusCode();
        } catch (HttpClientException $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
        return $response;
    }

    /**
     * @return Request
     */
    protected function createApiRequest(): Request
    {
        return $this
            ->createRequest()
            ->setOptions([
                'timeout' => $this->timeout,
            ])
        ;
    }
}
