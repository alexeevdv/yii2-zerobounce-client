<?php

namespace alexeevdv\yii\zerobounce;

use yii\helpers\ArrayHelper;
use yii\httpclient\Request;

/**
 * Class BulkClient
 * @package alexeevdv\yii\zerobounce
 */
class BulkClient extends BaseClient implements BulkClientInterface
{
    /**
     * @inheritDoc
     */
    public $baseUrl = 'https://bulkapi.zerobounce.net';

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
        $request = $this
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
        $request = $this
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
        $request = $this
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
}
