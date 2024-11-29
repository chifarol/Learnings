<!-- @format -->

Helper functions in laravel are kinda accessible globally, even in view files

## Creating Custom Helper functions

- Create a `helpers.php` file in `app` folder

```php
// app/helpers.php
function shout(string $string)
    {
        return strtoupper($string);
    }
```

- Load the custom `helpers.php` it up with composer

```php
"autoload": {
    "classmap": [
        ...
    ],
    "psr-4": {
        "App\\": "app/"
    },
    "files": [
        "app/helpers.php" // <---- ADD THIS
    ]
},
```

- composer dump-autoload

- php artisan config:cache (to update config cache)

- use anywhere like:

```php

<p class="tw-font-semiBold tw-text-18 tw-mb-[1rem]">
    {{ (shout("hey")) }}
</p>

// or
class SomeController extends Controller
{

    public function __construct()
    {
        shout('now i\'m using my helper function in a controller!!');
    }
}

```
