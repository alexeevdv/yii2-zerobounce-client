<?php

namespace tests\unit;

use alexeevdv\yii\zerobounce\BadResponseException;
use alexeevdv\yii\zerobounce\Client;
use alexeevdv\yii\zerobounce\NotAuthorizedException;
use alexeevdv\yii\zerobounce\TransportException;
use Codeception\Test\Unit;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception as HttpClientException;
use yii\httpclient\Request;
use yii\httpclient\Response;

class ClientTest extends Unit
{
    public function testApiKeyIsEnsured()
    {
        $this->expectException(InvalidConfigException::class);
        new Client;
    }

    public function testSuccessfulInstantiation()
    {
        new Client([
            'apiKey' => 'a95c530a7af5f492a74499e70578d150',
        ]);
    }

    public function testValidateOnValidEmail()
    {
        /** @var Client $client */
        $client = $this->make(Client::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '200',
                'getData' => [
                    'status' => 'valid',
                ],
            ])
        ]);
        $this->assertTrue($client->validate('valid@example.com')->isValid());
    }

    public function testValidateOnInvalidEmail()
    {
        /** @var Client $client */
        $client = $this->make(Client::class, [
            'createApiRequest' => $this->make(Request::class, [
                'send' => $this->make(Response::class, [
                    'getStatusCode' => '200',
                    'getData' => [
                        'status' => 'invalid',
                    ],
                ]),
            ])
        ]);
        $this->assertFalse($client->validate('invalid@example.com')->isValid());
    }

    public function testValidateWithTransportError()
    {
        /** @var Client $client */
        $client = $this->make(Client::class, [
            'createApiRequest' => $this->make(Request::class, [
                'send' => function () {
                    throw new HttpClientException;
                },
            ])
        ]);
        $this->expectException(TransportException::class);
        $client->validate('does-not-matter@example.com');
    }

    public function testValidateWithBadResponse()
    {
        /** @var Client $client */
        $client = $this->make(Client::class, [
            'createApiRequest' => $this->make(Request::class, [
                'send' => $this->make(Response::class, [
                    'getStatusCode' => '500',
                ]),
            ])
        ]);
        $this->expectException(BadResponseException::class);
        $client->validate('does-not-matter@example.com');
    }

    public function testValidateWithWrongCredentials()
    {
        $client = $this->make(Client::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '200',
                'getData' => [
                    'error' => 'Invalid API Key or your account ran out of credits',
                ],
            ])
        ]);
        $this->expectException(NotAuthorizedException::class);
        $client->validate('does-not-matter@example.com');
    }

    public function testGetCreditsSuccessful()
    {
        /** @var Client $client */
        $client = $this->make(Client::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '200',
                'getData' => [
                    'Credits' => '1234'
                ],
            ]),
        ]);
        $this->assertEquals(1234, $client->getCredits());
    }

    public function testGetCreditsFailed()
    {
        /** @var Client $client */
        $client = $this->make(Client::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '500',
            ]),
        ]);
        $this->expectException(BadResponseException::class);
        $client->getCredits();
    }

    public function testGetCreditsWithWrongCredentials()
    {
        /** @var Client $client */
        $client = $this->make(Client::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '400',
                'getData' => 'Bad Request',
            ]),
        ]);
        $this->expectException(NotAuthorizedException::class);
        $client->getCredits();
    }

    public function testSendFileSuccessful()
    {
        $expectedData = [
            'success' => true,
            'message' => 'File Accepted',
            'file_name' => '1565706204.csv',
            'file_id' => '03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf'
        ];
        $client = $this->make(Client::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '200',
                'getData' => $expectedData,
            ]),
            'getBulkApiClient' => $this->make(Client::class, [
                'createApiRequest' => $this->make(Request::class, [
                    'addFile' => function () {
                        return $this->make(Request::class);
                    }
                ]),
            ])
        ]);
        $this->assertEquals($expectedData, $client->sendFile('file.csv', 'http://google.com'));
    }

    public function testSendFileAuthIssues()
    {
        $expectedData = [
            'success' => true,
            'message' => 'File Accepted',
            'file_name' => '1565706204.csv',
            'file_id' => '03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf'
        ];
        $client = $this->make(Client::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '400',
                'getData' => 'Bad Request',
            ]),
            'getBulkApiClient' => $this->make(Client::class, [
                'createApiRequest' => $this->make(Request::class, [
                    'addFile' => function () {
                        return $this->make(Request::class);
                    }
                ]),
            ])
        ]);
        $this->expectException(NotAuthorizedException::class);
        $client->sendFile('file.csv', 'http://google.com');
    }

    public function testSendFileFailed()
    {
        $expectedData = [
            'success' => true,
            'message' => 'File Accepted',
            'file_name' => '1565706204.csv',
            'file_id' => '03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf'
        ];
        $client = $this->make(Client::class, [
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '500',
            ]),
            'getBulkApiClient' => $this->make(Client::class, [
                'createApiRequest' => $this->make(Request::class, [
                    'addFile' => function () {
                        return $this->make(Request::class);
                    }
                ]),
            ])
        ]);
        $this->expectException(BadResponseException::class);
        $client->sendFile('file.csv', 'http://google.com');
    }

    public function testReadFileSuccessful()
    {
        /** @var Client $client */
        $client = $this->make(Client::class, [
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
        /** @var Client $client */
        $client = $this->make(Client::class, [
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
        /** @var Client $client */
        $client = $this->make(Client::class, [
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
        /** @var Client $client */
        $client = $this->make(Client::class, [
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
        /** @var Client $client */
        $client = $this->make(Client::class, [
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
        /** @var Client $client */
        $client = $this->make(Client::class, [
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
        /** @var Client $client */
        $client = $this->make(Client::class, [
            'apiKey' => 'a549f91e-95a1-4763-9571-d96e56a0b7e5',
            'sendApiRequest' => $this->make(Response::class, [
                'getStatusCode' => '500',
            ]),
        ]);
        $this->assertEquals(false, $client->deleteFile('03e6a6d4-9b1f-438c-b6d5-cfa9c2f2dddf'));
    }
}
