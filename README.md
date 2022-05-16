
# `#[Transactional]` Laravel Routes

Effortlessly wrap your controller actions with database transactions.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/niclas-van-eyk/laravel-transactional-routes.svg?style=flat-square)](https://packagist.org/packages/niclas-van-eyk/laravel-transactional-routes)
[![Total Downloads](https://img.shields.io/packagist/dt/niclas-van-eyk/laravel-transactional-routes.svg?style=flat-square)](https://packagist.org/packages/niclas-van-eyk/laravel-transactional-routes)

```php
class ExampleUsageController
{
    #[Transactional]
    public function demo()
    {
        User::create();
        User::create();

        throw \Exception("Everything will be rolled back!");
    }
}
```

## Installation

You can install the package via composer:

```bash
composer require niclas-van-eyk/laravel-transactional-routes
```

## Background

If you want to make a series of edits to your database, where either _all_ should happen at once, or _none at all_, you typically use database transactions. The example we use here is a user (`$author`) transferring a certain `$amount` of to another user (`$receiver`). We also want to save that the fact that this transfer took place in a separate model (`TransferLog`).

## Usage

Before you might have written something like this:
```php
namespace App\Http\Controllers;

use App\Http\Requests\TransferMoneyRequest;
use App\Models\TransferLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankAccountController 
{
    public function transferMoney(TransferMoneyRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $request->author->balance->decrement($request->amount);
            $request->receiver->balance->increment($request->amount);

            return TransferLog::createFromTransferRequest($request);
        })
    }
}
```

You have to wrap your whole code inside one big closure, explicitly `use` all parameters you inject, and if you want to return something from _inside_ the transaction closure, you end up with this double return, making the code harder to read and your IDE angry.

`laravel-transactional-routes` solves this, by eliminating the need to wrap the code inside a closure and instead adding the `Transactional` attribute to the controller method:
```php
namespace App\Http\Controllers;

use App\Http\Requests\TransferMoneyRequest;
use App\Models\TransferLog;
use Illuminate\Http\Request;
use NiclasVanEyk\TransactionalRoutes\Transactional; // <-- from this package

class BankAccountController 
{
    #[Transactional]
    public function transferMoney(TransferMoneyRequest $request): TransferLog
    {
        $request->author->balance->decrement($request->amount);
        $request->receiver->balance->increment($request->amount);

        return TransferLog::createFromTransferRequest($request);
    }
}
```

No more `use`, double `return`s or your IDE complaining about it not being able to guarantee a correct return type!

You can also explicitly specify the database connection to use for running the transaction (`config('database.default` is used by default):

```php
    #[Transactional(connection: 'other')]
    public function store() {}
```

## Implementation Details

This package uses Laravels lesser known [`ControllerDispatcher`](https://github.com/laravel/framework/blob/master/src/Illuminate/Routing/Contracts/ControllerDispatcher.php) component, which determines how the controller action should be executed. This means we can defer opening a transaction until the last possible moment, preventing **unnecessary transactions from being opened**! If e.g. the validation inside a `FormRequest` fails, or a model is not found when using route model binding, no transaction is started.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Niclas van Eyk](https://github.com/niclas-van-eyk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
