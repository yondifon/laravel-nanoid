<?php

use Illuminate\Support\Str;
use Malico\LaravelNanoid\Eloquent\Model;

abstract class ModelTest extends Model
{
    protected $table = 'test_migrations_with_string_id';
}

class BasicModel extends ModelTest
{
}

class BasicModelWithPrefix extends ModelTest
{
    protected $nanoidPrefix = 'pl_';
}

class BasicModelWithLength extends ModelTest
{
    protected $nanoidLength = 3;
}

test('creates nanoid before saving', function () {
    $model = BasicModel::create();

    expect($model->getKey())->toBeString();
});

test('creates nanoid with prefix before saving', function () {
    $model = BasicModelWithPrefix::create();

    expect(Str::is('pl_*', $model->getKey()))->toBeTrue();
});

test('creates nanoid with length before saving', function () {
    $model = BasicModelWithLength::create();

    expect(Str::length($model->getKey()))->toBe(3);
});
