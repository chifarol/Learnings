<!-- @format -->

# Migrations

- Everything we can do in migrations rely on the methods of the **_Schema_** facade

A single migration file that defines 2 things:

1. modifications desired when running this migration **up**
2. modifications desired when running this migration **down**

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
 }
```

## Creating a migration

- a new migration file is created with `php artisan make:migration`
- The default format for targeting a table is `create_{{ table_name_goes_here }}_table`
- Use the `--table=table_name` flag to specify the target table if you want a custom migration filename
- Use `--create=table_name` prefill the migration with code designed to create a table
- Migrations are always run in order by date that's why every migration filename has a date stamp e.g **2018_10_12_000000_create_users_table.php**

```php
// create new migration file for "users" table
php artisan make:migration create_users_table
// using --table to specify the target table
php artisan make:migration add_votes_to_users_table --table=users
// prefill the migration with code designed to create a table
php artisan make:migration create_users_table --create=users
```

## “Up” and “Down” in Migrations

- When php artisan migrate is called for the **first time**, the system grabs **each** migration, starting at the oldest date, and runs its **up()** method
- But since the migration system also allows you to “undo” your most recent set of migrations, It’ll grab each of them and run its **down()** method
- Insummary, the **up()** method of a migration should “do” its migration, and the **down()** method should “undo” it.

## Squashing migrations

- If you have too many migrations to reason with, you can merge them all into a single SQL file that Laravel will run before it runs any future migrations
- Laravel only runs these dumps if it detects no migrations have been run so far

```php
// Squash the schema but keep your existing migrations
php artisan schema:dump

// Dump the current database schema and delete all existing migrations
php artisan schema:dump --prune

```

## Running Migrations

- Laravel keeps track of which migrations you have run and which you haven’t

```
php artisan migrate
```

- This command runs `up()` method on each “outstanding” migrations starting from the oldest
- It checks whether you’ve run all available migrations, and if you haven’t, it’ll run any that remain

You can also run any of the following commands:

1. **migrate:install** - Creates the database table that keeps track of which migrations you have and haven’t run; this is run automatically when you run your migrations, so you can basically ignore it

2. **migrate:reset** - Rolls back every database migration you’ve run on this instance. It basically calls the `down()` method on each migration file starting from the latest

3. **migrate:refresh** - Rolls back every database migration you’ve run on this instance, and then runs every migration available. It’s the same as running `migrate:reset` followed by `migrate`
4. **migrate:fresh** - Drops all of your tables and runs every migration again. It’s the same as refresh but doesn’t bother with the “down” migrations—it just deletes the tables and then runs the “up” migrations again.
5. **migrate:rollback** - Rolls back just the migrations that ran the last time you ran migrate, or, with the added option --step=n, rolls back the number of migrations you specify.
6. **migrate:status** - Shows a table listing every migration, with a Y or N next to each indicating whether or not it has run yet in this environment.
7. **migrate:refresh** - Rolls back every
