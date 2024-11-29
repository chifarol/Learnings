<!-- @format -->

# Laravel Dump Server

- One common method of debugging the state of your data during development is to use Laravel’s `dump()` helper

- You can enable the Laravel dump server, which catches those `dump()` statements and **displays them in your console instead of rendering them to the page**
- This is very useful when debugging APIs

```php
$ php artisan dump-server
```

```php
Route::get('/', function () {
 dump('Dumped Value');
 return 'Hello World';
});

GET http://myapp.test/
--------------------
 ------------ ---------------------------------
 date Tue, 18 Sep 2018 22:43:10 +0000
 controller "Closure"
 source web.php on line 20
 file routes/web.php
 ------------ ---------------------------------

```
