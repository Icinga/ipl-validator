<?php

namespace ipl\Validator;

use ipl\I18n\Translation;

class UrlValidator extends BaseValidator
{
    use Translation;

    /** @var bool Whether URL must have a path after the domain name to be valid */
    protected $pathRequired;

    /** @var bool Whether URL must have a query string to be valid */
    protected $queryRequired;

    /**
     * Validates url
     *
     * Optional options:
     *
     *  - path_required: (bool) If true, URL must have a path after the domain name (like www.example.com/ab1/),
     * default false
     *
     *  - query_required: (bool) If true, URL must have a query string (like "example.php?name=Foo&age=37"),
     * default false
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setPathRequired($options['path_required'] ?? false)
            ->setQueryRequired($options['query_required'] ?? false);
    }

    /**
     * Whether path is required
     *
     * @return bool
     */
    public function isPathRequired(): bool
    {
        return $this->pathRequired;
    }

    /**
     * Set whether URL must have a path after the domain name to be valid
     *
     * @param bool $pathRequired
     */
    public function setPathRequired($pathRequired = true): self
    {
        $this->pathRequired = (bool) $pathRequired;

        return $this;
    }

    /**
     * Whether query is required
     *
     * @return bool
     */
    public function isQueryRequired(): bool
    {
        return $this->queryRequired;
    }

    /**
     * Set whether URL must have a query string to be valid
     *
     * @param bool $queryRequired
     */
    public function setQueryRequired($queryRequired = true): void
    {
        $this->queryRequired = (bool) $queryRequired;
    }

    public function isValid($value)
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addMessage(sprintf(
                $this->translate("'%s' is not a valid url"),
                $value
            ));

            return false;
        }

        if ($this->isPathRequired() && ! filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
            $this->addMessage(sprintf(
                $this->translate("Url must contain a path after the domain name"),
                $value
            ));

            return false;
        }

        if ($this->isQueryRequired() && ! filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED)) {
            $this->addMessage(sprintf(
                $this->translate("Url must contain a query string"),
                $value
            ));

            return false;
        }

        return true;
    }
}
