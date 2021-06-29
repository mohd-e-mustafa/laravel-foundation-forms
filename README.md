# Laravel Bootstrap Forms

Create Foundation forms in Laravel in no time.
You can find the original article here: http://blog.stidges.com/post/easy-bootstrap-forms-in-laravel

## Install

```
composer require mohd-e-mustafa/laravel-foundation-forms:~0.1
```

## Configure

Make sure you comment out the existing HtmlServiceProvider (Illuminate\Html\HtmlServiceProvider):

```php
<?php
// File (Laravel 5): config/app.php

return array(
    // ...
    'providers' => array(
        // ...
        //Illuminate\Html\HtmlServiceProvider::class,
        MohdMustafa\BootstrapFoundationForms\FoundationFormsServiceProvider::class,
        // ...
    ),
    // ...
);
```

No change is necessary for the Form Facade.

## Example

```
{{ Form::open([ 'route' => 'posts.store' ]) }}

    {{ Form::text('title') }}

    {{ Form::select('status', ['Active', 'In-Active']) }}

    {{ Form::checkbox('name', 'value') }}

    {{ Form::radio('name', 'value') }}

    {{ Form::file('image') }}

{{ Form::close() }}
```
