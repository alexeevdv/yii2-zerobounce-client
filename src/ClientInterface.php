<?php

namespace alexeevdv\yii\zerobounce;

interface ClientInterface
{
    public function validate(string $email, string $ip = ''): ValidateResponseInterface;

    public function getCredits(): int;
}
