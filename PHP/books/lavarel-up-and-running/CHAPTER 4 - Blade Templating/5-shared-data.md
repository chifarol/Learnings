<!-- @format -->

## Binding Data to Views Using View Composers

- This is useful when you want to pass the same data over and over to multiple views
- **View composer** allows you to define that any time **a particular** view loads, it should have certain data passed to it (without the route definition having to pass that data in explicitly)

1. Using `view()->share()`

- If you want to use `view()->share()`, the best place would be the `boot()` method of a service provider so that the binding runs on every page load
- it makes the variable accessible to every view in the entire application, which can be an overkill sometimes.

```php
// Some service provider e.g App\Providers\AppServiceProvider
public function boot()
{
 // ...
    view()->share('recentPosts', Post::recent());
}
```

## Scoping View composers

- share a variable, but only with a specific views

### Scope view composers using closures

```php
// Some service provider e.g App\Providers\AppServiceProvider
public function boot()
{
 // ...
    view()->composer(['partials.*'], function ($view) {
        $view->with('recentPosts', Post::recent());
    });
}
```

### Scope view composers using Classes

- unfortunately, child templates that extend their parents don't have access to the shared variable within their parent (those children/it's folders have to be specified)

1. There’s no formally defined place for view composers to live, but the docs recommend `App\Http\ViewComposers`

```php
// any file e.g App\Http\ViewComposers\RecentPostsComposer
namespace App\Http\ViewComposers;

use App\Post;
use Illuminate\Contracts\View\View;

class RecentPostsComposer
{
    public function compose(View $view)
    {
    $view->with('recentPosts', Post::recent());
    }
}
```

2. create a custom Service Provider e.g ViewComposerServiceProvider or just use `App\Providers\AppServiceProvider`

```php
// any file e.g App\Providers\AppServiceProvider
public function boot(): void
{
    view()->composer(
    'partials.sidebar',
    \App\Http\ViewComposers\RecentPostsComposer::class
    );
}
```
