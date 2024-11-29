<!-- @format -->

# Query Builder

-

```php
// Non-fluent:
$users = DB::select(['table' => 'users', 'where' => ['type' => 'donor']]);
// Fluent:
$users = DB::table('users')->where('type', 'donor')->get();

```

## Basic Usage of the DB Facade

- The DB facade is used both for query builder chaining and for simpler **raw** queries
- **unchained calls** to DB Facade **returns an array** for any nonchained method that returns (or can return) multiple rows
- For chained calls, at the end of your chain you’ll use some method—likely `get()` to trigger the actual execution of the query you’ve just built
- this (chained calls) will **return a collection** of stdClass objects **instead of an array**.

```php
// DB::statement('SQL statement here')
DB::statement('drop table users');
// Raw select, and parameter binding
DB::select('select * from contacts where validated = ?', [true]);
// Select using the fluent builder
$users = DB::table('users')->get();
// Joins and other complex calls
DB::table('users')
 ->join('contacts', function ($join) {
 $join->on('users.id', '=', 'contacts.user_id')
 ->where('contacts.type', 'donor');
 })
 ->get();
```

#### Raw updates

Updates look like this:

```php
$countUpdated = DB::update(
 'update contacts set status = ? where id = ?',
 ['donor', $id]
);
```

#### Raw deletes

And deletes look like this:

```php
$countDeleted = DB::delete(
'delete from contacts where archived = ?',
[true]
);
```

## Query Builder and Collections

- **Collection** is like a PHP array with superpowers, allowing you to run `map()`, `filter()`, `reduce()`,` each()`, and much more on your data

- The DB facade returns an instance of **Illuminate\Support\Collection** and Eloquent returns an instance of **Illuminate\Database\Eloquent\Collection** both extends **Illuminate\Database\Eloquent\Collection**

The query builder methods can be categorized into

1. ending/returning methods.
2. constraining methods
3. modifying methods
4. conditional methods

### Ending/returning

- These methods stop the query chain and trigger the execution of the SQL query
- Without one of these at the end of the query chain, your return will always just be an instance of the query builder

1. `get()`: Gets all results for the built query as a **collection**

   ```php
   $emails = DB::table('contacts')
   ->select('email', 'email2 as second_email')
   ->get();
   ```

2. `first()`, `firstOrFail()`:

- Gets only the first result, (like a `get()`, but with a `LIMIT 1` added)
- `first()` fails silently if there are no results, whereas `firstOrFail()` will throw an exception
- If you pass an array of column names to either method, it will return the data for just those columns instead of all columns.
  ```php
  $emails = DB::table('contacts')
  ->select('email', 'email2 as second_email')
  ->first(['title','phone']);
  ```

3. `find(id)`, `findOrFail(id)`:

- Gets only the first result, (like a `get()`, but with a `LIMIT 1` added)
- `find()` fails silently if there are no results, whereas `findOrFail()` will throw an exception
- If you pass an array of column names to either method, it will return the data for just those columns instead of all columns.
  ```php
    $contactFive = DB::table('contacts')->find(5);
  ```

4. `value()`:

- Plucks just the value from a single field from the **first** row
- Like `first()`, but if you only want a single column

  ```php
  $newestContactEmail = DB::table('contacts')
   ->orderBy('created_at', 'desc')
   ->value('email')
  ```

4. `count()`:

- Returns an integer count of all of the matching results:

  ```php
  $countVips = DB::table('contacts')
   ->where('vip', true)
   ->count();
  ```

4. `min()`, `max()`:

- Returns the minimum or maximum value of a particular column

  ```php
  $highestCost = DB::table('orders')->max('amount');
  ```

4. `dd()`, `dump()`:

- Dumps the underlying SQL query and the bindings, and, if using `dd()`, ends the script

  ```php
  DB::table('users')->where('name', 'Wilbur Powery')->dd();
  ```

4. `explain()`:

- returns an explanation of how SQL will execute the query
- can use it alongside the `dd()` or `dump()` methods to debug your query

  ```php
  User::where('name', 'Wilbur Powery')->explain()->dd();
  ```

### Constraining methods

These methods take the query as it is and constrain it to return a smaller subset of possible data:

1. `select()`: Allows you to choose which columns you’re selecting:

   ```php
   $emails = DB::table('contacts')
   ->select('email', 'email2 as second_email')
   ->get();
   // Or
   $emails = DB::table('contacts')
   ->select('email')
   ->addSelect('email2 as second_email')
   ->get();
   ```

2. `where()`:

- Allows you filter/limit the scope of what’s being returned.
- takes three parameters by default - `column name`, `comparison operator`, and `value`
- However, if your comparison is =, you can drop the second operator
- to combine `where()` statements with **AND**, you can either

  - chain them after each other, or
  - pass an array of arrays

    ```php
    $newContacts = DB::table('contact')->where('created_at', '>', now()->subDay())->get();

    // drop "=" comparator
    $vipContacts = DB::table('contacts')->where('vip',true)->get();
    // combine where() by chaining
    $newVips = DB::table('contacts')->where('vip', true)->where('created_at', '>', now()->subDay());
    // combine where() with array
    $newVips = DB::table('contacts')->where([
        ['vip', true],
        ['created_at', '>', now()->subDay()],
    ]);
    ```

3. `orWhere()`: Creates simple OR WHERE statements:

   ```php
   $priorityContacts = DB::table('contacts')
   ->where('vip', true)
   ->orWhere('created_at', '>', now()->subDay())
   ->get();

   $contacts = DB::table('contacts')
   ->where('vip', true)
   ->orWhere(function ($query) {
   $query->where('created_at', '>', now()->subDay())
   ->where('trial', false);
   })
   ->get();
   ```

**NOTE**: How to combine `orWhere()` and `where()`

    ```php
    $canEdit = DB::table('users')
        ->where('admin', true)
        ->orWhere('plan', 'premium')
        ->where('is_plan_owner', true)
        ->get();

    // Do this
    $canEdit = DB::table('users')
        ->where('admin', true)
        ->orWhere(function ($query) {
            $query->where('plan', 'premium')
            ->where('is_plan_owner', true);
        })
        ->get();

    ```

4. `whereBetween(colName, [low, high])`: Allows you to scope a query to return only rows where a column is between two values (inclusive of the two values):

   ```php
   $mediumDrinks = DB::table('drinks')->whereBetween('size', [6, 12])->get();
   ```

5. `whereIn(colName, [1, 2, 3])`: Allows you to scope a query to return only rows where a column value is in an explicitly provided list of options:

   ```php
   $closeBy = DB::table('contacts')->whereIn('state', ['FL', 'GA', 'AL'])->get();
   ```

6. `whereNull(colName)`, `whereNotNull(colName)`: Allows you to select only rows where a given column is NULL or is NOT NULL,
   respectively

   ```php
   $mediumDrinks = DB::table('drinks')->whereNull('size')->get();
   ```

7. `whereRaw()`: Allows you to pass in a raw, unescaped string to be added after the **WHERE** statement

   ```php
   $goofs = DB::table('contacts')->whereRaw('id = 12345')->get();
   ```

8. `distinct()`:

- Selects only rows where the selected data is unique when compared to the other rows in the returned data
- Usually this is paired with `select()`, because if you use a primary key, there will be no duplicated rows

  ```php
  $commenters = DB::table('users')
  ->whereExists(function ($query) {
      $query->select('id')
      ->from('comments')
      ->whereRaw('comments.user_id = users.id');
  })
  ->get();
  ```

9. `whereBetween(colName, [low, high])`: Allows you to scope a query to return only rows where a column is between two values (inclusive of the two values):

   ```php
   $mediumDrinks = DB::table('drinks')->whereBetween('size', [6, 12])->get();
   ```

### Modifying methods

These methods change the way the query’s results will be output, rather than just limiting its results

1. `orderBy(colName, direction)`:

- Sorts/orders the results.
- The second parameter may be either asc (the default, ascend‐
  ing order) or desc (descending order)

  ```php
  $contacts = DB::table('contacts')->orderBy('last_name', 'asc')->get();
  ```

2. `groupBy()`, `having()`, `havingRaw()`:

- Groups your results by a column.
- `having()` and `havingRaw()` allow you to filter your results based on properties of the groups

  ```php
    $populousCities = DB::table('contacts')
                        ->groupBy('city')
                        ->havingRaw('count(contact_id) > 30')
                        ->get();
  ```

3. `skip()`, `take()`:

- Most often used for pagination
- `take()` allows you to define how many rows to return
- `skip()` allows you to define an **offset** or how many to skip **before starting** the return

  ```php
    // returns rows 31-40
    $page4 = DB::table('contacts')->skip(30)->take(10)->get();
  ```

4. `latest(colName)`, `oldest(colName)`:

- Sorts by the passed column (or **created_at** if no column name is passed)
- `latest()` - sorts in descending
- `oldest()` - sorts in ascending

  ```php
  $contacts = DB::table('contacts')->orderBy('last_name', 'asc')->get();
  ```

5. `inRandomOrder()`:

- Sorts the result randomly.

### Conditional methods

There are **2 methods** that allow you to conditionally apply their “contents” (a closure you pass to them) based on the Boolean state of a value you pass in:

1. `when(condition)`:

- just like **if, else**
- If first parameter is truthy, the query modification returned by the 2nd parameter (closure) is applied;
- You can also pass a third parameter, another closure, which will be applied only if the first parameter is falsy

  ```php
    $status = request('status'); // Defaults to null if not set
    $posts = DB::table('posts')
        ->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->get();
  ```

2. `unless(condition)`:

- If the first parameter is falsy, it will run the second closure

```php
  $status = request('status'); // Defaults to null if not set
  $posts = DB::table('posts')
      ->unless($status, function ($query) use ($status) {
          return $query->where('status', $status);
      })
      ->get();
```

### Writing raw queries inside query builder methods with DB::raw

You can pass in the result of a `DB::raw()` call to almost **any method** in the query builder

```php
$contacts = DB::table('contacts')
 ->select(DB::raw('*, (score * 100) AS integer_score'))
 ->get();

```

### Joins

- The `join()` method creates an **inner join**
- The `leftJoin()` method creates an **left join**
- The `rightJoin()` method creates an **right join**

```php
$users = DB::table('users')
 // Joins CONTACTS and USERS tables where user.id match contact.user_id
 ->join('contacts', 'users.id', '=', 'contacts.user_id')
 // get all columns in USERS tables but only name and status from CONTACTS table
 ->select('users.*', 'contacts.name', 'contacts.status')
 ->get();
```

- you can alo create more complex joins by passing a closure into the join() method:

```php
DB::table('users')
 ->join('contacts', function ($join) {
        $join->on('users.id', '=', 'contacts.user_id')
        ->orOn('users.id', '=', 'contacts.proxy_user_id');
    })
    ->get();
```

### Unions

You can union two queries (join their results together into one result set) by creating them first and then using the `union()` or `unionAll()` method

```php
$first = DB::table('contacts')
 ->whereNull('first_name');
$contacts = DB::table('contacts')
 ->whereNull('last_name')
 ->union($first)
 ->get();
```

### Inserts

- pass it as an array to insert a single/multiple row or
- use `insertGetId()` to get the newly created instance Id

```php
$id = DB::table('contacts')->insertGetId([
    'name' => 'Abe Thomas',
    'email' => 'athomas1987@gmail.com',
]);
DB::table('contacts')->insert([
    ['name' => 'Tamika Johnson', 'email' => 'tamikaj@gmail.com'],
    ['name' => 'Jim Patterson', 'email' => 'james.patterson@hotmail.com'],
]);
```

### Updates

- pass it as an array of parameters
- commonly used with `where()` method
- You can also quickly **increment** and **decrement** columns using the `increment()` and `decrement()`

```php
DB::table('contacts')
 ->where('points', '>', 100)
 ->update(['status' => 'vip']);

 // decrement/increment by 1 or by specified factor
DB::table('contacts')->increment('tokens', 5);
DB::table('contacts')->decrement('tokens');
```

### Deletes

- Build your query and then end it with `delete()`
- You can also **truncate** the table, which **deletes every row** and also **resets the autoincrementing ID**

```php
DB::table('users')
 ->where('last_login', '<', now()->subYear())
 ->delete();

 DB::table('contacts')->truncate();
```

### JSON operations

If you have JSON columns, you can update or select rows based on aspects of the JSON structure by using the **arrow syntax** (->) to traverse children

```php
// Select all records where the "isAdmin" property of the "options"
// JSON column is set to true
DB::table('users')->where('options->isAdmin', true)->get();

// Update all records, setting the "verified" property
// of the "options" JSON column to true
DB::table('users')->update(['options->isVerified', true]);
```


