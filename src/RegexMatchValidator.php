<?php

namespace ipl\Validator;

use Exception;

/**
 * Validates value with a given regex pattern
 *
 * Available options:
 * - pattern: (string) Regex pattern
 * - notMatchMessage: (string) Message to show when the value isn't valid. If not set, a default message will be used
 */
class RegexMatchValidator extends BaseValidator
{
    /** @var string Regex pattern */
    protected string $pattern;

    /** @var ?string Message to show when the value isn't valid. If not set, a default message will be used */
    protected ?string $notMatchMessage = null;

    /**
     * Create a RegexMatchValidator
     *
     * @param string|array $pattern
     *
     * @throws Exception If the given parameter is an array and does not contain the `pattern` option
     */
    public function __construct(string|array $pattern)
    {
        if (is_array($pattern)) {
            if (! isset($pattern['pattern'])) {
                throw new Exception("Missing option 'pattern'");
            }

            $this->pattern = $pattern['pattern'];
            $this->notMatchMessage = $pattern['notMatchMessage'] ?? null;
        } else {
            $this->pattern = $pattern;
        }
    }

    public function isValid($value): bool
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

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
