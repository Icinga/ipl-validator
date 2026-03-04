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
     * @param string|array{pattern: string, notMatchMessage?: string|null} $pattern
     *
     * @throws Exception If the notMatchMessage consists only of whitespace or the pattern is missing or invalid
     */
    public function __construct(string|array $pattern)
    {
        if (is_array($pattern)) {
            if (! isset($pattern['pattern'])) {
                throw new Exception("Missing option 'pattern'");
            }

            $this->pattern = $pattern['pattern'];
            $this->notMatchMessage = $pattern['notMatchMessage'] ?? null;

            if ($this->notMatchMessage !== null && empty(trim($this->notMatchMessage))) {
                throw new Exception("Option 'notMatchMessage' consists only of whitespace");
            }
        } else {
            $this->pattern = $pattern;
        }

        $rsv = new RegexSyntaxValidator();

        if (! $rsv->isValid($this->pattern)) {
            throw new Exception($rsv->getMessages()[0]);
        }
    }

    public function isValid($value): bool
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        if (! preg_match($this->pattern, $value)) {
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
