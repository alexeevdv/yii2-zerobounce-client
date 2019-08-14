<?php


namespace tests\unit;

use alexeevdv\yii\zerobounce\BadResponseException;
use alexeevdv\yii\zerobounce\BulkClient;
use alexeevdv\yii\zerobounce\NotAuthorizedException;
use Codeception\Test\Unit;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class BulkClientTest
 * @package tests\unit
 */
class BulkClientTest extends Unit
{
    public function testSendFileSuccessful()
    {
        $expectedData = [
            'success' => true,
            'message' => 'File Accepted',
            'file_name' => '1565706204.csv',
            'file_id' => '03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf'
        ];
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '200',
                'getData' => $expectedData,
            ]),
            'createApiRequest' => $this->make(Request::class, [
                'addFile' => function () {
                    return $this->make(Request::class);
                }
            ]),
        ]);
        $this->assertEquals($expectedData, $client->sendFile('file.csv', 'http://google.com'));
    }

    public function testSendFileAuthIssues()
    {
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '400',
                'getData' => 'Bad Request',
            ]),
            'createApiRequest' => $this->make(Request::class, [
                'addFile' => function () {
                    return $this->make(Request::class);
                }
            ]),
        ]);
        $this->expectException(NotAuthorizedException::class);
        $client->sendFile('file.csv', 'http://google.com');
    }

    public function testSendFileFailed()
    {
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '500',
            ]),
            'createApiRequest' => $this->make(Request::class, [
                'addFile' => function () {
                    return $this->make(Request::class);
                }
            ]),
        ]);
        $this->expectException(BadResponseException::class);
        $client->sendFile('file.csv', 'http://google.com');
    }

    public function testReadFileSuccessful()
    {
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'apiKey' => 'a549f91e-95a1-4763-9571-d96e56a0b7e5',
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '200',
                'getContent' => 'expected data',
            ]),
        ]);
        $this->assertEquals('expected data', $client->readFile('03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf'));
    }

    public function testReadFileAuthIssues()
    {
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'apiKey' => 'a549f91e-95a1-4763-9571-d96e56a0b7e5',
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '400',
                'getData' => 'Bad Request',
            ]),
        ]);
        $this->expectException(NotAuthorizedException::class);
        $client->readFile('03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf');
    }

    public function testReadFileFailed()
    {
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'apiKey' => 'a549f91e-95a1-4763-9571-d96e56a0b7e5',
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '500',
            ]),
        ]);
        $this->expectException(BadResponseException::class);
        $client->readFile('03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf');
    }

    public function testDeleteFileSuccessful()
    {
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'apiKey' => 'a549f91e-95a1-4763-9571-d96e56a0b7e5',
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '200',
                'getData' => [
                    'success' => true
                ],
            ]),
        ]);
        $this->assertEquals(true, $client->deleteFile('03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf'));
    }

    public function testDeleteFileNotSuccessful()
    {
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'apiKey' => 'a549f91e-95a1-4763-9571-d96e56a0b7e5',
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '200',
                'getData' => [
                    'success' => false
                ],
            ]),
        ]);
        $this->assertEquals(false, $client->deleteFile('03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf'));
    }

    public function testDeleteFileAuthIssues()
    {
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'apiKey' => 'a549f91e-95a1-4763-9571-d96e56a0b7e5',
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '400',
                'getData' => 'Bad Request',
            ]),
        ]);
        $this->expectException(NotAuthorizedException::class);
        $client->deleteFile('03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf');
    }

    public function testDeleteFileFailed()
    {
        /** @var BulkClient $client */
        $client = $this->make(BulkClient::class, [
            'apiKey' => 'a549f91e-95a1-4763-9571-d96e56a0b7e5',
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '500',
            ]),
        ]);
        $this->assertEquals(false, $client->deleteFile('03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf'));
    }
}
