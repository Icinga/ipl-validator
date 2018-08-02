<?php

namespace ipl\Validator;

use ipl\Stdlib\Contracts\ValidatorInterface;
use ipl\Stdlib\MessageContainer;

abstract class SimpleValidator implements ValidatorInterface
{
    use MessageContainer;

    protected $settings = [];

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    public function getSetting($name, $default = null)
    {
        if (array_key_exists($name, $this->settings)) {
            return $this->settings[$name];
        } else {
            return $default;
        }
    }
}
