<?php

namespace alexeevdv\yii\zerobounce;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
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
     * @throws NotAuthorizedException
     * @throws TransportException
     */
    public function validate(string $email, string $ip = ''): ValidateResponseInterface
    {
        $request = $this
            ->createApiRequest()
            ->setMethod('GET')
            ->setUrl([
                'v2/validate',
                'email' => $email,
                'api_key' => $this->apiKey,
                'ip_address' => $ip,
            ])
        ;

        $response = $this->sendApiRequest($request);

        if ($response->getStatusCode() !== '200') {
            throw new BadResponseException($response, 'Failed to validate email.');
        }

        $error = ArrayHelper::getValue($response->getData(), 'error');
        if ($error) {
            throw new NotAuthorizedException($error);
        }

        return new ValidateResponse($response->getData());
    }

    /**
     * @throws BadResponseException
     * @throws NotAuthorizedException
     * @throws TransportException
     */
    public function getCredits(): int
    {
        $request = $this
            ->createApiRequest()
            ->setMethod('GET')
            ->setUrl([
                'v2/getcredits',
                'api_key' => $this->apiKey,
            ])
        ;

        $response = $this->sendApiRequest($request);

        if ($response->getStatusCode() === '400') {
            throw new NotAuthorizedException($response->getContent());
        }

        $credits = ArrayHelper::getValue($response->getData(), 'Credits');
        if ($response->getStatusCode() !== '200' || $credits === null) {
            throw new BadResponseException($response, 'Failed to get credits.');
        }

        return (int) $credits;
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
