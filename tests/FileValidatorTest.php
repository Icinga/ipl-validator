<?php

namespace ipl\Tests\Validator;

use ipl\I18n\NoopTranslator;
use ipl\I18n\StaticTranslator;
use ipl\Validator\FileValidator;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class FileValidatorTest extends TestCase
{
    public function createUploadedFileObject(
        string $filename = 'test.pdf',
        int $size = 500,
        int $error = UPLOAD_ERR_OK,
        string $mimeType = 'application/pdf'
    ): UploadedFileInterface {
        return new class ($filename, $size, $error, $mimeType) implements UploadedFileInterface {
            private readonly int $size;
            private readonly int $error;
            private readonly string $clientFilename;
            private readonly string $clientMediaType;

            public function __construct(
                string $clientFilename,
                int $size,
                int $error,
                string $clientMediaType
            ) {
                $this->size = $size;
                $this->error = $error;
                $this->clientFilename = $clientFilename;
                $this->clientMediaType = $clientMediaType;
            }

            public function getStream(): StreamInterface
            {
                throw new RuntimeException('not implemented');
            }

            public function moveTo(string $targetPath): void
            {
                throw new RuntimeException('not implemented');
            }

            public function getSize(): ?int
            {
                return $this->size;
            }

            public function getError(): int
            {
                return $this->error;
            }

            public function getClientFilename(): ?string
            {
                return $this->clientFilename;
            }

            public function getClientMediaType(): ?string
            {
                return $this->clientMediaType;
            }
        };
    }

    public function testValidValue(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $validator = new FileValidator();

        $uploadedFile = $this->createUploadedFileObject();

        $this->assertTrue($validator->isValid($uploadedFile));
    }

    public function testArrayAsValue(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $validator = new FileValidator();

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

        $validator = new FileValidator(['minSize' => 10]);

        $this->assertTrue($validator->isValid($uploadedFile));

        $validator->setMinSize(700);
        $this->assertFalse($validator->isValid($uploadedFile));
    }

    public function testMaxSizeOption(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $uploadedFile = $this->createUploadedFileObject();

        $validator = new FileValidator(['maxSize' => 700]);

        $this->assertTrue($validator->isValid($uploadedFile));

        $validator->setMaxSize(300);
        $this->assertFalse($validator->isValid($uploadedFile));
    }

    public function testMimeTypeOption(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $uploadedFile = $this->createUploadedFileObject();

        $validator = (new FileValidator())
            ->setAllowedMimeTypes(['application/pdf']);

        $this->assertTrue($validator->isValid($uploadedFile));

        $validator->setAllowedMimeTypes(['application/*']);

        $this->assertTrue($validator->isValid($uploadedFile));

        $validator->setAllowedMimeTypes(['image/gif', 'image/jpeg']);
        $uploadedFile = $this->createUploadedFileObject(mimeType: 'image/png');

        $this->assertFalse($validator->isValid($uploadedFile));
    }

    public function testMaxFileNameLengthOption(): void
    {
        StaticTranslator::$instance = new NoopTranslator();

        $uploadedFile = $this->createUploadedFileObject();

        $validator = new FileValidator(['maxFileNameLength' => 10]);

        $this->assertTrue($validator->isValid($uploadedFile));

        $validator->setMaxFileNameLength(3);
        $this->assertFalse($validator->isValid($uploadedFile));
    }
}
