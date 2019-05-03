<?php

namespace tests\unit;

use alexeevdv\yii\zerobounce\ValidateResponse;
use Codeception\Test\Unit;

class ValidateResponseTest extends Unit
{
    public function testInstantiation()
    {
        $rawFields = [
            'address' => 'flowerjill@aol.com',
            'status' => 'valid',
            'sub_status' => '',
            'free_email' => true,
            'did_you_mean' => null,
            'account' => 'flowerjill',
            'domain' => 'aol.com',
            'domain_age_days' => '8426',
            'smtp_provider' => 'yahoo',
            'mx_record' => 'mx-aol.mail.gm0.yahoodns.net',
            'mx_found' => true,
            'firstname' => 'Jill',
            'lastname' => 'Stein',
            'gender' => 'female',
            'country' => 'United States',
            'region' => 'Florida',
            'city' => 'West Palm Beach',
            'zipcode' => '33401',
            'processed_at' => '2017-04-01 02:48:02.592'
        ];
        $response = new ValidateResponse($rawFields);

        $this->assertEquals($rawFields, $response->raw());
        $this->assertEquals('2017-04-01 02:48:02', $response->getProcessedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('valid', $response->getStatus());
        $this->assertEquals('', $response->getSubStatus());
        $this->assertTrue($response->isValid());
        $this->assertFalse($response->isInvalid());
        $this->assertFalse($response->isCatchAll());
        $this->assertFalse($response->isSpamTrap());
        $this->assertFalse($response->isAbuse());
        $this->assertFalse($response->isDoNotMail());
        $this->assertFalse($response->isUnknown());
        $this->assertTrue($response->isMxFound());
        $this->assertTrue($response->isFreeEmail());
        $this->assertEquals('mx-aol.mail.gm0.yahoodns.net', $response->getMxRecord());
        $this->assertEquals('Jill', $response->getFirstName());
        $this->assertEquals('Stein', $response->getLastName());
        $this->assertEquals(ValidateResponse::GENDER_FEMALE, $response->getGender());
    }

    public function testIsInvalid()
    {
        $rawFields = [
            'status' => 'invalid',
        ];
        $response = new ValidateResponse($rawFields);
        $this->assertTrue($response->isInvalid());
    }

    public function testIsCatchAll()
    {
        $rawFields = [
            'status' => 'catch-all',
        ];
        $response = new ValidateResponse($rawFields);
        $this->assertTrue($response->isCatchAll());
    }

    public function testIsSpamTrap()
    {
        $rawFields = [
            'status' => 'spamtrap',
        ];
        $response = new ValidateResponse($rawFields);
        $this->assertTrue($response->isSpamTrap());
    }

    public function testIsAbuse()
    {
        $rawFields = [
            'status' => 'abuse',
        ];
        $response = new ValidateResponse($rawFields);
        $this->assertTrue($response->isAbuse());
    }

    public function isDoNotMail()
    {
        $rawFields = [
            'status' => 'do_not_mail',
        ];
        $response = new ValidateResponse($rawFields);
        $this->assertTrue($response->isDoNotMail());
    }

    public function isUnknown()
    {
        $rawFields = [
            'status' => 'do_not_mail',
        ];
        $response = new ValidateResponse($rawFields);
        $this->assertTrue($response->isUnknown());
    }
}
