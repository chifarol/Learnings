<!-- @format -->

# Eloquent

- Eloquent is Laravel’s ActiveRecord ORM (Object Relational Mapping)
- It works on a **one class per table** basis, responsible for retrieving, representing, and persisting data in that table

## Configuration

- configuration for database access lives in **cong/database.php** and **.env**
- you can define **multiple “connections”** in **config/databases.php** under "connections" array
- By default there’s one connection per driver but sometimes you might need multiple connections within the same application e.g read from one database and write to another
- Here’s how you ask for a specific DB connection,

```php
// in config/databases.php
return[
    // default connection is 'mysql' if DB_CONNECTION in .env is not different
    'default' => env('DB_CONNECTION', 'mysql'),

    // multiple possible connections
    'connections' => [

            'sqlite' => [
                'driver' => 'sqlite',
                'url' => env('DATABASE_URL'),
                //...others...
            ],

            'mysql' => [
                'driver' => 'mysql',
                'url' => env('DATABASE_URL'),
                // ...others...
            ],
    ]
]

// use sqlite instead of default MYSQL
$users = DB::connection('sqlite')->select('select * from users');
```

### URL Configurations

- Some services like _Heroku_ will provide a URL that contains all of the information you need to connect to the database. e.g

  ```
  mysql://root:password@127.0.0.1/forge?charset=UTF-8
  ```

- You don’t have to write code to parse this URL out; instead, pass it in as the `DATABASE_URL` in **.env** and Laravel will understand it.

### Other Database Conguration Options

In **config/database.php**, you can also

1. configure Redis access
2. customize the table name used for migrations
3. determine the default connection
4. toggle whether non-Eloquent calls return stdClass or array instances etc
