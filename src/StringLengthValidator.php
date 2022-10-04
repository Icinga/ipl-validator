<?php

namespace ipl\Validator;

use Exception;
use ipl\I18n\Translation;
use LogicException;

/**
 * Validates string length with given options
 */
class StringLengthValidator extends BaseValidator
{
    use Translation;

    /** Minimum length
     *
     * @var int
     */
    protected $min;

    /** Maximum length
     *
     * If null, there is no maximum length
     *
     * @var int|null
     */
    protected $max;

    /** Encoding to use
     *
     * @var string|null
     */
    protected $encoding;

    public function __construct(array $options)
    {
        $min = $options['min'] ?? 0;
        $max = $options['max'] ?? null;

        if ($max !== null && $min > $max) {
            throw new LogicException(
                sprintf(
                    'The min must be less than or equal to the max length, but min: %s and max: %s given.',
                    $min,
                    $max
                )
            );
        }

        $this->min = $min;
        $this->max = $max;
        $this->encoding = $options['encoding'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getEncoding(): ?string
    {
        return $this->encoding;
    }

    /**
     * @param string|null $encoding
     *
     * @return self
     */
    public function setEncoding(?string $encoding): self
    {
        if ($encoding !== null) {
            $orig = ini_get('default_charset');
            ini_set('default_charset', $encoding);
            $result = ini_get('default_charset');

            if (! $result) {
                throw new Exception('Given encoding not supported on this OS!');
            }

            ini_set('default_charset', $orig);
        }

        $this->encoding = $encoding;

        return  $this;
    }

    public function isValid($value)
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        if (empty($value)) {
            return true;
        }

        $length = iconv_strlen($value, $this->encoding);
        if ($length < $this->min) {
            $this->addMessage(sprintf(
                $this->translate('Value is less than %s characters long.'),
                $this->min
            ));

            return false;
        }

        if ($this->max && $this->max < $length) {
            $this->addMessage(sprintf(
                $this->translate('Value is more than %s characters long.'),
                $this->max
            ));

            return false;
        }

        return true;
    }
}
