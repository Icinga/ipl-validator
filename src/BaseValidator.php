<?php

namespace ipl\Validator;

use ipl\Stdlib\Contract\Validator;
use ipl\Stdlib\Messages;

abstract class BaseValidator implements Validator
{
    use Messages;

    /** @var bool Whether to validate an empty value */
    protected $validateEmpty = false;

    /**
     * Get whether to validate an empty value
     *
     * @return bool
     */
    public function validateEmpty(): bool
    {
        return $this->validateEmpty;
    }

    /**
     * Set whether to validate an empty value
     *
     * @param bool $validateEmpty
     *
     * @return $this
     */
    public function setValidateEmpty(bool $validateEmpty = true): self
    {
        $this->validateEmpty = $validateEmpty;

        return $this;
    }
}
