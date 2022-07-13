<?php

namespace ipl\Tests\Validator;

use ipl\I18n\NoopTranslator;
use ipl\I18n\StaticTranslator;
use ipl\Validator\EmailAddressValidator;

class EmailAddressValidatorTest extends TestCase
{
    public function testEmailAddressValidatorWithValidEmailAddress()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $this->assertTrue((new EmailAddressValidator())->isValid("test@test.com"), 'valid email address is invalid');
    }

    public function testEmailAddressValidatorWithInvalidEmailAddress()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator();

        $this->assertFalse($validator->isValid("testattheratetest.com"), 'invalid email address is valid');
    }

    public function testEmailAddressValidatorWithInvalidDomainname()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator();

        $this->assertFalse(
            $validator->isValid("test@tes t.com"),
            'invalid host name as domain part is valid for the email address'
        );
    }

    public function testEmailAddressValidatorWithInvalidLocalpart()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator();

        $this->assertFalse($validator->isValid("tes t@test.com"), 'invalid local part is valid for the email address');
    }

    public function testEmailAddressValidatorWithInvalidIpAsHostName()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator();

        $this->assertFalse(
            $validator->isValid('test@[580.0.0.0]'),
            'invalid ip as domain part is valid for the email address'
        );
    }

    public function testEmailAddressValidatorWithLongLocalPart()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator();

        $this->assertFalse(
            $validator->isValid("FXIzqQyYyvKLpjAGW8sUwyHxlVeGMXIR9ZFtciqkI2OJ3vkCO7DVxQt80UAQeIsVhA2Re@10.0.0.0/8"),
            'local part exceeding the maximum length (64) is valid for the email address'
        );
    }

    public function testEmailAddressValidatorWithValidMXRecordValidation()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator(['mx' => true]);

        $this->assertTrue(
            $validator->isValid("test@example.com"),
            'domain part for email address with valid MX Record is invalid'
        );
    }

    public function testEmailAddressValidatorWithInvalidMXRecordValidation()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator(['mx' => true]);

        $this->assertFalse(
            $validator->isValid("test@test.com"),
            'domain part with invalid MX Record for email address is valid'
        );
    }

    public function testEmailAddressValidatorWithValidDeepMXRecordValidation()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator(['mx' => true, 'deep' => true]);

        $this->assertTrue(
            $validator->isValid("test@google.com"),
            'domain part with valid deep MX Record for email address is invalid'
        );
    }

    public function testEmailAddressValidatorWithInvalidDeepMXRecordValidation()
    {
        StaticTranslator::$instance = new NoopTranslator();
        $validator = new EmailAddressValidator(['mx' => true, 'deep' => true]);

        $this->assertFalse(
            $validator->isValid("test@example.com"),
            'domain part with invalid deep MX Record for email address is valid'
        );
    }
}
