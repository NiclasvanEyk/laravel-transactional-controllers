# `#[Transactional]` Laravel Controllers

Effortlessly wrap your controller actions with database transactions.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/niclas-van-eyk/laravel-transactional-controllers.svg?style=flat-square)](https://packagist.org/packages/niclas-van-eyk/laravel-transactional-controllers)
[![Total Downloads](https://img.shields.io/packagist/dt/niclas-van-eyk/laravel-transactional-controllers.svg?style=flat-square)](https://packagist.org/packages/niclas-van-eyk/laravel-transactional-controllers)

```php
class ExampleUsageController
{
    #[Transactional]
    public function demo(Request $request)
    {
        User::create($request->all());
        User::create($request->all());

        throw new Exception("Everything will be rolled back!");
    }
}
```

## Installation

You can install the package via composer:

```bash
composer require niclas-van-eyk/laravel-transactional-controllers
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

`laravel-transactional-controllers` solves this, by eliminating the need to wrap the code inside a closure and instead adding the `Transactional` attribute to the controller method:
```php
namespace App\Http\Controllers;

use App\Http\Requests\TransferMoneyRequest;
use App\Models\TransferLog;
use Illuminate\Http\Request;
use NiclasVanEyk\TransactionalControllers\Transactional; // <-- from this package

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

You can also explicitly specify the database connection to use for running the transaction (`config('database.default')` is used by default):

```php
    #[Transactional(connection: 'other')]
    public function store() {}
```

## Limitations

This only works when using controllers:

```php
use NiclasVanEyk\TransactionalControllers\Transactional;

// Works ✅
class RegularController
{
    #[Transactional] 
    public function store() {}
}
Route::post('/regular-store', [RegularController::class, 'store']);

// Works ✅
class InvokableController
{
    #[Transactional]
    public function __invoke() {}
}
Route::post('/invokable-store', InvokableController::class);

// Does not work ❌
Route::post(
    '/invokable-store', 
    #[Transactional]
    function () { /* Will not open a transaction! */},
)
```

## Implementation Details

This package uses Laravels [`ControllerDispatcher`](https://github.com/laravel/framework/blob/master/src/Illuminate/Routing/Contracts/ControllerDispatcher.php) component, which determines how the controller action should be executed. This means we can defer opening a transaction until the last possible moment, preventing **unnecessary transactions from being opened**! If e.g. the validation inside a `FormRequest` fails, or a model is not found when using route model binding, no transaction is started.


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

If you have any ideas for changes, feel free to open issues, PRs or fork the project.


### Local Development

This assumes you already have installed sqlite, PHP, and all composer dependencies locally.

Run tests
```bash
composer test
```

Run formatter
```bash
composer fix-cs
```

Run analysis
```bash
composer analyse
```

Run all of the above at once
```bash
composer ci
```

## Credits

- [Niclas van Eyk](https://github.com/niclas-van-eyk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
