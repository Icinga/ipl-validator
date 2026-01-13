<?php

namespace ipl\Tests\Validator;

use ipl\I18n\NoopTranslator;
use ipl\I18n\StaticTranslator;
use ipl\Validator\RegexSyntaxValidator;

class RegexSyntaxValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        StaticTranslator::$instance = new NoopTranslator();
    }

    public function testBadType()
    {
        $validator = new RegexSyntaxValidator();

        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid(false));
        $this->assertFalse($validator->isValid(true));
        $this->assertFalse($validator->isValid(42));
        $this->assertFalse($validator->isValid(3.14));
        $this->assertFalse($validator->isValid([]));
        $this->assertFalse($validator->isValid((object) []));
    }

    public function testInvalidRegex()
    {
        $validator = new RegexSyntaxValidator();

        $this->assertFalse($validator->isValid('no_delimiters'));
        $this->assertFalse($validator->isValid('/unclosed_pattern'));
        $this->assertFalse($validator->isValid('/pattern/invalid-modifier!'));
        $this->assertFalse($validator->isValid('/invalid[pattern/'));
        $this->assertFalse($validator->isValid('/(unbalanced_parentheses/'));
    }

    public function testValidRegex()
    {
        $validator = new RegexSyntaxValidator();

        $this->assertTrue($validator->isValid('/simple_pattern/'));
        $this->assertTrue($validator->isValid('/pattern_with_modifiers/i'));
        $this->assertTrue($validator->isValid('/complex[pattern](with){various}+elements?$/m'));
    }
}
