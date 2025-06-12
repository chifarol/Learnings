<!-- @format -->

# Using Components

- This another way of structuring view partials that looks much closer to how components work in frontend frameworks like **React**

There 2 types of component:

1. **Class-based components**

   - Blade templates backed by a PHP class that injects data and functionality
   - to generate the PHP class, run

   ```php
   php artisan make:component Modals
   // for nested views folders
   php artisan make:component Modals/Msg
   ```

2. **Anonymous components**
   - They purely Blade templates
   - generated with the `--view` flag:
   ```
   php artisan make:component modal --view
   ```

```html
<!-- resources/views/components/modal/message.blade.php -->
<div class="modal">
  <h2>{{ $title }}</h2>
  <div>{{ $slot }}</div>
  <div class="close button etc">...</div>
</div>

<!-- in another template -->
<x-modal.message title="Insecure password">
  <p>
    The password you have provided is not valid. Here are the rules for valid
    passwords: [...]
  </p>
  <p>
    <a href="#">...</a>
  </p>
</x-modal.message>
```

- If you’d like to group your components under folders, you can use the . separator

## Passing data into components

There are four ways to pass data into components:

1. **attributes**:
   For anonymouse components:

   - you’ll need to define the attributes in a props array at the **top** of your template
   - to pass a PHP variables/expressions prefix attribute with colon(:) eg `:width`
   - to pass an ordinary string omit the ':' prefix

   ```html
   <?php 
     $width = 50;  
   ?>
   <!-- Passing the data in -->
   <x-modal title="Title here yay" :width="$width" />

   <!-- Accessing the data in the template -->

   @props([ 'messages'=>[],'width'=>40, 'title' ])

   <h4>{{ $title }}</h4>
   @if ($messages)
   <ul style="width: {{ $width }}; ">
     @foreach ( $messages as $message)
     <li>{{ $message }}</li>
     @endforeach
   </ul>
   @endif
   ```

For Class-based components:

- you’ll need to define every attribute in the PHP class and set it as a public property on the class
- to pass a ordinary string omit the ':' prefix

```php
class Modal extends Component {

  public function __construct( public string $title, public string $width ) {

     }
 }

<!-- Accessing the data in the template -->
<div style="width: {{ $width }}">
  <h1>{{ $title }}</h1>
</div>
```

-

2. **Passing data into components via slots**

- By default, if a component that has an **opening** and a **closing** tag then it **automatically** has a variable called `$slot` which is the equivalent of `children` in React components

```html
<!-- in modal.blade.php -->
<div>
  <!-- the children are rendered here -->
  <h1>{{ $slot }}</h1>
</div>

<!-- in template -->
<x-modal>
  <p>
    The password you have provided is not valid. Here are the rules for valid
    passwords: [...]
  </p>
  <p><a href="#">...</a></p>
</x-modal>
```

4. **Named slots**

- if the prop data is a complex HTML markup we can pass it as a named component

```html
 <!-- in modal.blade.php -->
<div style="width: {{ $width }}">
    <!-- x-slot:title is rendered here -->
    <h1>{{ $title }}</h1>
    <!-- the children are rendered here -->
  <div>{{ $slot }}</div>
</div>


 <!-- in template -->
<x-modal>
 <x-slot:title>
    <h2 class="uppercase">Password requirements not met</h2>
 </x-slot>
    <p>The password you have provided is not valid.
    Here are the rules for valid passwords: [...]</p>
    <p><a href="#">...</a></p>
</x-modal>


```

## Component Methods

- mainly for Class-based components

```php
// in the component Class definition
public function checkNum($number)
{
 return $number>4;
}
<!-- in the template -->
<div>
 @if ($checkNum(8))
  <div> 8 is greater than 4 </div>
 @endif
 <!-- ... -->
</div>

```

## Component Methods

- mainly for Class-based components

```php
// in the component Class definition
public function checkNum($number)
{
    return $number>4;
}
<!-- in the template -->
<div>
 @if ($checkNum(8))
  <div> 8 is greater than 4 </div>
 @endif
 <!-- ... -->
</div>

```

## Attributes bag

- This can be likened to props in React e.g

```jsx
// JS component
const MyComp=(props)=>
<div>
    {props.title}
</div>

// in template
<MyComp title="lol" text="lorem ipsum" />
```

- It is used to grab all of those attributes at once
- Merge default classes with passed-in classes
- you can just echo them out with `{{ $attributes }}` or merge an attribute e.g a classname `{{ $attributes->merge(['class' => 'p-4 m-4']) }}`

```php
<!-- Definition -->
<div {{ $attributes->merge(['class' => 'p-4 m-4','type' => 'button']) }}>
 {{ $slot }}
</div>

<!-- Usage -->
<x-notice class="text-blue-200">
 Message here
</x-notice>
```
