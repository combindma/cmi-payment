# Laravel package to communicate with the CMI payment plateform

[![Latest Version on Packagist](https://img.shields.io/packagist/v/combindma/cmi-payment.svg?style=flat-square)](https://packagist.org/packages/combindma/cmi-payment)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/combindma/cmi-payment/run-tests?label=tests)](https://github.com/combindma/cmi-payment/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/combindma/cmi-payment/Check%20&%20fix%20styling?label=code%20style)](https://github.com/combindma/cmi-payment/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/combindma/cmi-payment.svg?style=flat-square)](https://packagist.org/packages/combindma/cmi-payment)


## Installation

You can install the package via composer:

```bash
composer require combindma/cmi-payment
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="cmi-payment-config"
```

This is the contents of the published config file:

```php
return [

];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="cmi-payment-views"
```

## Usage

```php

```

## Testing

```bash
composer test
```


## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Combind](https://github.com/combindma)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
