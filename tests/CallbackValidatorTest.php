<?php

namespace ipl\Tests\Validator;

use ipl\Validator\CallbackValidator;

class CallbackValidatorTest extends TestCase
{
    public function testWhetherValidationCallbackIsOnlyExecutedWhenIsValidIsCalled()
    {
        $messages = ['Too short', 'Must contain only digits'];

        $validator = new CallbackValidator(function ($value, CallbackValidator $validator) use ($messages) {
            $validator->setMessages($messages);

            return $value;
        });

        $this->assertSame([], $validator->getMessages());

        $validator->isValid(true);

        $this->assertSame($messages, $validator->getMessages());
    }

    public function testWhetherCallbackIsOnlyCalledAgainIfNotCached()
    {
        $count = 0;

        $validator = new CallbackValidator(function ($value, CallbackValidator $validator) use (&$count) {
            $count++;
            return true;
        });

        $validator->isValid(true);
        $validator->isValid(true);

        $this->assertEquals(1, $count, 'The callback is called again even if the cache is enabled');
    }

    public function testWhetherCallbackIsCalledAgainIfCached()
    {
        $count = 0;

        $validator = new CallbackValidator(function ($value, CallbackValidator $validator) use (&$count) {
            $count++;
            return true;
        }, false);

        $validator->isValid(true);
        $validator->isValid(true);

        $this->assertEquals(2, $count, 'The callback is not called again even if the cache is disabled');
    }
}
