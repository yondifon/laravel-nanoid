# Laravel Nanoid

## Introduction

A simple drop-in solution for providing nanoid support for the IDs of your Eloquent models. (Stripe-like IDs)

## Installing

`composer require malico/laravel-nanoid`

## Usage

There are two ways to use this package:

- By extending the provided model classes (preferred and simplest method).
- By using the provided model trait (allows for extending another model class).

### Extend the model classes

While creating your model, you can extend the `Eloquent Model` class provided by the package.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Malico\LaravelNanoid\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
}
```

To create model fast and easy, use the artisan command `make:nanoid-model`, same as you will do with the `make:model` command.
All arguments work the same as the `make:model` command. To create migration file with string id (which is what is needed), add the `-m` option, the migration file created will have id of string type (string) instead of autoincrementing integer type.

### Extend the model trait

With the `ModelTrait` trait, all you need to do is add the trait to your model class, then make you sure model properties look like this:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
namespace Malico\LaravelNanoid\Eloquent\NanoidTrait;

class Book extends Model{
    use HasFactory;
    use NanoidTrait;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The "booting" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            $model->{$model->getKeyName()} = $model->generateNanoid();
        });
    }
}
```

Take note of the `$incrementing` and `$keyType` properties. Also make sure within your `boot` method you call the `parent::boot` method and then add the `creating` event listener.
Also make sure your id column is set to `string` type.

```php
// migration file
public function up()
{
    Schema::create('test_models', function (Blueprint $table) {
        $table->string('id')->primary();
        //
        $table->timestamps();
    });
}
```

To create a new migration, use the artisan command `make:nanoid-migration`. All arguments work the same as the `make:migration` command.

## Options

1. Prefix: To Specify a Prefix for the IDs, you can specify a prefix by add `nanoPrefix' property to your model class.
2. Same applies for the length of the ID.

```php
<?php




    /**
     * Nano id length.
     *
     * @var array|int
     */
    protected $nanoidLength = 10;
    // id will be of length 10
    // specifying to array. e.g [10, 20] will generate id of length 10 to 20

    /**
     * Nano id prefix.
     *
     * @var string
     */
    protected $nanoidPrefix = 'pl_'; // id will look: pl_2k1MzOO2shfwow ...

```

This is inspired by the [Laravel Eloquent UUID](https://github.com/goldspecdigital/laravel-eloquent-uuid) package and stripe's transaction ids.

### Author

Ndifon Desmond Yong
