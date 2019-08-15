<?php

namespace alexeevdv\yii\zerobounce;

/**
 * Interface BulkClientInterface
 * @package alexeevdv\yii\zerobounce
 */
interface BulkClientInterface
{
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
