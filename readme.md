# Laravel Nanoid

## Introduction

A simple drop-in solution for providing nanoid support for the IDs of your Eloquent models. (Stripe-like IDs)

## Installing

`composer require malico/laravel-nanoid`

## Usage

Use nanoid within your model, use the trait `HasNanoids` within your model like.

```diff
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
+ use Malico\LaravelNanoid\HasNanoids;

class Book extends Model{
    use HasFactory;
+   use HasNanoids;
}
```

Your migration file should like.

```diff
// migration file
public function up()
{
    Schema::create('test_models', function (Blueprint $table) {
-       $table->id();
+       $table->string('id')->primary();
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

class YourModel Extends \Illuminate\Database\Eloquent\Model
{
    /** @var array|int */
    protected $nanoidLength = 10;
    // id will be of length 10
    // specifying to array. e.g [10, 20] will generate id of length 10 to 20
    // or
    public function nanoidLength(): array|int
    {
        // [10,20]
        return 10;
    }

    /** @var string */
    protected $nanoidPrefix = 'pl_'; // id will look: pl_2k1MzOO2shfwow ...
    // or
    public function nanoidPrefix(): string
    {
        return 'pay_'; // pay_2MII83829sl2d
    }

    /** @var string */
    protected nanoidAlphabet = 'ABC';
    // or
    public function nanoidAlphabet(): stirng {
        return 'ABC'; // pay_ACBACB
    }

    public function uniqueIds()
    {
        // will create nanonids for 'unique_id' &'another_with'
        // Also, won't break if id is not listed.
        return ['unique_id', 'another_id'];
    }
}
```

Check the upgrade guide if you're [upgrading](UPGRADE.MD) from 0.x

### Author

Ndifon Desmond Yong
