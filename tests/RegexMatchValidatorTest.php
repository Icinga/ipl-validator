<?php

namespace ipl\Tests\Validator;

use InvalidArgumentException;
use ipl\I18n\NoopTranslator;
use ipl\I18n\StaticTranslator;
use ipl\Validator\RegexMatchValidator;

class RegexMatchValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        StaticTranslator::$instance = new NoopTranslator();
    }

    public function testValidPatternMatch(): void
    {
        $validator = new RegexMatchValidator('/^[a-z]+$/');

        $this->assertTrue($validator->isValid('abc'));
        $this->assertTrue($validator->isValid('xyz'));
    }

    public function testInvalidPatternMatch(): void
    {
        $validator = new RegexMatchValidator('/^[a-z]+$/');

        $this->assertFalse($validator->isValid('ABC'));
        $this->assertFalse($validator->isValid('123'));
        $this->assertFalse($validator->isValid('abc123'));
    }

    public function testConstructorWithStringPattern(): void
    {
        $validator = new RegexMatchValidator('/^\d{3}-\d{4}$/');

        $this->assertTrue($validator->isValid('123-4567'));
        $this->assertFalse($validator->isValid('12-4567'));
        $this->assertFalse($validator->isValid('abc-defg'));
    }

    public function testConstructorWithArrayPattern(): void
    {
        $validator = new RegexMatchValidator(['pattern' => '/^test/']);

        $this->assertTrue($validator->isValid('test123'));
        $this->assertTrue($validator->isValid('testing'));
        $this->assertFalse($validator->isValid('notest'));
    }

    public function testCustomNotMatchMessage(): void
    {
        $customMessage = 'This value does not match the required pattern';
        $validator = new RegexMatchValidator([
            'pattern'         => '/^[0-9]+$/',
            'notMatchMessage' => $customMessage
        ]);

        $this->assertFalse($validator->isValid('abc'));

        $messages = $validator->getMessages();
        $this->assertCount(1, $messages);
        $this->assertSame($customMessage, $messages[0]);
    }

    public function testDefaultNotMatchMessage(): void
    {
        $validator = new RegexMatchValidator('/^[0-9]+$/');

        $this->assertFalse($validator->isValid('abc'));

        $messages = $validator->getMessages();
        $this->assertCount(1, $messages);
        $this->assertStringContainsString("'abc'", $messages[0]);
        $this->assertStringContainsString('/^[0-9]+$/', $messages[0]);
    }

    public function testWhitespaceOnlyNotMatchMessageThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Option 'notMatchMessage' must not be an empty or whitespace-only string");

        new RegexMatchValidator(['pattern' => '/^test/', 'notMatchMessage' => "  \t\n  "]);
    }

    public function testMissingPatternOptionThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing option 'pattern'");

        new RegexMatchValidator(['notMatchMessage' => 'Test message']);
    }

    public function testInvalidRegexPattern(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('failed to compile');

        new RegexMatchValidator('/[/'); // Unclosed character class
    }

    public function testPregMatchErrorYieldsErrorMessage(): void
    {
        $validator = new RegexMatchValidator('/^.$/u');

        $this->assertFalse($validator->isValid("\xFF"));

        $messages = $validator->getMessages();

        $this->assertCount(1, $messages);
        $this->assertStringContainsString('UTF-8', $messages[0]);
    }

    public function testMultipleIsValidCalls(): void
    {
        $validator = new RegexMatchValidator('/^[a-z]+$/');

        // First validation - should fail
        $this->assertFalse($validator->isValid('123'));
        $this->assertCount(1, $validator->getMessages());

        // Second validation - should succeed and clear previous messages
        $this->assertTrue($validator->isValid('abc'));
        $this->assertCount(0, $validator->getMessages());

        // Third validation - should fail with new message
        $this->assertFalse($validator->isValid('XYZ'));
        $this->assertCount(1, $validator->getMessages());
    }

    public function testHexColorPattern(): void
    {
        $validator = new RegexMatchValidator('/^#[0-9A-Fa-f]{6}$/');

        $this->assertTrue($validator->isValid('#FF5733'));
        $this->assertTrue($validator->isValid('#000000'));
        $this->assertTrue($validator->isValid('#ffffff'));
        $this->assertFalse($validator->isValid('#FFF'));
        $this->assertFalse($validator->isValid('FF5733'));
        $this->assertFalse($validator->isValid('#GGGGGG'));
    }

    public function testPhoneNumberPattern(): void
    {
        $validator = new RegexMatchValidator('/^\+?[1-9]\d{1,14}$/');

        $this->assertTrue($validator->isValid('+1234567890'));
        $this->assertTrue($validator->isValid('1234567890'));
        $this->assertFalse($validator->isValid('+0123456789'));
        $this->assertFalse($validator->isValid('abc'));
    }
}
