<?php

namespace ipl\Validator;

use Exception;
use ipl\I18n\Translation;

/**
 * Validates value with given regex pattern
 */
class RegexValidator extends BaseValidator
{
    use Translation;

    protected $pattern;

    protected $notMatchMessage;

    public function __construct($pattern)
    {
        if (is_array($pattern)) {
            if (! isset($pattern['pattern'])) {
                throw new Exception("Missing option 'pattern'");
            }

            $this->pattern = $pattern['pattern'];
            $this->notMatchMessage = $pattern['notMatchMessage'] ?? null;
        } else {
            $this->pattern = (string) $pattern;
        }
    }

    public function isValid($value)
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        if (empty($value)) {
            return true;
        }

        $status = @preg_match($this->pattern, $value);
        if ($status === false) {
            $this->addMessage(sprintf(
                "There was an internal error while using the pattern '%s'",
                $this->pattern
            ));

            return false;
        }

        if ($status === 0) {
            if (empty($this->notMatchMessage)) {
                $this->addMessage(sprintf(
                    $this->translate("'%s' does not match against pattern '%s'"),
                    $value,
                    $this->pattern
                ));
            } else {
                $this->addMessage($this->notMatchMessage);
            }

            return false;
        }

        return true;
    }
}
