<?php

namespace tests\unit;

use alexeevdv\yii\zerobounce\BadResponseException;
use Codeception\Test\Unit;
use yii\httpclient\Response;

class BadResponseExceptionTest extends Unit
{
    public function testGetResponse()
    {
        $response = $this->makeEmpty(Response::class);
        $exception = new BadResponseException($response);
        $this->assertInstanceOf(Response::class, $exception->getResponse());
    }
}
