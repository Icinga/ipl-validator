<?php

namespace ipl\Validator;

use Icinga\Util\Format;
use ipl\I18n\Translation;
use LogicException;

/**
 * Validates file with given options
 */
class FileValidator extends BaseValidator
{
    use Translation;

    /** @var int|null */
    protected $minSize;

    /** @var int|null */
    protected $maxSize;

    /** @var array */
    protected $allowedMimeTypes;

    /** @var int|null */
    protected $maxFileNameLength;

    public function __construct(array $options)
    {
        $min = $options['minSize'] ?? null;
        $max = $options['maxSize'] ?? null;
        if ($max !== null && $min > $max) {
            throw new LogicException(
                sprintf(
                    'The minSize must be less than or equal to the maxSize, but minSize: %s and maxSize: %s given.',
                    $min,
                    $max
                )
            );
        }

        $this->minSize = $min;
        $this->maxSize = $max;
        $this->maxFileNameLength = $options['maxFileNameLength'] ?? null;
        $this->allowedMimeTypes = ! empty($options['mimeType'])
            ? explode(',', str_replace(' ', '', $options['mimeType']))
            : [];
    }

    public function isValid($value)
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        if (empty($value)) {
            return true;
        }

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
        /** @var $file  \GuzzleHttp\Psr7\UploadedFile */
        if ($this->maxSize && $file->getSize() > $this->maxSize) {
            $this->addMessage(sprintf(
                $this->translate('File %s is bigger than the allowed maximum size of %s'),
                $file->getClientFileName(),
                Format::bytes($this->maxSize)
            ));

            return false;
        }

        if ($this->minSize && $file->getSize() < $this->minSize) {
            $this->addMessage(sprintf(
                $this->translate('File %s is smaller than the minimum required size of %s'),
                $file->getClientFileName(),
                Format::bytes($this->minSize)
            ));

            return false;
        }

        if ($this->maxFileNameLength) {
            $strValidator = new StringLengthValidator(['max' => $this->maxFileNameLength]);

            if (! $strValidator->isValid($file->getClientFilename())) {
                $this->addMessage(sprintf(
                    $this->translate('File name is longer than the allowed name length of %s characters.'),
                    $this->maxFileNameLength
                ));

                return false;
            }
        }

        $hasRequiredMimeType = false;
        foreach ($this->allowedMimeTypes as $type) {
            $fileMimetype = $file->getClientMediaType();
            if (($pos = strpos($type, '/*')) !== false) { // image/*
                $typePrefix = substr($type, 0, $pos);
                if (strpos($fileMimetype, $typePrefix) !== false) {
                    $hasRequiredMimeType = true;
                    break;
                }
            } elseif (strpos($type, '/') === false) { // .png
                $typeExtension = trim($type, '.');
                if (strpos($fileMimetype, $typeExtension) !== false) {
                    $hasRequiredMimeType = true;
                    break;
                }
            } elseif (strpos($fileMimetype, $type) !== false) { // image/png
                $hasRequiredMimeType = true;
                break;
            }
        }

        if (! empty($this->allowedMimeTypes) && ! $hasRequiredMimeType) {
            $this->addMessage(sprintf(
                $this->translate('File %s is of type %s. Only %s allowed.'),
                $file->getClientFileName(),
                $file->getClientMediaType(),
                implode(', ', $this->allowedMimeTypes)
            ));

            return false;
        }

        return true;
    }
}
