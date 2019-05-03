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

    public function isInvalid(): bool
    {
        return $this->getStatus() === self::STATUS_INVALID;
    }

    public function isCatchAll(): bool
    {
        return $this->getStatus() === self::STATUS_CATCH_ALL;
    }

    public function isSpamTrap(): bool
    {
        return $this->getStatus() === self::STATUS_SPAM_TRAP;
    }

    public function isAbuse(): bool
    {
        return $this->getStatus() === self::STATUS_ABUSE;
    }

    public function isDoNotMail(): bool
    {
        return $this->getStatus() === self::STATUS_DO_NOT_MAIL;
    }

    public function isUnknown(): bool
    {
        return $this->getStatus() === self::STATUS_UNKNOWN;
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

    public function getMxRecord(): ?string
    {
        return ArrayHelper::getValue($this->_rawFields, 'mx_record');
    }

    public function getProcessedAt(): DateTimeInterface
    {
        $dateString = ArrayHelper::getValue($this->_rawFields, 'processed_at');
        return new DateTime($dateString, new DateTimeZone('UTC'));
    }

    public function getFirstName(): ?string
    {
        return ArrayHelper::getValue($this->_rawFields, 'firstname');
    }

    public function getLastName(): ?string
    {
        return ArrayHelper::getValue($this->_rawFields, 'lastname');
    }

    public function getGender(): ?string
    {
        return ArrayHelper::getValue($this->_rawFields, 'gender');
    }

    public function raw(): array
    {
        return $this->_rawFields;
    }
}
