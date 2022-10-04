<?php

namespace ipl\Validator;

use Exception;

class AssertionValidator extends BaseValidator
{
    public function isValid($value)
    {
        try {
            return Assertion::$type($this->options);
        } catch (Exception $e) {
            $this->addMessage($e->getMessage());
        }
    }

    public function setOptions($options) {
        $this->options = $options;
    }
}
