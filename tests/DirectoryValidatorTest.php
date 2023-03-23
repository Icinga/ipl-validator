<?php

namespace ipl\Tests\Validator;

use ipl\I18n\NoopTranslator;
use ipl\I18n\StaticTranslator;
use ipl\Validator\DirectoryValidator;

class DirectoryValidatorTest extends TestCase
{
    public function createDir(string $dir) : string
    {
        if (! file_exists($dir)) {
            mkdir($dir, 0000, true);
            //chmod($dir, 0000);
        }

        return $dir;
    }

    public function testValidation(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $validator = new DirectoryValidator();
        $dir = 'foo/bar';

        $this->assertFalse($validator->isValid($dir));

        $this->assertTrue($validator->isValid($this->createDir($dir)));

        rmdir($dir);
    }

    public function testShouldReadableOption(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $validator = new DirectoryValidator(['readable' => true]);
        $dir = 'foo/bar';

        $this->assertFalse($validator->isValid($dir));

        $this->assertTrue($validator->isValid($this->createDir($dir)));

        rmdir($dir);
    }
}