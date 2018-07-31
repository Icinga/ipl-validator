<?php

namespace ipl\Validator;

interface ValidatorInterface
{
    /**
     * Whether the given value is valid
     *
     * // TODO: @throws \RuntimeException
     * @param mixed $value
     * @return bool
     */
    public function isValid($value);

    /**
     * @return array
     */
    public function getMessages();
}
