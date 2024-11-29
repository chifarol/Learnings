<!-- @format -->

# Transactions

Database transactions are tools that enable you to wrap up a series of database queries to be **performed in a batch**, which you can choose to **roll back**, undoing the entire series of queries

- Transactions are often used to ensure that **all** or **nothing**
- If one fails or any exceptions are thrown, the ORM will roll back the entire series of queries
- If the transaction closure finishes successfully, all the queries will be committed and not rolled back

```php
DB::transaction(function () use ($userId, $numVotes) {
    // Possibly failing DB query
    DB::table('users')
        ->where('id', $userId)
        ->update(['votes' => $numVotes]);

    // Caching query that we don't want to run if the above query fails
    DB::table('votes')
        ->where('user_id', $userId)
        ->delete();
});
```
- you can also manually begin and end transactions

```php
DB::beginTransaction();

// Take database actions
if ($badThingsHappened) {
 DB::rollBack();
}

// Take other database actions
DB::commit();
```
