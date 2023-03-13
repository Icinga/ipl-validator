<?php

namespace ipl\Validator;

use ipl\Stdlib\Messages;

class ValidatedResult
{
    use Messages;

    /** @var bool */
    protected $isValid = true;

    /**
     * Get whether the validation was successful
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Set whether the validation was successful
     *
     * @param bool $valid
     *
     * @return $this
     */
    public function setIsValid(bool $valid): self
    {
        $this->isValid = $valid;

        return $this;
    }
}
