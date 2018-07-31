<?php

namespace ipl\Validator;

class GreaterThanValidator extends SimpleValidator
{
    public function __construct($max)
    {
        if (is_array($max)) {
            parent::__construct($max);
        } else {
            $this->settings = [
                'max' => $max
            ];
        }
    }

    public function isValid($value)
    {
        if ($value > $this->getSetting('max', PHP_INT_MAX)) {
            $this->clearMessages();

            return true;
        } else {
            $this->addMessage(
                '%s is not greater than %d',
                $value,
                $this->getSetting('max', PHP_INT_MAX)
            );

            return false;
        }
    }
}
