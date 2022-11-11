<?php

namespace ipl\Tests\Validator\lib;

use ipl\Validator\InArrayValidator;

class InArrayValidatorWithPublicFindInvalid extends InArrayValidator
{
    public function findInvalid(array $values = []): array
    {
        return parent::findInvalid($values);
    }
}
