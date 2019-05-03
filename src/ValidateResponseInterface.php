<?php

namespace alexeevdv\yii\zerobounce;

use DateTimeInterface;

interface ValidateResponseInterface
{
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';
    const STATUS_CATCH_ALL = 'catch-all';
    const STATUS_SPAM_TRAP = 'spamtrap';
    const STATUS_ABUSE = 'abuse';
    const STATUS_DO_NOT_MAIL = 'do_not_mail';
    const STATUS_UNKNOWN = 'unknown';

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    /**
     * Returns validation status
     */
    public function getStatus(): string;

    /**
     * Returns whether or not the status is `valid`
     */
    public function isValid(): bool;

    /**
     * Returns whether or not the status is `invalid`
     */
    public function isInvalid(): bool;

    /**
     * Returns whether or not the status is `catch-all `
     */
    public function isCatchAll(): bool;

    /**
     * Returns whether or not the status is `spamtrap`
     */
    public function isSpamTrap(): bool;

    /**
     * Returns whether or not the status is `abuse`
     */
    public function isAbuse(): bool;

    /**
     * Returns whether or not the status is `do_not_mail`
     */
    public function isDoNotMail(): bool;

    /**
     * Returns whether or not the status is `unknown`
     */
    public function isUnknown(): bool;

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
     * Returns preferred MX record of the domain
     */
    public function getMxRecord(): ?string;

    /**
     * Returns time email was validated
     */
    public function getProcessedAt(): DateTimeInterface;

    /**
     * Returns first name of the email owner
     */
    public function getFirstName(): ?string;

    /**
     * Returns last name of the email owner
     */
    public function getLastName(): ?string;

    /**
     * Returns gender of the email owner
     */
    public function getGender(): ?string;

    /**
     * Returns raw response fields array
     */
    public function raw(): array;
}
