<?php

namespace ipl\Validator;

use ipl\Stdlib\Contract\PluginLoader;

class AssertionLoader implements PluginLoader
{
    public function load($name)
    {
        if (method_exists(Assertion, $name)) {
            return new AssertionValidator($name);
        }

        return false;
    }
}
