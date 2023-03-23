<?php

namespace ipl\Validator;

use ipl\I18n\Translation;

class IpAddressValidator extends BaseValidator
{
    use Translation;

    /** @var bool if true, value must be a valid IPv4 address */
    protected $shouldIpv4;

    /** @var bool if true, value must be a valid IPv6 address */
    protected $shouldIpv6;

    /** @var bool if true, value must not be within a private range */
    protected $shouldNotInPrivateRange;

    /** @var bool if true, value must not be within a reserved range */
    protected $shouldNotInReservedRange;

    public function __construct(array $options = [])
    {
        if (isset($options['ipv4'])) {
            $this->setShouldIpv4($options['ipv4']);
        } elseif (isset($options['ipv6'])) {
            $this->setShouldIpv6($options['ipv6']);
        }
    }

    /**
     * @return bool
     */
    public function getShouldIpv4(): bool
    {
        return $this->shouldIpv4;
    }

    /**
     * @param bool $shouldIpv4
     */
    public function setShouldIpv4($shouldIpv4 = true): void
    {
        $this->shouldIpv4 = (bool) $shouldIpv4;
    }

    /**
     * @return bool
     */
    public function getShouldIpv6(): bool
    {
        return $this->shouldIpv6;
    }

    /**
     * @param bool $shouldIpv6
     */
    public function setShouldIpv6($shouldIpv6 = true): void
    {
        $this->shouldIpv6 = (bool) $shouldIpv6;
    }

    /**
     * @return bool
     */
    public function getShouldNotInPrivateRange(): bool
    {
        return $this->shouldNotInPrivateRange;
    }

    /**
     * @param bool $shouldNotInPrivateRange
     */
    public function setShouldNotInPrivateRange($shouldNotInPrivateRange = true): void
    {
        $this->shouldNotInPrivateRange = (bool) $shouldNotInPrivateRange;
    }

    /**
     * @return bool
     */
    public function getShouldNotInReservedRange(): bool
    {
        return $this->shouldNotInReservedRange;
    }

    /**
     * @param bool $shouldNotInReservedRange
     */
    public function setShouldNotInReservedRange($shouldNotInReservedRange = true): void
    {
        $this->shouldNotInReservedRange = (bool) $shouldNotInReservedRange;
    }

    public function isValid($value)
    {
        // Multiple isValid() calls must not stack validation messages
        $this->clearMessages();

        if (! filter_var($value, FILTER_VALIDATE_IP)) {
            $this->addMessage(sprintf(
                $this->translate("%s is not a valid IP address"),
                $value
            ));

            return false;
        }

        if ($this->getShouldIpv4() && ! filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->addMessage(sprintf(
                $this->translate("%s is not a valid IPv4 address"),
                $value
            ));

            return false;
        }

        if ($this->getShouldIpv6() && ! filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $this->addMessage(sprintf(
                $this->translate("%s is not a valid IPv6 address"),
                $value
            ));

            return false;
        }

        if ($this->getShouldNotInPrivateRange()
            && ! filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $this->addMessage(sprintf(
                $this->translate("Ip address must not be within a private range"),
                $value
            ));

            return false;
        }

        if ($this->getShouldNotInReservedRange()
            && ! filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)) {
            $this->addMessage(sprintf(
                $this->translate("Ip address must not be within a reserved range"),
                $value
            ));

            return false;
        }

        return true;
    }
}
