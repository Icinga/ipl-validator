<?php

namespace ipl\Validator;

use Icinga\Util\Format;
use ipl\I18n\Translation;
use LogicException;
use Psr\Http\Message\UploadedFileInterface;

class FileValidator extends BaseValidator
{
    use Translation;

    /** @var UploadedFileInterface|UploadedFileInterface[] */
    protected $value;

    /** @var int Minimum allowed file size */
    protected $minSize;

    /** @var ?int Maximum allowed file size */
    protected $maxSize;

    /** @var ?string[] Allowed mime types */
    protected $allowedMimeTypes;

    /** @var ?int Maximum allowed file name length */
    protected $maxFileNameLength;

    /**
     * Validates file with given options
     *
     * Available options:
     * - minSize: (int) Minimum allowed file size, default null
     * - maxSize: (int) Maximum allowed file size, default null
     * - maxFileNameLength: (int) Maximum allowed file name length, default null
     * - mimeType: (array) Allowed mime types, default null
     */
    public function __construct(array $options = [])
    {
        $min = $options['minSize'] ?? 0;
        $max = $options['maxSize'] ?? null;
        if ($max !== null) {
            if ($min > $max) {
                throw new LogicException(
                    sprintf(
                        'The minSize must be less than or equal to the maxSize, but minSize: %s and maxSize: %s given.',
                        $min,
                        $max
                    )
                );
            }

            $this->setMaxSize($max);
        }

        $this->setMinSize($min);

        if (isset($options['maxFileNameLength'])) {
            $this->setMaxFileNameLength($options['maxFileNameLength']);
        }

        if (isset($options['mimeType'])) {
            $this->setAllowedMimeTypes($options['mimeType']);
        }
    }

    /**
     * Get the minimum allowed file size
     *
     * @return int
     */
    public function getMinSize(): int
    {
        return $this->minSize;
    }

    /**
     * Set the minimum allowed file size
     *
     * @param int $minSize
     *
     * @return $this
     */
    public function setMinSize(int $minSize): self
    {
        $this->minSize = $minSize;

        return $this;
    }

    /**
     * Get the maximum allowed file size
     *
     * @return ?int
     */
    public function getMaxSize(): ?int
    {
        return $this->maxSize;
    }

    /**
     * Set the maximum allowed file size
     *
     * @param int $maxSize
     *
     * @return $this
     */
    public function setMaxSize(int $maxSize): self
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    /**
     * Get the allowed file mime types
     *
     * @return ?string[]
     */
    public function getAllowedMimeTypes(): array
    {
        return $this->allowedMimeTypes;
    }

    /**
     * Set the allowed file mime types
     *
     * @param string[] $allowedMimeTypes
     *
     * @return $this
     */
    public function setAllowedMimeTypes(array $allowedMimeTypes): self
    {
        $this->allowedMimeTypes = $allowedMimeTypes;

        return $this;
    }

    /**
     * Get maximum allowed file name length
     *
     * @return ?int
     */
    public function getMaxFileNameLength(): ?int
    {
        return $this->maxFileNameLength;
    }

    /**
     * Set maximum allowed file name length
     *
     * @param int $maxFileNameLength
     *
     * @return $this
     */
    public function setMaxFileNameLength(int $maxFileNameLength): self
    {
        $this->maxFileNameLength = $maxFileNameLength;

        return $this;
    }

    public function isValid($value)
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        if (is_array($value)) {
            foreach ($value as $file) {
                if (! $this->validateFile($file)) {
                    return false;
                }
            }
        } else {
            if (! $this->validateFile($value)) {
                return false;
            }
        }

        return true;
    }


    private function validateFile($file): bool
    {
        $isValid = true;
        /** @var $file UploadedFileInterface */
        if ($this->getMaxSize() && $file->getSize() > $this->getMaxSize()) {
            $this->addMessage(sprintf(
                $this->translate('File %s is bigger than the allowed maximum size of %s'),
                $file->getClientFileName(),
                Format::bytes($this->getMaxSize())
            ));

            $isValid = false;
        }

        if ($this->getMinSize() && $file->getSize() < $this->getMinSize()) {
            $this->addMessage(sprintf(
                $this->translate('File %s is smaller than the minimum required size of %s'),
                $file->getClientFileName(),
                Format::bytes($this->getMinSize())
            ));

            $isValid = false;
        }

        if ($this->getMaxFileNameLength()) {
            $strValidator = new StringLengthValidator(['max' => $this->getMaxFileNameLength()]);

            if (! $strValidator->isValid($file->getClientFilename())) {
                $this->addMessage(sprintf(
                    $this->translate('File name is longer than the allowed name length of %s characters.'),
                    $this->maxFileNameLength
                ));

                $isValid = false;
            }
        }

        $hasAllowedMimeType = false;
        if (! empty($this->getAllowedMimeTypes())) {
            foreach ($this->getAllowedMimeTypes() as $type) {
                $fileMimetype = $file->getClientMediaType();
                if (($pos = strpos($type, '/*')) !== false) { // image/*
                    $typePrefix = substr($type, 0, $pos);
                    if (strpos($fileMimetype, $typePrefix) !== false) {
                        $hasAllowedMimeType = true;
                    }
                } elseif (strpos($type, '/') === false) { // .png
                    $typeExtension = trim($type, '.');
                    if (strpos($fileMimetype, $typeExtension) !== false) {
                        $hasAllowedMimeType = true;
                    }
                } elseif (strpos($fileMimetype, $type) !== false) { // image/png
                    $hasAllowedMimeType = true;
                }
            }

            if (! $hasAllowedMimeType) {
                $this->addMessage(sprintf(
                    $this->translate('File %s is of type %s. Only %s allowed.'),
                    $file->getClientFileName(),
                    $file->getClientMediaType(),
                    implode(', ', $this->allowedMimeTypes)
                ));

                $isValid = false;
            }
        }

        return $isValid;
    }
}
