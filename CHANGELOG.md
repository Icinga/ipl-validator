# Changelog

All notable changes to this library are documented in this file.

## [Unreleased]

- **Breaking** Raise minimum PHP version to 8.2 (#44)
- Add `RegexMatchValidator` to validate values against a
  regular expression (#19)
- Add `RegexSyntaxValidator` to validate that a string is a
  syntactically valid regular expression (#42)
- Add strict type declarations (#39, #44)
- Support PHP 8.5 (#38)

## [0.5.0] - 2023-03-22

- Add `EmailAddressValidator` (#7)
- Add `HexColorValidator` (#9)
- Add `InArrayValidator` (#11)
- Add `DeferredInArrayValidator` (#12)
- Add `FileValidator` (#13)
- Add `BetweenValidator` (#17)
- Add `GreaterThanValidator` (#20)
- Add `LessThanValidator` (#21)
- Add `StringLengthValidator` (#14)
- Add `CidrValidator` (#25)
- Support PHP 8.2 (#24)
- Refine validation of empty values (#15, #27, #28, #29)

## [0.4.0] - 2022-06-15

- **Breaking** Drop support for PHP 5.6, 7.0, and 7.1 (#5)
- Support PHP 8.1 (#5)

## [0.3.0] - 2021-11-10

- Add `DateTimeValidator` to validate that a value is a parseable
  date/time string (#3)

## [0.2.0] - 2021-06-15

- Add `X509CertValidator` to validate X.509 certificates and
  `PrivateKeyValidator` to validate private keys (#2)
- Support PHP 8 (#1)

## [0.1.0] - 2020-03-12

Initial release with `BaseValidator`, `CallbackValidator`, and
`ValidatorChain`.

[Unreleased]: https://github.com/Icinga/ipl-validator/compare/v0.5.0...HEAD
[0.5.0]: https://github.com/Icinga/ipl-validator/releases/tag/v0.5.0
[0.4.0]: https://github.com/Icinga/ipl-validator/releases/tag/v0.4.0
[0.3.0]: https://github.com/Icinga/ipl-validator/releases/tag/v0.3.0
[0.2.0]: https://github.com/Icinga/ipl-validator/releases/tag/v0.2.0
[0.1.0]: https://github.com/Icinga/ipl-validator/releases/tag/v0.1.0
