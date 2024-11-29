<!-- @format -->

# Section Title: Setting Up a Laravel Development Environment

How to setup up laravel in local environment

Laravel offers 5 tools for local development:

- Artisan serve,
- Sail,
- Valet,
- Herd, and
- Homestead.

## Steps (using Artisan)

1.  Installing lavarel
    There are 2 ways of "installing" up laravel

    - Installing Laravel with the Laravel Installer Tool

      - composer global require "laravel/installer"
      - laravel new projectName

              OR

    - Installing Laravel with Composer’s create-project Feature
      - composer create-project laravel/laravel projectName

2.  Install dependencies

    - composer install
    - composer dump-autoload (this is usually done automatically by composer)

3.  install npm modules

    - npm install

4.  run npm

    - npm run dev

5.  make a copy of the sample .env.example to a .env file

    - cp .env.example .env

6.  generate encryption key to avoid "No application encryption key has been specified." error

    - php artisan key:generate

7.  Link storage

    - php artisan storage:link

8.  Run server
    - php artisan serve

## Configuration

- core settings of your Laravel application live inside the config folder
- this includes settings for database connection settings, queue
  and mail settings, etc
- Each of these files **returns** a PHP **array**
- Each value in the array is accessible by a config key ( that is comprised of the filename and all descendant keys, separated by dots "." )

```php
// config/services.php
return [
 'sparkpost' => [
 'secret' => 'abcdefg',
 ],
];

```

to access that config variable use

```php
 config('services.sparkpost.secret');
```
