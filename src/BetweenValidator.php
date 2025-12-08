<?php

namespace ipl\Validator;

use Exception;
use ipl\I18n\Translation;

/**
 * Validates whether value is between the given min and max
 */
class BetweenValidator extends BaseValidator
{
    use Translation;

    /** @var int|float Min value */
    protected int|float $min;

    /** @var int|float Max value */
    protected int|float $max;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to min and/or max
     *
     * If false, then strict comparisons are done, and the value may equal neither
     * the min nor max options
     *
     * @var bool
     */
    protected bool $inclusive;

    /**
     * Create a new BetweenValidator
     *
     * Required options:
     *
     * - min: (int|float) Minimum border
     * - max: (int|float) Maximum border
     *
     * Optional options:
     *
     * - inclusive: (bool) Whether inclusive border values, default true
     *
     * @param array{min: int|float, max: int|float, inclusive?: bool} $options
     *
     * @throws Exception When required option is missing
     */
    public function __construct(array $options)
    {
        if (! isset($options['min'], $options['max'])) {
            throw new Exception("Missing option. 'min' and 'max' has to be given");
        }

        $this->setMin($options['min'])
            ->setMax($options['max'])
            ->setInclusive($options['inclusive'] ?? true);
    }

    /**
     * Return the min option
     *
     * @return int|float
     */
    public function getMin(): float|int
    {
        return $this->min;
    }

    /**
     * Set the min option
     *
     * @param float|int $min
     *
     * @return $this
     */
    public function setMin(float|int $min): self
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Return the max option
     *
     * @return int|float
     */
    public function getMax(): float|int
    {
        return $this->max;
    }

    /**
     * Set the max option
     *
     * @param float|int $max
     *
     * @return $this
     */
    public function setMax(float|int $max): self
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Return the inclusive option
     *
     * @return bool
     */
    public function getInclusive(): bool
    {
        return $this->inclusive;
    }

    /**
     * Set the inclusive option
     *
     * @param  bool $inclusive
     *
     * @return $this
     */
    public function setInclusive(bool $inclusive = true): self
    {
        $this->inclusive = $inclusive;

        return $this;
    }

    /**
     * @param int|float $value
     *
     * @return bool
     */
    public function isValid($value): bool
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        if ($this->getInclusive()) {
            if ($this->getMin() > $value || $value > $this->getMax()) {
                $this->addMessage(sprintf(
                    $this->translate("'%s' is not between '%s' and '%s', inclusively"),
                    $value,
                    $this->getMin(),
                    $this->getMax()
                ));

                return false;
            }
        } elseif ($this->getMin() >= $value || $value >= $this->getMax()) {
            $this->addMessage(sprintf(
                $this->translate("'%s' is not between '%s' and '%s'"),
                $value,
                $this->getMin(),
                $this->getMax()
            ));

            return false;
        }

        return true;
    }
}
