<?php

namespace ipl\Validator;

/**
 * Validator that uses a callback for the actual validation
 *
 * # Example Usage
 * ```
 * $dedup = new CallbackValidator(function ($value, CallbackValidator $validator) {
 *     if (already_exists_in_database($value)) {
 *         $validator->addMessage('Record already exists in database');
 *
 *         return false;
 *     }
 *
 *     return true;
 * });
 *
 * $dedup->isValid($id);
 * ```
 */
class CallbackValidator extends BaseValidator
{
    /** @var callable Validation callback */
    protected $callback;

    /** @var bool Whether to cache the callback's result */
    protected $cacheResult;

    /** @var ?bool The result, if {@see self::$cacheResult} is `true` (default) */
    protected $result;

    /**
     * Create a new callback validator
     *
     * @param callable $callback Validation callback
     * @param boolean $cacheResult Whether to cache the callback's result
     */
    public function __construct(callable $callback, bool $cacheResult = true)
    {
        $this->callback = $callback;
        $this->cacheResult = $cacheResult;
    }

    public function isValid($value)
    {
        if ($this->result !== null) {
            return $this->result;
        }

        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        $valid = (bool) call_user_func($this->callback, $value, $this);
        if ($this->cacheResult) {
            $this->result = $valid;
        }

        return $valid;
    }
}
