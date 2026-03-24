# Icinga PHP Library - Common Validators and Validator Chaining

`ipl/validator` provides a collection of reusable input validators and a
`ValidatorChain` to compose and execute multiple validators in a
user-defined order.

## Installation

The recommended way to install this library is via
[Composer](https://getcomposer.org):

```shell
composer require ipl/validator
```

`ipl/validator` requires PHP 8.0 or later with the `mbstring` and `openssl` extensions.

## Usage

### Applying a Single Validator

Every validator exposes the same basic workflow:

1. Configure the validator
2. Call `isValid($value)`
3. Read validation errors via `getMessages()` if validation fails

```php
use ipl\Validator\StringLengthValidator;

$validator = new StringLengthValidator([
    'min' => 3,
    'max' => 10,
]);

// $value is the input to validate.
if (! $validator->isValid($value)) {
    echo implode(PHP_EOL, $validator->getMessages());
}
```

### Composing Multiple Validators with `ValidatorChain`

Use `ValidatorChain` when a value must satisfy multiple conditions. By default,
all validators run in sequence even when one fails, so every error is collected.
Set `breakChainOnFailure: true` when adding a validator to stop on the first failure.

```php
use ipl\Validator\HostnameValidator;
use ipl\Validator\RegexMatchValidator;
use ipl\Validator\ValidatorChain;

$chain = new ValidatorChain();
$chain->add(new HostnameValidator(), breakChainOnFailure: true);
$chain->add(new RegexMatchValidator('/^api\./'));

// $value is the input to validate.
if (! $chain->isValid($value)) {
    echo implode(PHP_EOL, $chain->getMessages());
}
```

In this example, the chain stops early when `HostnameValidator` fails
because it was added with `breakChainOnFailure: true`.

## Built-In Validators

`ipl/validator` ships with validators for common categories of input:

- **Value and range** — string length, numeric comparisons, and membership tests
- **Text and network formats** — email addresses, hostnames, CIDR notation, dates, and regular expressions
- **File and security** — uploaded files, private keys, and X.509 certificates
- **Custom logic** — callback-based validation for specific rules:
  ```php
  use ipl\Validator\CallbackValidator;

  // Illustrates cross-field validation, omits type checks for brevity.
  $validator = new CallbackValidator(function ($end, CallbackValidator $validator) use ($start): bool {
      if ($end <= $start) {
          $validator->addMessage('End must be after start.');

          return false;
      }

      return true;
  });
  ```

  Use this for rules that built-in validators cannot express —
  such as cross-field comparisons or checks against runtime state.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of notable changes.

## License

`ipl/validator` is licensed under the terms of the [MIT License](LICENSE.md).
