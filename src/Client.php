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
     * @var string
     */
    public $bulkApiBaseUrl = 'https://bulkapi.zerobounce.net';

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

    /**
     * @param string $fileName
     * @param string $redirectUrl
     * @return array
     * @throws BadResponseException
     * @throws NotAuthorizedException
     * @throws TransportException
     */
    public function sendFile(string $fileName, string $redirectUrl): array
    {
        $request = $this->getBulkApiClient()
            ->createApiRequest()
            ->setMethod('POST')
            ->setUrl('v2/sendfile')
            ->addData([
                'api_key' => $this->apiKey,
                'email_address_column' => 1,
                'return_url' => $redirectUrl
            ])
            ->addFile('file', $fileName)
        ;
        $response = $this->sendApiRequest($request);

        if ($response->getStatusCode() === '400') {
            throw new NotAuthorizedException($response->getContent());
        }

        if ($response->getStatusCode() !== '200' || !ArrayHelper::getValue($response->getData(), 'success')) {
            throw new BadResponseException($response, 'Failed to load .csv file');
        }

        return $response->getData();
    }

    /**
     * @param string $fileId
     * @return string
     * @throws BadResponseException
     * @throws NotAuthorizedException
     * @throws TransportException
     */
    public function readFile(string $fileId): string
    {
        $request = $this->getBulkApiClient()
            ->createApiRequest()
            ->setMethod('GET')
            ->setUrl([
                'v2/getfile',
                'api_key' => $this->apiKey,
                'file_id' => $fileId
            ])
        ;

        $response = $this->sendApiRequest($request);

        if ($response->getStatusCode() === '400') {
            throw new NotAuthorizedException($response->getContent());
        }

        if ($response->getStatusCode() !== '200') {
            throw new BadResponseException($response, 'Failed to read .csv file');
        }
        return $response->getContent();
    }

    /**
     * @param string $fileId
     * @return bool
     * @throws NotAuthorizedException
     * @throws TransportException
     */
    public function deleteFile(string $fileId): bool
    {
        $request = $this->getBulkApiClient()
            ->createApiRequest()
            ->setMethod('GET')
            ->setUrl([
                'v2/deletefile',
                'api_key' => $this->apiKey,
                'file_id' => $fileId
            ])
        ;

        $response = $this->sendApiRequest($request);

        if ($response->getStatusCode() === '400') {
            throw new NotAuthorizedException($response->getContent());
        }

        if ($response->getStatusCode() === '200' && ArrayHelper::getValue($response->getData(), 'success')) {
            return true;
        }
        return false;
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

    /**
     * @return Client
     */
    protected function getBulkApiClient(): self
    {
        return new self([
            'baseUrl' => $this->bulkApiBaseUrl,
            'apiKey' => $this->apiKey
        ]);
    }
}
