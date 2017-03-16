# laravel-modules
Modules management library for laravel 5.1

[![Packagist](https://img.shields.io/packagist/v/itvisionsy/laravel-modules.svg)]()
[![license](https://img.shields.io/github/license/itvisionsy/laravel-modules.svg)]()
[![Build Status](https://travis-ci.org/itvisionsy/laravel-modules.svg?branch=master)](https://travis-ci.org/itvisionsy/laravel-modules)
[![PHP](https://img.shields.io/badge/PHP-5.6+-4F5B93.svg)]()
[![Laravel](https://img.shields.io/badge/Laravel-5.1-f4645f.svg)]()

Allows modules structure of your project. Each module can have its views, config, routes, controllers, ...

## Installation
 1. The package relies on the composer PSR-4 loader like all laravel projects. Use the composer command:
     ```
    composer require itvisionsy/laravel-modules
    ```
 1. Add `\ItvisionSy\Laravel\Modules\ServiceProvider::class` to providers section in your `config/app.php` file:
     ```php
    'providers'=>[
        //...
        \ItvisionSy\Laravel\Modules\ServiceProvider::class,
    ],
    ```
 1. Publish the config file using the command
    ```
    php artisan vendor:publish
    ```
    This will copy the `modules.php` config file to your `config` folder.
 1. Modify the `config/modules.php` config file as needed.

## How It Works
Your modules should go in a root modules folder. By default this is `app/Modules` which maps to the namespace 
`\App\Modules`.

Each of your modules will have its own folder inside the modules root folder, the folder will be named after the module 
name, and will map to the namespace `\App\Modules\{ModuleName}`.

Each module will contain a base module definition class, which (by default) will be named `Module.php` and maps to 
the namespace `\App\Modules\{ModuleName}\Module`. This class will act as the key generator for the module URLs, routes,
and other framework-related values.

Each module will contain its data models, controllers, views, routes, and other project files as usual. The `composer`
PSR-4 loader should take care of loading your module files and classes properly.

Your module controllers (by default go into the `Http/Controllers` folder) should inherite the 
`ItvisionSy\Laravel\Modules\Controller` class to make views rendering and other tasks easier.

## Creating Modules
To create a new module, you can use the artisan command
```
php artisan modules:make {id} {name} [--url={url}]
```
Values of `id`, `name`, and `url` are strings, and the URL part is optional and will be used to generate make the
  URLs of the module more human friendly.
  
This command will create the basic folder structure inside the modules folder, along with the base module and a sample
routes (inside `Http/routes.php`), controller (inside `Http/Controllers/`), and view (inside `Views`).

As you have the basic structure, you can start creating your files and classes as normal. Nothing special to worry about.

## Thanks
 - [JetBrains](https://www.jetbrains.com/) for the free license of [PHPStorm IDE](https://www.jetbrains.com/phpstorm/specials/phpstorm/phpstorm.html). The great tool I wrote this module with.