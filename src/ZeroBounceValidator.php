<?php

namespace alexeevdv\yii\zerobounce;

use Yii;
use yii\di\Instance;
use yii\validators\Validator;
use yii\web\Request;

class ZeroBounceValidator extends Validator
{
    /**
     * @var Client|string|array
     */
    public $client = 'zerobounceClient';

    /**
     * Callable that returns client IP address
     *
     * @var    callable|null
     * @return string|null Clients IP address
     */
    public $ipGetter;

    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }

        $this->client = Instance::ensure($this->client, Client::class);
    }

    protected function validateValue($value)
    {
        $ip = null;

        if (Yii::$app->request instanceof Request) {
            $ip = Yii::$app->request->getUserIP();
        }

        if (is_callable($this->ipGetter)) {
            $ip = call_user_func($this->ipGetter);
        }

        if (!$ip) {
            $ip = '';
        }
        try {
            $response = $this->client->validate($value, $ip);

            if (!$response->isValid() && !$response->isUnknown()) {
                return [$this->message, []];
            }
        } catch (TransportException $th) {
        } catch (BadResponseException $th) {
        }

        return null;
    }
}
