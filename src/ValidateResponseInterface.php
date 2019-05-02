<?php

namespace alexeevdv\yii\zerobounce;

use DateTimeInterface;

interface ValidateResponseInterface
{
    const STATUS_VALID = 'valid';

    /**
     * Returns validation status
     * @return string
     */
    public function getStatus(): string;

    /**
     * Returns whether or not status equals `valid`
     */
    public function isValid(): bool;

    /**
     * Returns validation sub status
     */
    public function getSubStatus(): string;

    /**
     * Returns whether or not email comes from a free provider
     */
    public function isFreeEmail(): bool;

    /**
     * Returns whether or not domain have an MX record
     */
    public function isMxFound(): bool;

    /**
     * Returns time email was validated
     */
    public function getProcessedAt(): DateTimeInterface;

    /**
     * Returns raw response fields array
     */
    public function raw(): array;
}
