<?php

namespace ipl\Validator;

use ipl\I18n\Translation;

/**
 * Validates Email address
 */
class EmailAddressValidator extends BaseValidator
{
    use Translation;

    public function isValid($value)
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        // Validate email address
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addMessage($this->translate('Invalid Email address given.'));
            return false;
        }

        return true;
    }
}
