<!-- @format -->

## Creating tables

- use the `create()` method of the **Schema** facade
- the **first** parameter is the table name
- the **second** is a closure that defines its columns

```php
Schema::create('users', function (Blueprint $table) {
    // Create columns here
    $table->string('firstName');
});
```

- To create or modify a table, use the instance of **Blueprint** that’s passed into your closure

## Dropping tables

- use the dropIfExists() method on Schema

```php
Schema::dropIfExists('contacts');
```
