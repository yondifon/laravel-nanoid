<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Malico\LaravelNanoid\HasNanoids;

it('creates nanoid before saving', function () {
    $model = BasicModel::create();

    expect($model->getKey())->toBeString();
});

it('creates nanoid with prefix before saving', function () {
    $model = BasicModelWithPrefix::create();

    expect(Str::is('pl_*', $model->getKey()))->toBeTrue();
});

it('creates nanoid with length before saving', function () {
    $model = BasicModelWithLength::create();

    expect(Str::length($model->getKey()))->toBe(3);
});

it('creates nanoid with length array before saving', function () {
    $model = BasicModelWithLengthArray::create();

    expect(Str::length($model->getKey()))->toBeGreaterThanOrEqual(3);
    expect(Str::length($model->getKey()))->toBeLessThanOrEqual(5);
});

it('creates nanoid with prefix and length before saving', function () {
    $model = BasicModelWithPrefixAndLength::create();

    expect(Str::is('pl_*', $model->getKey()))->toBeTrue();
    expect(Str::length($model->getKey()))->toBe(6); // 3 + 3
});

it('creates nanoid with prefix and length array before saving', function () {
    $model = BasicModelWithPrefixAndLengthArray::create();

    expect(Str::is('pl_*', $model->getKey()))->toBeTrue();
    expect(Str::length($model->getKey()))->toBeGreaterThanOrEqual(6); // 3 + 3
    expect(Str::length($model->getKey()))->toBeLessThanOrEqual(8); // 3 + 5
});

it('creates nanoid with prefix method before saving', function () {
    $model = BasicModelWithPrefixMethod::create();

    expect(Str::is('pl_*', $model->getKey()))->toBeTrue();
});

it('creates nanoid with length method before saving', function () {
    $model = BasicModelWithLengthMethod::create();

    expect(Str::length($model->getKey()))->toBe(3);
});

it('creates nanoid with alphabet before saving', function () {
    $model = BasicModelWithAlphabet::create();

    expect((int) $model->getKey())->toBeInt();
});

it('creates nanoid with alphabet and length before saving', function () {
    $model = BasicModelWithAlphabetAndLength::create();

    expect((int) $model->getKey())->toBeInt();
    expect(Str::length($model->getKey()))->toBe(3);
});

abstract class ModelTest extends Model
{
    use HasNanoids;

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

class BasicModelWithLengthArray extends ModelTest
{
    protected $nanoidLength = [3, 5];
}

class BasicModelWithPrefixAndLength extends ModelTest
{
    protected $nanoidPrefix = 'pl_';

    protected $nanoidLength = 3;
}

class BasicModelWithPrefixAndLengthArray extends ModelTest
{
    protected $nanoidPrefix = 'pl_';

    protected $nanoidLength = [3, 5];
}

class BasicModelWithPrefixMethod extends ModelTest
{
    public function nanoidPrefix(): string
    {
        return 'pl_';
    }
}

class BasicModelWithLengthMethod extends ModelTest
{
    public function nanoidLength(): int
    {
        return 3;
    }
}

class BasicModelWithAlphabet extends ModelTest
{
    protected $nanoidAlphabet = '1234567890';
}

class BasicModelWithAlphabetAndLength extends ModelTest
{
    protected $nanoidAlphabet = '1234567890';

    protected $nanoidLength = 3;
}
