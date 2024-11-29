<!-- @format -->

# Introduction to Eloquent

Eloquent is an **ActiveRecord ORM**

- **ORM** in that it’s a database **abstraction layer** that provides a single interface to interact with multiple database types (MySQL, PostgreSQL, MongoDB)
- **ActiveRecord** in that a **single** Eloquent **Model** class can provide the ability to
  - interact with the table as a whole ( e.g. `User::all()` gets all users, `User::where('name','obi')` ),
    AND
  - also representing an individual table row ( e.g., $sharon = new User() )
- Additionally, each instance is capable of managing its own persistence; you can call `$sharon->save()` or `$sharon->delete()`.

```php
// In a controller
public function save(Request $request)
{
    // Create and save a new contact from user input
 $contact = new Contact();
 $contact->first_name = $request->input('first_name');
 $contact->email = $request->input('email');
 $contact->save();
 return redirect('contacts');
}
```

## Creating and Defining Eloquent Models

- Run the artisan command to create a model
- to automatically create a migration when you create your model, pass the `-m` or `--migration` flag

```php
php artisan make:model Contact
// to automatically create a migration when you create
php artisan make:model Contact --migration
```

### Table name

- The default behavior for table names is that Laravel **snake_cases** and **pluralizes** your class name
  - so **CartItems** Model would access a table named **cart_items**
  - assign a custom table name via `protected $table` property to explicitly specify target table

```php
protected $table = 'contacts_secondary';
```

### Primary key

- Laravel assumes, by default, that each table will have an **autoincrementing intege**r primary key, and it will be named **id**
- change the name of your primary key via `protected $table` property
- And if you want to set it to be nonincrementing, use: `public $incrementing = false`;

```php
protected $primaryKey = 'contact_id';
public $incrementing = false;
```

### Print summmary of Eloquent Model

- As your project grows, it may become a bit challenging to keep track of each model’s
  definition, attributes, and relations.
- The `model:show` command can help with this

```php
// summarize Post
php artisan model:show Post
```

### Timestamps

- Eloquent expects every table to have `created_at` and `updated_at` timestamp columns.
- If your table won’t have them, disable the $timestamps functionality:

```php
public $timestamps = false;
```

- You can customize the format for your timestamps by setting the `$dateFormat` class property to a custom string
- The string will be parsed using PHP’s `date()` syntax

```php
 protected $dateFormat = 'U';
```

## Retrieving Data with Eloquent

- Most of the time when you pull data from your database with Eloquent, you’ll use **static calls** on your Eloquent model

```php
$allContacts = Contact::all();
```

### Chunking responses with chunk()

- makes it possible to break your requests into smaller pieces (chunks) and process them in batches to **save memory**

```php
//  split a query into “chunks” of 100 records each
Contact::chunk(100, function ($contacts) {
 foreach ($contacts as $contact) {
 // Do something with $contact
 }
});
```

## Insert Data with Eloquent

1. `make()`

- creates an instance of the model
- `save()` must be called to save the instance to database

```php
$contact = new Contact;
$contact->name = 'Ken Hirata';
$contact->email = 'ken@hirata.com';
$contact->save();
// or
$contact = new Contact([
  'name' => 'Ken Hirata',
  'email' => 'ken@hirata.com',
]);
$contact->save();
// or
$contact = Contact::make([
  'name' => 'Ken Hirata',
  'email' => 'ken@hirata.com',
]);
$contact->save();
```

2. `create()` ans mass assignments

- to save instance automatically use `create()` method
- every property you set via `Model::create()` has to be approved for “mass assignment” in the Model class `$fillable` property

```php
$contact = Contact::create([
 'name' => 'Keahi Hale',
 'email' => 'halek481@yahoo.com',
]);

// in Models/Contact.php
class Contact extends Model
{
  //  approved for mass assignments
    protected $fillable = [
        'name',
        'email',
    ];
    // nonfillable properties can only be changed by direct assignment not mass assignment
    protected $guarded = ['id', 'created_at', 'updated_at', 'owner_id'];
}
```

3. `firstOrCreate()` and `firstOrNew()`

- Get an instance but if it doesn’t exist, create it
- `firstOrCreate()` will **save** that instance to the database and then return it, while `firstOrNew()` will return it **without saving** it

```php
$contact = Contact::firstOrCreate(['email' => 'luis.ramos@myacme.com']);
```

## Updating with Eloquent

- Updating records looks very similar to inserting.
- You can also update one or more Eloquent records by passing an array to the `update()` method

```php
$contact = Contact::find(1);
$contact->email = 'natalie@parkfamily.com';
$contact->save();

// Updating one or more Eloquent records by passing an array to the update() method
Contact::where('created_at', '<', now()->subYear())
 ->update(['longevity' => 'ancient']);
```

## Deleting with Eloquent

1. **Normal deletes**

- The simplest way to delete a model record is to call the delete() method on the
  instance
- However, you can pass an ID or an array of IDs to the model’s destroy()

```php
$contact = Contact::find(5);
$contact->delete()

// delete all of the results of a query
Contact::where('amount', '<', 100)->delete()

Contact::destroy(1);
// or
Contact::destroy([1, 5, 7]);
```

2. **Soft deletes**

- Soft deletes mark database rows as deleted **without actually deleting them from the database**

- with this, you can **archive** your deleted items for later inspection or even recovery with (optional) **soft deletes**
- The hard part about handcoding an application with soft deletes is that **every query you ever write will need to exclude the soft-deleted data**
- if you use Eloquent’s soft deletes, every query you ever make will be scoped to ignore soft deletes by default, unless you explicitly ask to bring them back
- Eloquent’s soft delete functionality requires a `deleted_at`

**Enabling soft deletes.**

- You enable soft deletes by doing two things:

1. adding the **deleted_at** column using `$table->softDeletes()` in a migration
2. importing the `SoftDeletes` trait in the model

- Once you make these changes, every `delete()` and `destroy()` call will now set the
  deleted_at column on your row

```php
// create_contacts_migration-----.php
Schema::table('contacts', function (Blueprint $table) {
 $table->softDeletes();
});

// Models/Contact.php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Contact extends Model
{
 use SoftDeletes; // use the trait
}
```

### Include soft deletes in query result

- use the `withTrashed()` method to add soft-deleted items to a query
- use the `trashed()` method to check if a particular instance has been softdeleted
- use the `onlyTrashed()` method to get only soft-deleted items in query

```php
// add soft-deleted items to a query
$allHistoricContacts = Contact::withTrashed()->get();

// check if instance has been softdeleted
if ($contact->trashed()) {
 // do something
}

//  get only soft-deleted items
$deletedContacts = Contact::onlyTrashed()->get();
```

### Restoring soft-deleted entities

- run `restore()` on an instance or a query to to restore a soft-deleted item

```php
// restore instance
$contact->restore();

// restore all query results
Contact::onlyTrashed()->where('vip', true)->restore();
```

### Force-deleting soft-deleted entities

- Permanently delete a soft-deleted entity by calling `forceDelete()` on an entity or query

```php
$contact->forceDelete();
// or
Contact::onlyTrashed()->forceDelete();
```
