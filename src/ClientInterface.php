<?php

namespace alexeevdv\yii\zerobounce;

interface ClientInterface
{
    /**
     * Check if provided email is valid
     */
    public function isEmailValid(string $email): bool;
}
