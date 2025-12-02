# Email Validation for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eg-mohamed/email-validation.svg?style=flat-square)](https://packagist.org/packages/eg-mohamed/email-validation)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/eg-mohamed/email-validation/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/eg-mohamed/email-validation/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/eg-mohamed/email-validation/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/eg-mohamed/email-validation/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eg-mohamed/email-validation.svg?style=flat-square)](https://packagist.org/packages/eg-mohamed/email-validation)

A comprehensive Laravel package for advanced email validation combining RFC compliance checks, DNS/MX record verification, and disposable email detection. Built on top of `egulias/email-validator` and `propaganistas/laravel-disposable-email`.

## Features

- **RFC Syntax Validation**: Validates email format against RFC 5321, 5322, 6530, 6531, 6532, and 1035
- **DNS/MX Record Verification**: Checks if the email domain has valid MX records
- **Disposable Email Detection**: Blocks temporary/disposable email providers
- **Configurable Validations**: Enable/disable specific validation checks via config
- **Custom Error Messages**: Customize validation error messages
- **Facade Support**: Easy access via Laravel facade

## Installation

You can install the package via composer:

```bash
composer require eg-mohamed/email-validation
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="email-validation-config"
```

This is the contents of the published config file:

```php
return [
    'validations' => [
        'syntax' => true,
        'dns' => true,
        'disposable' => true,
    ],
];
```

You can also publish the translations:

```bash
php artisan vendor:publish --tag="email-validation-translations"
```

The package includes English and Arabic translations by default.

## Usage

### Using the Validation Rule

You can use the `email_validation` rule in your validation rules:

```php
use Illuminate\Http\Request;

public function store(Request $request)
{
    $validated = $request->validate([
        'email' => ['required', 'email_validation'],
    ]);
}
```

### Using the Rule Class

You can also use the rule class directly:

```php
use MohamedSaid\EmailValidation\Rules\EmailValidationRule;

$request->validate([
    'email' => ['required', new EmailValidationRule()],
]);
```

### Using the Facade

The package provides a facade for programmatic validation:

```php
use MohamedSaid\EmailValidation\Facades\EmailValidation;

if (EmailValidation::isValid('user@example.com')) {
    // Email is valid
}

$results = EmailValidation::validate('user@example.com');

$failures = EmailValidation::getFailures('user@example.com');
```

### Configuration

You can control which validations are performed by modifying the config file:

```php
return [
    'validations' => [
        'syntax' => true,      // RFC syntax validation
        'dns' => true,         // DNS/MX record check
        'disposable' => true,  // Disposable email detection
    ],
];
```

### Custom Error Messages

Customize the error messages by publishing and editing the translation files:

**English (lang/en/email-validation.php):**
```php
return [
    'syntax' => 'The :attribute must be a valid email address.',
    'dns' => 'The :attribute domain does not have valid MX records.',
    'disposable' => 'Disposable email addresses are not allowed.',
];
```

**Arabic (lang/ar/email-validation.php):**
```php
return [
    'syntax' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالح.',
    'dns' => 'نطاق :attribute لا يحتوي على سجلات MX صالحة.',
    'disposable' => 'عناوين البريد الإلكتروني المؤقتة غير مسموح بها.',
];
```

## Validation Order

Validations are performed in the following order for optimal performance:

1. **Syntax validation** (fastest)
2. **Disposable email check** (medium)
3. **DNS/MX verification** (slowest)

The validation stops at the first failure to minimize processing time.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mohamed Said](https://github.com/eg-mohamed)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
