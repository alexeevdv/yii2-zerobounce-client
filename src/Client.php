<?php

namespace alexeevdv\yii\zerobounce;

use yii\base\InvalidConfigException;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Exception as HttpClientException;
use yii\httpclient\Request;
use yii\httpclient\Response;

class Client extends HttpClient implements ClientInterface
{
    /**
     * @var string
     */
    public $apiKey;

    /**
     * @inheritDoc
     */
    public $baseUrl = 'https://api.zerobounce.net';

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
     * @throws BadResponseException
     * @throws TransportException
     */
    public function validate(string $email, string $ip = ''): ValidateResponseInterface
    {
        $request = $this
            ->createApiRequest()
            ->setMethod('GET')
            ->setUrl([
                '/v2/validate',
                'email' => $email,
                'api_key' => $this->apiKey,
                'ip_address' => $ip,
            ])
        ;

        $response = $this->sendApiRequest($request);

        if ($response->getStatusCode() !== '200') {
            throw new BadResponseException($response, 'Failed to validate email.');
        }

        return new ValidateResponse($response->getData());
    }

    protected function createApiRequest(): Request
    {
        return $this
            ->createRequest()
            ->setOptions([
                'timeout' => $this->timeout,
            ])
        ;
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
}
