<!-- @format -->

# Customizing Field Interactions with Accessors, Mutators, and Attribute Casting

## Accessors

- E.g for a Model/Table with `firstName` and `lastName`, you can define a custom `fullName` to be accessed like the former
- Accessors allow you to define custom attributes on your Eloquent models for when you are **reading** data from the model instance
- With this you can decorate existing or custom table columns
- You define an accessor by creating a method on your model with the name of your custom property, but **camelCased**
- this method needs to have return type **Illuminate\Database\Eloquent\Casts\Attribute**

```php
// Model definition:
class Contact extends Model
{
    // define attributes that never existed in the database
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }

    // define attributes that already existed in the database
    // assign a default value to name column on when queried
    protected function name(): Attribute
    {
        // $value represents ->name attribute
        return Attribute::make(
            get: fn (string $value) => $value ?: '(No name provided)',
        );
    }

}

```

- use the **snake_case** of the camelCase method name

```php
// Accessor usage:
$name = $contact->name;
$fullName = $contact->full_name;
```

## Mutators

- if **accessors are getters** then **mutators are setters**
- you can use them to modify the process of **writing data** to existing columns, or to allow for setting columns that don’t exist in the database
- Works just like with accessors but instead of the get parameter, we’ll be setting the `set` parameter
- So any time the Model is saved the column will be modified by the mutator

```php
// Defining the mutator
class Order extends Model
{
// setting the value of a existing attribute

protected function amount(): Attribute
{
    return Attribute::make(
        set: fn (string $value) => $value > 0 ? $value : 0,
    );
}
}
// setting the value of a nonexistent attribute
protected function workgroupName(): Attribute
{
    return Attribute::make(
        set: fn (string $value) => [
            'email' => "{$value}@ourcompany.com",
        ],
    );
}
// amount will be saved as 0 in database
$order->amount = -15;
// setting our custom workgroup_name, writes a new email to database
$order->workgroup_name = 'jstott';

```

## Attribute casting

- instead of writing accessors for "type" casting, e.g converting stringified arrays to array, you can use built-in attribute casting

```php
class Contact extends Model
{
 protected $casts = [
    'vip' => 'boolean',
    'children_names' => 'array',
    'birthday' => 'date',
    'subscription' => SubscriptionStatus::class
 ];
}
```

### Custom attribute casting

- A custom cast type can be defined as a regular PHP class that extends `CastsAttributes` with a `get` and `set` method

```php
namespace App\Casts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
class Encrypted implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return Crypt::decrypt($value);
    }
    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return Crypt::encrypt($value);
    }
}
```

```php
class Contact extends Model
{
    protected $casts = [
     'ssn' => \App\Casts\Encrypted::class,
    ];
}
```

### List of cast

- array
- AsStringable::class
- boolean
- collection
- date
- datetime
- immutable_date
- immutable_datetime
- decimal:<precision>
- double
- encrypted
- encrypted:array
- encrypted:collection
- encrypted:object
- float
- hashed
- integer
- object
- real
- string
- timestamp
