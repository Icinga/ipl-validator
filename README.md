# Icinga PHP Library - Common Validators and Validator Chaining

`ipl/validator` provides validator objects for common validation scenarios
and a `ValidatorChain` to compose and execute multiple validators in a
configurable order. It is framework-agnostic and fits well wherever you
want validation logic that is easy to compose and reuse.

## Installation

`ipl/validator` requires PHP 8.0 or later.
Install it via [Composer](https://getcomposer.org):

```sh
composer require ipl/validator
```

## Quick Start

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

if (! $validator->isValid($value)) {
    foreach ($validator->getMessages() as $message) {
        echo $message . PHP_EOL;
    }
}
```

## Validator Chains

Use `ValidatorChain` when a value must satisfy multiple conditions.

```php
use ipl\Validator\HostnameValidator;
use ipl\Validator\RegexMatchValidator;
use ipl\Validator\ValidatorChain;

$chain = new ValidatorChain();
$chain->add(new HostnameValidator(), breakChainOnFailure: true);
$chain->add(new RegexMatchValidator('/^api\./'));

if (! $chain->isValid($value)) {
    foreach ($chain->getMessages() as $message) {
        echo $message . PHP_EOL;
    }
}
```

In this example, the chain stops early when `HostnameValidator` fails
because it was added with `$breakChainOnFailure = true`.

## Built-In Validators

The library ships with validators for common categories of input:

- **Value and range** — string length, numeric comparisons, and membership tests
- **Text and network formats** — email addresses, hostnames, CIDR notation, dates, and regular expressions
- **File and security** — uploaded files, private keys, and X.509 certificates
- **Custom logic** — callback-based validation for specific rules

## Changelog

See [CHANGELOG.md](./CHANGELOG.md) for a list of notable changes per release.

## License

This library is licensed under the terms of the [MIT License](LICENSE.md).
