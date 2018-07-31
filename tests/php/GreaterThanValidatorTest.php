<?php

namespace ipl\Tests\Validator;

use ipl\Validator\GreaterThanValidator;

class GreaterThanValidatorTest extends TestCase
{
    public function testHigherValueIsValid()
    {
        $validator = new GreaterThanValidator(10);
        $this->assertTrue($validator->isValid(11));
    }

    public function testLowerValueIsInvalid()
    {
        $validator = new GreaterThanValidator(10);
        $this->assertFalse($validator->isValid(9));
    }

    public function testEqualValueIsInvalid()
    {
        $validator = new GreaterThanValidator(10);
        $this->assertFalse($validator->isValid(10));
    }
}
