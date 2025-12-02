# Email Validation Package - Usage Examples

## Basic Usage in Controllers

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MohamedSaid\EmailValidation\Rules\EmailValidationRule;
use MohamedSaid\EmailValidation\Facades\EmailValidation;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email_validation'],
            'password' => ['required', 'min:8'],
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $validated,
        ]);
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');

        if (EmailValidation::isValid($email)) {
            return response()->json([
                'valid' => true,
                'message' => 'Email is valid and can be used',
            ]);
        }

        $failures = EmailValidation::getFailures($email);

        return response()->json([
            'valid' => false,
            'message' => 'Email validation failed',
            'failures' => $failures,
        ], 422);
    }

    public function detailedCheck(Request $request)
    {
        $email = $request->input('email');
        $results = EmailValidation::validate($email);

        return response()->json([
            'email' => $email,
            'validation_results' => [
                'syntax' => $results['syntax'] ?? null,
                'dns' => $results['dns'] ?? null,
                'disposable' => $results['disposable'] ?? null,
            ],
            'is_valid' => EmailValidation::isValid($email),
        ]);
    }
}
```

## Using with Form Requests

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MohamedSaid\EmailValidation\Rules\EmailValidationRule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', new EmailValidationRule()],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('Email address is required'),
        ];
    }
}
```

## API Testing Examples

### Valid Email

```bash
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@gmail.com",
    "password": "password123"
}

Response: 200 OK
{
    "message": "User created successfully",
    "data": {
        "name": "John Doe",
        "email": "john@gmail.com"
    }
}
```

### Invalid Syntax

```bash
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "invalid-email",
    "password": "password123"
}

Response: 422 Unprocessable Entity
{
    "message": "The email must be a valid email address.",
    "errors": {
        "email": ["The email must be a valid email address."]
    }
}
```

### Disposable Email

```bash
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "test@tempmail.com",
    "password": "password123"
}

Response: 422 Unprocessable Entity
{
    "message": "Disposable email addresses are not allowed.",
    "errors": {
        "email": ["Disposable email addresses are not allowed."]
    }
}
```

### Invalid DNS/MX Records

```bash
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "user@nonexistent-domain-xyz.com",
    "password": "password123"
}

Response: 422 Unprocessable Entity
{
    "message": "The email domain does not have valid MX records.",
    "errors": {
        "email": ["The email domain does not have valid MX records."]
    }
}
```

## Configuration Examples

### Enable Only Syntax Validation

```php
return [
    'validations' => [
        'syntax' => true,
        'dns' => false,
        'disposable' => false,
    ],
];
```

### Enable Only Disposable Check

```php
return [
    'validations' => [
        'syntax' => false,
        'dns' => false,
        'disposable' => true,
    ],
];
```

### Custom Error Messages in Arabic

```php
return [
    'messages' => [
        'syntax' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالح.',
        'dns' => 'نطاق :attribute لا يحتوي على سجلات MX صالحة.',
        'disposable' => 'عناوين البريد الإلكتروني المؤقتة غير مسموح بها.',
    ],
];
```

## Advanced Usage

### Validating Multiple Emails

```php
use MohamedSaid\EmailValidation\Facades\EmailValidation;

$emails = ['user1@example.com', 'user2@tempmail.com', 'user3@gmail.com'];
$results = [];

foreach ($emails as $email) {
    $results[$email] = [
        'valid' => EmailValidation::isValid($email),
        'details' => EmailValidation::validate($email),
    ];
}

return response()->json($results);
```

### Conditional Validation

```php
public function rules(): array
{
    $rules = ['required', 'email'];

    if (config('app.strict_email_validation')) {
        $rules[] = new EmailValidationRule();
    }

    return [
        'email' => $rules,
    ];
}
```

## Filament Integration

```php
<?php

namespace App\Filament\Resources\UserResource\Schemas\Components;

use Filament\Forms\Components\TextInput;
use MohamedSaid\EmailValidation\Rules\EmailValidationRule;

TextInput::make('email')
    ->label(__('Email Address'))
    ->email()
    ->required()
    ->rules([new EmailValidationRule()])
    ->unique(table: 'users', ignoreRecord: true)
    ->maxLength(255)
```

## Testing Your Implementation

```php
<?php

use MohamedSaid\EmailValidation\Facades\EmailValidation;

test('validates valid email', function () {
    expect(EmailValidation::isValid('user@gmail.com'))->toBeTrue();
});

test('rejects disposable email', function () {
    expect(EmailValidation::isValid('test@tempmail.com'))->toBeFalse();
    expect(EmailValidation::getFailures('test@tempmail.com'))->toContain('disposable');
});

test('rejects invalid syntax', function () {
    expect(EmailValidation::isValid('invalid-email'))->toBeFalse();
    expect(EmailValidation::getFailures('invalid-email'))->toContain('syntax');
});
```
