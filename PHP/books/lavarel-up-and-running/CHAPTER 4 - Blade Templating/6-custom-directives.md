<!-- @format -->

## Custom Blade Directives

- You can make your own directives just like `@if`, `@unless`.
- E.g you can make a shortcut `@ifGuest()` to replace `@if (auth()->guest())` and check if user id logged in or `@button('buttonName')` to render a button

```php
public function boot(): void
{
    Blade::directive('ifGuest', function () {
    return "<?php if (auth()->guest()): ?>";
    });

    // with parameter
   Blade::directive('newlinesToBr', function ($expression) {
    return "<?php echo nl2br({$expression}); ?>";
   });
}

//
<div>
    @ifGuest()
        Hello {{$user->name}}
    @endifGuest()

    <p>@newlinesToBr($message->body)</p>
</div>
```

- Blade caches aggressively so custom directives may not be re-created on every page load
