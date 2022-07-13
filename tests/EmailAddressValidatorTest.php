<?php

namespace ipl\Tests\Validator;

use DateTime;
use ipl\I18n\NoopTranslator;
use ipl\I18n\StaticTranslator;
use ipl\Validator\DateTimeValidator;
use ipl\Validator\EmailAddressValidator;

class EmailAddressValidatorTest extends TestCase
{
    public function testEmailAddressValidatorWithValidEmailAddress()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $this->assertTrue((new EmailAddressValidator())->isValid("test@test.com"), 'email address is valid');
    }

    public function testEmailAddressValidatorWithInvalidEmailAddress()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator();

        $this->assertFalse($validator->isValid("testattheratetest.com"), 'email address is invalid');
    }
}
