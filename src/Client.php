<?php

namespace alexeevdv\yii\zerobounce;

use yii\helpers\ArrayHelper;
use yii\httpclient\Request;

class Client extends BaseClient implements ClientInterface
{
    /**
     * @inheritDoc
     */
    public $baseUrl = 'https://api.zerobounce.net';

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
}
