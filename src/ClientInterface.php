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
}
