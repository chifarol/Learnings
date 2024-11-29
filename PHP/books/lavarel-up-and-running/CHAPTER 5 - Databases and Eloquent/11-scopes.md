<!-- @format -->

# Scopes

Eloquent allow you to define **prebuilt scopes** (filters) that you can use either every time a model is queried (global) or every time you query it with a particular method chain (local)

## Local scopes

- Local scopes are the simplest to understand
- Local scopes are defined in the Model's class e.g \App\Models\Contact
- To define a local scope,
  - just like custom hooks in ReactJS have to start with "use"
  - we add a method to the Eloquent class that begins with lowercase “scope”
  - then contains the PascalCase version of the scope name
  - return the desired query builder

```php
// we can turn this filter query
$activeVips = Contact::where('vip', true)->where('trial', false)->get();
$friends = Contact::where('status', $status)->get();

// to a local scope
$activeVips = Contact::activeVips()->get();
$friends = Contact::status('friend')->get();


// in the Contact Model class
class Contact extends Model
{
 public function scopeActiveVips($query)
 {
    return $query->where('vip', true)->where('trial', false);
 }
 public function scopeStatus($query)
 {
    return $query->where('status', $status);
 }

}
```

### Global scopes

- Difference is that Global scopes **run automatically** whenever the Model is queried, Local scopes have to **specified on every query** to run
- global scope for instance, is what allows every query on a model to ignore the soft-deleted items except when explicitly requested
- There are 2 ways to define a global scope, in each, you’ll register the defined scope in the model’s `booted()` using `static::addGlobalScope()` method

  1. using a closure

  ```php
  class Contact extends Model
    {
        protected static function booted()
        {
            // adds a global scope named 'active'
            static::addGlobalScope('active', function (Builder $builder) {
                $builder->where('active', true);
            });
        }
    }
  ```

  2. using an entire class

  - create class via artisan command

  ```
  php artisan make:scope ActiveScope
  ```

  - override `apply()` method

  ```php
  // App\Models\ActiveScope.php
    class ActiveScope implements Scope
    {
        public function apply(Builder $builder, Model $model): void
        {
            $builder->where('active', true);
        }
    }

  ```

  - add scope in model class

  ```php
  class Contact extends Model
  {
    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope);
    }
  }
  ```

### Removing global scopes

```php
// remove a closure-based scope using it's key
$allContacts = Contact::withoutGlobalScope('active')->get();

// remove a single class-based global scope
Contact::withoutGlobalScope(ActiveScope::class)->get();

// remove multiple class-based global scope
Contact::withoutGlobalScopes([ActiveScope::class, VipScope::class])->get();

// disable all global scopes for a query
Contact::withoutGlobalScopes()->get();
```
