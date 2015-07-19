# Laravel Bootstrap Forms

Create Foundation forms in Laravel in no time.
You can find the original article here: http://blog.stidges.com/post/easy-bootstrap-forms-in-laravel

## Install

```
composer require ckdot/laravel-foundation-forms:~0.1
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
        Illuminate\Html\HtmlServiceProvider::class,
        Ckdot\FoundationForms\FoundationFormsServiceProvider::class,
        // ...
    ),
    // ...
);
```

No change is necessary for the Form Facade.

## Example

```
{{ Form::open([ 'route' => 'posts.store' ]) }}

    {{ Form::openGroup('title', 'Title') }}
        {{ Form::text('title') }}
    {{ Form::closeGroup() }}

    {{ Form::openGroup('status', 'Status') }}
        {{ Form::select('status', $statusOptions) }}
    {{ Form::closeGroup() }}

{{ Form::close() }}
```
