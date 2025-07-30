<?php

namespace ipl\Validator;

use ipl\I18n\Translation;

class DirectoryValidator extends BaseValidator
{
    use Translation;

    /** @var bool Whether directory should be readable */
    protected $shouldReadable;

    /** @var bool Whether directory should be writeable */
    protected $shouldWriteable;

    /**
     * Validates whether value is a directory
     *
     * Available options:
     *
     * - writeable: (bool) If true, check whether directory is writeable, default false
     * - readable: (bool) If true, check whether directory is readable, default false
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setShouldReadable($options['readable'] ?? false);
        $this->setShouldWriteable($options['writable'] ?? false);
    }

    /**
     * Get whether directory should be readable
     *
     * @return bool Whether the directory should be readable
     */
    public function shouldReadable(): bool
    {
        return $this->shouldReadable;
    }

    /**
     * Set whether directory should be readable
     *
     * @param bool $shouldReadable
     */
    public function setShouldReadable($shouldReadable = true): self
    {
        $this->shouldReadable = (bool) $shouldReadable;

        return $this;
    }

    /**
     * Get whether directory should be writeable
     *
     * @return bool Whether the directory should be writeable
     */
    public function shouldWriteable(): bool
    {
        return $this->shouldWriteable;
    }

    /**
     * Set whether directory should be writeable
     *
     * @param bool $shouldWriteable
     */
    public function setShouldWriteable($shouldWriteable = true): void
    {
        $this->shouldWriteable = (bool) $shouldWriteable;
    }

    public function isValid($value)
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        if (! is_dir($value)) {
            $this->addMessage(sprintf(
                $this->translate("'%s' is not a directory"),
                $value
            ));

            return false;
        }

        if ($this->shouldReadable() && ! is_readable($value)) {
            $this->addMessage(sprintf(
                $this->translate("'%s' directory is not readable"),
                $value
            ));

            return false;
        }

        if ($this->shouldWriteable() && ! is_writable($value)) {
            $this->addMessage(sprintf(
                $this->translate("'%s' directory is not writeable"),
                $value
            ));

            return false;
        }

        return true;
    }
}
