<!-- @format -->

## Using Stacks

- sometimes certain pages,may have specific, unique CSS and JavaScript files they need to load
- **first** you define a `@stack('stack_name')`, in the parent template. Then, in each child that extends the template you can “push” entries onto that stack with `@push('stack_name')/@endpush` which adds them to the **bottom of the stack** in the final render

```php
<!-- resources/views/layouts/app.blade.php -->
<html>
    <head>
        <link href="/css/global.css">
        <!-- the placeholder where stack content will be placed -->
        @stack('styles')

    </head>
    <body>
        <!-- // -->
    </body>
</html>


<!-- resources/views/jobs.blade.php -->
@extends('layouts.app')
@push('styles')
 // push something to the bottom of the stack
 <link href="/css/jobs.css">
@endpush


<!-- resources/views/jobs/apply.blade.php -->
@extends('jobs')
@prepend('styles')
 // push something to the top of the stack
 <link href="/css/jobs--apply.css">
@endprepend
```
