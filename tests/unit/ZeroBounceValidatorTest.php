<?php

namespace tests\unit;

use alexeevdv\yii\zerobounce\BadResponseException;
use alexeevdv\yii\zerobounce\Client;
use alexeevdv\yii\zerobounce\TransportException;
use alexeevdv\yii\zerobounce\ValidateResponse;
use alexeevdv\yii\zerobounce\ZeroBounceValidator;
use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Yii;
use yii\console\Request as ConsoleRequest;
use yii\httpclient\Response;
use yii\web\Request;

class ZeroBounceValidatorTest extends Unit
{
    /**
     * @dataProvider validResponses
     */
    public function testValidEmail($response)
    {
        $validator = new ZeroBounceValidator([
            'request' => $this->make(ConsoleRequest::class),
            'client' => $this->make(Client::class, [
                'validate' => Expected::once(function ($value, $ip) use ($response) {
                    $this->assertEquals('test@test.com', $value);

                    return new ValidateResponse([
                        'status' => $response
                    ]);
                })
            ])
        ]);

        $this->assertTrue($validator->validate('test@test.com'));
    }

    public function validResponses()
    {
        return [
            'valid' => ['valid'],
            'unknown' => ['unknown'],
        ];
    }

    /**
     * @dataProvider invalidResponses
     */
    public function testInalidEmail($response)
    {
        $validator = new ZeroBounceValidator([
            'request' => $this->make(ConsoleRequest::class),
            'client' => $this->make(Client::class, [
                'validate' => Expected::once(function ($value, $ip) use ($response) {
                    $this->assertEquals('test@test.com', $value);
                    $this->assertEmpty($ip);

                    return new ValidateResponse([
                        'status' => $response
                    ]);
                })
            ])
        ]);

        $this->assertFalse($validator->validate('test@test.com'));
    }

    public function invalidResponses()
    {
        return [
            'invalid' => ['invalid'],
            'catch-all' => ['catch-all'],
            'spamtrap' => ['spamtrap'],
            'abuse' => ['abuse'],
            'do_not_mail' => ['do_not_mail'],
        ];
    }

    public function testExtractedIpAddress()
    {
        $validator = new ZeroBounceValidator([
            'request' => $this->make(Request::class, [
                'getUserIP' => Expected::once('127.0.0.1')
            ]),
            'client' => $this->make(Client::class, [
                'validate' => Expected::once(function ($value, $ip) {
                    $this->assertEquals('test@test.com', $value);
                    $this->assertEquals('127.0.0.1', $ip);

                    return new ValidateResponse([
                        'status' => 'valid'
                    ]);
                })
            ])
        ]);

        $this->assertTrue($validator->validate('test@test.com'));
    }

    public function testNotWebRequest()
    {
        $validator = new ZeroBounceValidator([
            'request' => $this->make(ConsoleRequest::class),
            'client' => $this->make(Client::class, [
                'validate' => Expected::once(function ($value, $ip) {
                    $this->assertEquals('test@test.com', $value);
                    $this->assertEmpty($ip);

                    return new ValidateResponse([
                        'status' => 'valid'
                    ]);
                })
            ])
        ]);

        $this->assertTrue($validator->validate('test@test.com'));
    }

    public function testWithIpGetter()
    {
        $validator = new ZeroBounceValidator([
            'ipGetter' => function () {
                return '127.0.0.1';
            },
            'request' => $this->make(ConsoleRequest::class),
            'client' => $this->make(Client::class, [
                'validate' => Expected::once(function ($value, $ip) {
                    $this->assertEquals('test@test.com', $value);
                    $this->assertEquals('127.0.0.1', $ip);

                    return new ValidateResponse([
                        'status' => 'valid'
                    ]);
                })
            ])
        ]);

        $this->assertTrue($validator->validate('test@test.com'));
    }

    public function testValidateThrowsTransportException()
    {
        $validator = new ZeroBounceValidator([
            'request' => $this->make(ConsoleRequest::class),
            'client' => $this->make(Client::class, [
                'validate' => Expected::once(function () {
                    throw new TransportException();
                }
            )])
        ]);

        $this->assertTrue($validator->validate('test@test.com'));
    }

    public function testValidateThrowsBadResponseException()
    {
        $validator = new ZeroBounceValidator([
            'request' => $this->make(ConsoleRequest::class),
            'client' => $this->make(Client::class, [
                'validate' => Expected::once(function () {
                    throw new BadResponseException(new Response());
                }
            )])
        ]);

        $this->assertTrue($validator->validate('test@test.com'));
    }
}
