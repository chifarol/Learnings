<!-- @format -->

## Seeding

- There’s a **database/seeders** folder that comes with a DatabaseSeeder class, which has a `run()` method

There are two primary ways to run the seeders:

1. Run seeder along with a migration

```php
php artisan migrate --seed
php artisan migrate:refresh --seed
```

2. Run seeder separately

```php
php artisan db:seed
php artisan db:seed VotesTableSeeder
```

## Creating a Seeder

To create a seeder,

- use the `make:seeder` Artisan command
- then call the just created seeder Class in database/seeders/DatabaseSeeder.php

```php
php artisan make:seeder ContactsTableSeeder

// database/seeders/DatabaseSeeder.php
...
public function run(): void
{
 $this->call(ContactsTableSeeder::class);
}
```

## Inserting database records in a custom seeder

-

```php
// database/seeders/ContactsTableSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class ContactsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('contacts')->insert([
        'name' => 'Lupita Smith',
        'email' => 'lupita@gmail.com',
        ]);
    }
}
```

## Model Factories

For truly functional seeds, you’ll likely want to loop over some sort of random generator and run the insert() like above. This is where **Factories** come in

- Model factories define one (or more) patterns for creating **fake entries** for your database tables.
- Theoretically you can name these factories anything you like, but naming the factory
  after your Eloquent class is the most idiomatic approach

- If you follow a different convention to name your factories, you can set the factory class name in the related model.

## Creating a model factory

1. create the factory class

```php
php artisan make:factory ContactFactory

// generates a new file within the database/factories directory called ContactFactory.php
```

2. implement a `definition()` method in the Factory

```php
namespace Database\Factories;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
        'phone' => "009388283",
            // you can also use a faker
        'name' => fake()->name(),
        'email' => fake()->unique()->email(),
        ];
    }
}
```

3.  In your model add the `use HasFactory` Trait

```php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // you can override the `newFactory()` method
    protected static function newFactory()
    {
        return \Database\Factories\Base\ContactFactory::new();
    }

}

```

- HasFactory trait provides an already made `factory()` method (which uses Laravel conventions to determine the proper factory for the model).
- It will look for a factory in `Database\Factories` namespace that has a **class name** that matches ('**model name**+Factory') e.g A factory for `User` model will be named `UserFactory`
- If you don’t follow these conventions, you can override the `newFactory()` method in your model to specify the factory class that should be used

4. call the static `factory()` method on the model, to create an instance of e.g Contact in our **seeding** and **testing**

```php
// Create one
$contact = Contact::factory()->create();
// Create many
Contact::factory()->count(20)->create();
```

With this steps we no longer need to call `ContactFactory` directly, just call `factory()->create()` method on the model class

## Using Fakers

- The [Faker](https://oreil.ly/gxnrI) library, which is globally available in Laravel via the fake() helper
- Faker makes it easy to randomize the creation of structured fake data
- use Faker’s `unique()` method to guarantee that the given entry is unique compared to the other randomly generated values

## Using a model factory

- To create an object, we use the factory() method on the model e.g inside a seeder Class
- Then we can run one of two methods on it: `make()` or `create()`
- Both methods generate and **returns** instance(s) of this specified model, using the `definition()` in the factory class
- The difference is that `make()` creates the instance but doesn’t (yet) save it to the database, whereas `create()` saves it to the database instantly.

```php
$post = Post::factory()->create([
 'title' => 'My greatest post ever',
]);
// Pro-level factory; but don't get overwhelmed!
$users = User::factory()->count(20)->has(Address::factory()->count(2))->create()
```

- To generate and return more than one instance with a model factory use the `count()`
- To overriding properties when calling a model factory, pass an array to either make() or create() e.g `->create(['name]=>'John)`

### Attaching relationships when defining model factories

- to create a related item along with the item you’re creating. You can call the `factory()` method on the related model to pull its ID,
- to use values from other parameters in a factory, pass a **closure** where a single parameter ($attributes) is passed, which contains the array form of the generated item up until that point

```php
class ContactFactory extends Factory
{
 public function definition(): array
 {
     return [
         'name' => 'Lupita Smith',
        'email' => 'lupita@gmail.com',
        // Company::factory() returns ID of newly created Company record
        'company_id' => Company::factory(),
        'company_size' => function (array $attributes) {
            // Uses the "company_id" property generated above
        return Company::find($attributes['company_id'])->size;
 },

    ];
 }
}
```

- `YourRelatedModel::factory()` will force an instance to be created each time and you may not want that, sometimes you want to attch Ids from existing model instances, in that case use

```php
class ContactFactory extends Factory
{
 public function definition(): array
 {
     return [
         'name' => 'Lupita Smith',
        'email' => 'lupita@gmail.com',
        // pick random Id from existing Company instances/records
        'company_id' => fake()->randomElement(Company::pluck('id')),
        'company_size' => function (array $attributes) {
            // Uses the "company_id" property generated above
        return Company::find($attributes['company_id'])->size;
        },
    ];
 }
}
```

### Using the same model as the relationship in complex factory Setups

- Say you have 3 factories that uses a **User** instance, you can enforce them to use same user instance
- With the `recycle($obj)` method, you can instruct that every factory called up the chain uses the same instance of a given object ($obj)

```php
$user = User::factory()->create();
$trip = Trip::factory()->recycle($user)->create();
```

### Attaching hasMany and belongsTo items when generating model factory instances

- `has()` allows us to define that the instance we’re creating **“has”** children or other items in a `hasMany` type relationship

let’s assume a Contact has many Addresses.

```php
// Attach 3 addresses
Contact::factory()
 ->has(Address::factory()->count(3))
 ->create()

```

- `for()` allows us to define that the instance we’re creating **“belongsTo”** another item

let’s assume a Contact has many Addresses.

```php
// Use an existing parent model (assuming we already have it as $contact)
Address::factory()
    ->count(3)
    ->for($contact)
    ->create();

// Specify details about the created parent
Address::factory()
    ->count(3)
    ->for(Contact::factory()->state([
    'name' => 'Imani Carette',
    ]))
    ->create();

```

## Defining and accessing multiple model factory states

- by default a factry's `definition()` method is called
- sometimes you need more than one factory for a class of object to spin up different types of records
- We can use the `state()` method to define a second factory state
- if we return false, the operation will cancel and the `save()` or `update()` will be canceled

```php
class ContactFactory extends Factory
{
 protected $model = Contact::class;

public function definition(): array
 {
    return [
        'name' => fake()->name,
        'email' => fake()->email,
        'vip' => false,
        // Uses the "company_id" property from the $attributes
        'company_size' => function () use ($attributes) {
            return Company::find($attributes['company_id'])->size;
        },
    ];

 }
 public function vip()
 {
    $this->state(function (array $attributes) {
        return [
            'name' => fake()->name,
            'email' => fake()->email,
            'vip' => true,
        ];
    });
 }
}

public function adminUser()
{
    return [
        'name' => "admin",
        'email' => "admin@gmail.com",
        // assuming default is_admin is 0
        'is_admin' => "1",
        'vip' => true,
    ];
}
```

```php
// create regular contacts
$regular_contacts = Contact::factory()->count(3)->create();

// create VIP contacts
$vips = Contact::factory()->count(3)->vip()->create();

$vips = Contact::factory()->count(1)->adminUser()->create();
```
