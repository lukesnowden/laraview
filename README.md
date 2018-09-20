
# Laraview

## Installation

You can install the package via Composer:

```
composer require lukesnowden/laraview
````

Next, you must install the service provider to `config/app.php`:

```php
'providers' => [
    // ...
    Laraview\Providers\AppServiceProvider::class,
    // Laraview\Providers\DemoServiceProvider::class
];
```

Uncomment the demo service provider to run a demo test.

And finally run the following command to generate your view files

```cli
php artisan laraview:compile
```
