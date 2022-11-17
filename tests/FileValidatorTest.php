<?php

namespace ipl\Tests\Validator;

use GuzzleHttp\Psr7\UploadedFile;
use ipl\I18n\NoopTranslator;
use ipl\I18n\StaticTranslator;
use ipl\Validator\FileValidator;

class FileValidatorTest extends TestCase
{
    public function createUploadedFileObject(): UploadedFile
    {
        return new UploadedFile(
            'test/test.pdf',
            500,
            0,
            'test.pdf',
            'application/pdf'
        );
    }

    public function testWithValidValue(): void
    {
        $options = [
            'mimeType'  => ['pdf'],
        ];

        $validator = new FileValidator($options);

        $uploadedFile = $this->createUploadedFileObject();

        $this->assertTrue($validator->isValid($uploadedFile));
    }
}
