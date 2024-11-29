<!-- @format -->

# Blade

- Laravel's custom templating engine is called **Blade** ( inspired by .NET’s
  **Razor** engine)

- It uses `{{ }}` curly braces for its “echo”
- It supports “directives” prefixed with an `@`
- directives are to be used for **control structures** and also for **inheritance** and any **custom functionality** you want to add

## Echoing Data

- `{{ $variable }}` is similar to `<?php echo $variable ?>` or `<?= $variable ?>`
- Difference is: blade escapes all {{echoes}} by default using PHP’s `htmlentities()` equivalent to `<?= htmlentities($variable) ?>`
- If you want to echo without the escaping, use `{!! ... !!}` instead

### So how does Laravel know when you’re writing Blade versus other template engines (like Handlebars)

- Prefix any conflicting syntax from other frameworks with `@`
  OR
- wrap the content with the `@verbatim` directive

```php
// Parsed as Blade; the value of $bladeVariable is echoed to the view
{{ $bladeVariable }}
// @ is removed and "{{ handlebarsVariable }}" echoed to the view directly
@{{ handlebarsVariable }}

```

### Control Structures

#### Conditionals

1. `@if`

```php
@if (count($talks) === 1)
 There is one talk at this time period.
@elseif (count($talks) === 0)
 There are no talks at this time period.
@else
 There are {{ count($talks) }} talks at this time period.
@endif
```

2. `@unless`

- direct opposite of `@if`

```php
@unless ($user->hasPaid())
 You can complete your payment by switching to the payment tab.
@endunless
```

#### Loops

3. `@for`

```php
@for ($i = 0; $i < $talk->slotsCount(); $i++)
 The number is {{ $i }}<br>
@endfor
```

4. `@foreach`

```php
@foreach ($talks as $talk)
 • {{ $talk->title }} ({{ $talk->length }} minutes)<br>
@endforeach
```

5. `@while`

```php
@while ($item = array_pop($items))
 {{ $item->orSomething() }}<br>
@endwhile
```

6. `@forelse`

```php
@forelse ($talks as $talk)

  {{ $talk->title }} ({{ $talk->length }} minutes)<br>

@empty

 No talks this day.

@endforelse
```

##### `$loop` variable

- `@foreach` and `@forelse` directives add one feature that’s not available in PHP foreach loops: the `$loop` variable

```php
<ul>
    @foreach ($pages as $page)
        <li>{{ $loop->iteration }}: {{ $page->title }}
            @if ($page->hasChildren())
            <ul>
                @foreach ($page->children() as $child)
                    <li>
                        {{ $loop->parent->iteration }} {{ $loop->iteration }}:
                        {{ $child->title }}
                    </li>
                @endforeach
            </ul>
            @endif
        </li>
    @endforeach
</ul>
```

#### Template Inheritance

- template inheritance allows views to extend, modify, and include other views.

##### Extending a Blade layout

```php
<!-- resources/views/layouts/master.blade.php -->
// Defining Sections with `@section` / `@show` and `@yield`
<html>
    <head>
    // @yield() with default value
     <title>My Site | @yield('title', 'Home Page')</title>
    </head>
    <body>
        <div class="container">
             @yield('content')
        </div>
        @section('footerScripts')
            // fallback script
            <script src="app.js"></script>
        @show
    </body>
</html>

<!-- resources/views/dashboard.blade.php -->
@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    Welcome to your application dashboard!
@endsection

@section('footerScripts')
    @parent
    <script src="dashboard.js"></script>
@endsection
```

- all three (`@yield()` and `@section/@show`) are defining **defaults** to render if the section isn’t extended, The difference between is that for ` @section/@show` its default contents will be available to its children as `@parent` directive
- `@show` is used when you’re defining the place for a section, in the **parent** template
- `@endsection` or `@stop` when you’re defining the end of a section content in a **child** template
- `@extends()` signals to Laravel that the view is not standalone instead it **extends** another view
- sometimes instead of using `@section` and `@endsection`, we’re just using a shortcut e.g `@section('title', 'Dashboard')`

### Including View Partials

1. `@include`

- we can include a component and also pass the **props**
- included partials automatically get access to global variables e.g `$pageName`

```php
<!-- resources/views/home.blade.php -->
<div class="content" data-page-name="{{ $pageName }}">
    <p>Please click below to sign up</p>
    @include('sign-up-button', ['text' => 'See just how great it is'])
</div>

<!-- resources/views/sign-up-button.blade.php -->
<a class="button" data-page-name="{{ $pageName }}">
    <i class="exclamation-icon"></i> {{ $text }}
</a>
```

- You also use the `@includeIf`, `@includeWhen`, and `@includeFirst`

```php
//  Include view if it exists
@includeIf('sidebars.admin', ['some' => 'data'])

//  Include view if a passed variable is truth-y
@includeWhen($user->isAdmin(), 'sidebars.admin', ['some' => 'data'])

//  Include the first view that exists from a given array of views
@includeFirst(['customs.header', 'header'], ['some' => 'data'])

```

2. `@each`

- useful if you’d need to **loop** over an array or collection and **include** a partial for each item.
- The first **('partials.module')** parameter is the name of the view partial
- The second **($modules)** is the $array or $collection to iterate over
- . The third **('module')** is the variable name that each item (in this case, each element in the `$modules` array) will be passed as to the view (i.e `$module`)
- The 4th **('partials.empty-module')** parameter is the name of the view partial

```php
<!-- resources/views/sidebar.blade.php -->
<div class="sidebar">
    @each('partials.module', $modules, 'module', 'partials.empty-module')
</div>

<!-- resources/views/partials/module.blade.php -->
<div class="sidebar-module">
    <h1>{{ $module->title }}</h1>
</div>

<!-- resources/views/partials/empty-module.blade.php -->
<div class="sidebar-module">
    Sorry, no modules
</div>
```
