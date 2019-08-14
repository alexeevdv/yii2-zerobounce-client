<?php

namespace alexeevdv\yii\zerobounce;

interface ClientInterface
{
    /**
     * @throws Exception
     */
    public function validate(string $email, string $ip = ''): ValidateResponseInterface;

    /**
     * @throws Exception
     */
    public function getCredits(): int;

    /**
     * @throws Exception
     */
    public function sendFile(string $fileName, string $redirectUrl): array;

    /**
     * @throws Exception
     */
    public function readFile(string $fileId): string;

    /**
     * @throws Exception
     */
    public function deleteFile(string $fileId): bool;
}
