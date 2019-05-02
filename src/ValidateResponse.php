<?php

namespace alexeevdv\yii\zerobounce;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use yii\helpers\ArrayHelper;

class ValidateResponse implements ValidateResponseInterface
{
    /**
     * @var array
     */
    private $_rawFields;

    public function __construct(array $rawFields)
    {
        $this->_rawFields = $rawFields;
    }

    public function getStatus(): string
    {
        return (string) ArrayHelper::getValue($this->_rawFields, 'status');
    }

    public function isValid(): bool
    {
        return $this->getStatus() === self::STATUS_VALID;
    }

    public function getSubStatus(): string
    {
        return (string) ArrayHelper::getValue($this->_rawFields, 'sub_status');
    }

    public function isFreeEmail(): bool
    {
        return (bool) ArrayHelper::getValue($this->_rawFields, 'free_email');
    }

    public function isMxFound(): bool
    {
        return (bool) ArrayHelper::getValue($this->_rawFields, 'mx_found');
    }

    public function getProcessedAt(): DateTimeInterface
    {
        $dateString = ArrayHelper::getValue($this->_rawFields, 'processed_at');
        return new DateTime($dateString, new DateTimeZone('UTC'));
    }

    public function raw(): array
    {
        return $this->_rawFields;
    }
}
