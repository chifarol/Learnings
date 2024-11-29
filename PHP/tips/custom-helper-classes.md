<!-- @format -->

Helper functions in laravel are kinda accessible globally, even in view files

## Creating Custom Classes

- Create a `Helpers.php` file in `app\Helpers` folder

```php
// app\Helpers\Helper.php
class Helper
{
    public static function shout(string $string)
    {
        return strtoupper($string);
    }
}
```

- Create an alias in `config/app.php`

```php
'aliases' => [
     // ... other aliases,
    'Helper' => App\Helpers\Helper::class,
]
```

- composer dump-autoload

- php artisan config:cache (to update config cache)

- use anywhere

```php

<p class="tw-font-semiBold tw-text-18 tw-mb-[1rem]">
    {{ Helper::shout("hey") }}
</p>

// or
class SomeController extends Controller
{

    public function __construct()
    {
        Helper::shout('now i\'m using my helper class in a controller!!');
    }
}

```
