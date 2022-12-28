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

    public function testValidValue(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $options = [
            'mimeType'  => ['pdf'],
        ];

        $validator = new FileValidator($options);

        $uploadedFile = $this->createUploadedFileObject();

        $this->assertTrue($validator->isValid($uploadedFile));
    }

    public function testArrayAsValue(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $options = [
            'mimeType'  => ['pdf'],
        ];

        $validator = new FileValidator($options);


        $files = [
            $this->createUploadedFileObject(),
            $this->createUploadedFileObject()
        ];

        $this->assertTrue($validator->isValid($files));
    }

    public function testMinSizeOption(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $uploadedFile = $this->createUploadedFileObject();

        $validator = (new FileValidator())
            ->setMinSize(10);

        $this->assertTrue($validator->isValid($uploadedFile));

        $validator->setMinSize(700);
        $this->assertFalse($validator->isValid($uploadedFile));
    }

    public function testMaxSizeOption(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $uploadedFile = $this->createUploadedFileObject();

        $validator = (new FileValidator())
            ->setMaxSize(700);

        $this->assertTrue($validator->isValid($uploadedFile));

        $validator->setMaxSize(300);
        $this->assertFalse($validator->isValid($uploadedFile));
    }

    public function testMimeTypeOption(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $uploadedFile = $this->createUploadedFileObject();

        $validator = (new FileValidator())
            ->setAllowedMimeTypes(['gif', '.doc', 'png', '.pdf']);

        $this->assertTrue($validator->isValid($uploadedFile));

        $validator->setAllowedMimeTypes(['gif', '.doc', 'png', '.csv']);
        $this->assertFalse($validator->isValid($uploadedFile));
    }

    public function testMaxFileNameLengthOption(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $uploadedFile = $this->createUploadedFileObject();

        $validator = (new FileValidator())
            ->setMaxFileNameLength(10);

        $this->assertTrue($validator->isValid($uploadedFile));

        $validator->setMaxFileNameLength(3);
        $this->assertFalse($validator->isValid($uploadedFile));
    }
}
