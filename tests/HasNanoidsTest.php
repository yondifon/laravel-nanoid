<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Malico\LaravelNanoid\HasNanoids;

it('creates nanoid before saving', function (): void {
    $model = BasicModel::create();

    expect($model->getKey())->toBeString();
});

it('creates nanoid with prefix before saving', function (): void {
    $model = BasicModelWithPrefix::create();

    expect(Str::is('pl_*', $model->getKey()))->toBeTrue();
});

it('creates nanoid with length before saving', function (): void {
    $model = BasicModelWithLength::create();

    expect(Str::length($model->getKey()))->toBe(3);
});

it('creates nanoid with length array before saving', function (): void {
    $model = BasicModelWithLengthArray::create();

    expect(Str::length($model->getKey()))->toBeGreaterThanOrEqual(3);
    expect(Str::length($model->getKey()))->toBeLessThanOrEqual(5);
});

it('creates nanoid with prefix and length before saving', function (): void {
    $model = BasicModelWithPrefixAndLength::create();

    expect(Str::is('pl_*', $model->getKey()))->toBeTrue();
    expect(Str::length($model->getKey()))->toBe(6); // 3 + 3
});

it('creates nanoid with prefix and length array before saving', function (): void {
    $model = BasicModelWithPrefixAndLengthArray::create();

    expect(Str::is('pl_*', $model->getKey()))->toBeTrue();
    expect(Str::length($model->getKey()))->toBeGreaterThanOrEqual(6); // 3 + 3
    expect(Str::length($model->getKey()))->toBeLessThanOrEqual(8); // 3 + 5
});

it('creates nanoid with prefix method before saving', function (): void {
    $model = BasicModelWithPrefixMethod::create();

    expect(Str::is('pl_*', $model->getKey()))->toBeTrue();
});

it('creates nanoid with length method before saving', function (): void {
    $model = BasicModelWithLengthMethod::create();

    expect(Str::length($model->getKey()))->toBe(3);
});

it('creates nanoid with alphabet before saving', function (): void {
    $model = BasicModelWithAlphabet::create();

    expect((bool) preg_match('/^a+$/', $model->getKey()))->toBeTrue();
});

it('creates nanoid with alphabet and length before saving', function (): void {
    $model = BasicModelWithAlphabetAndLength::create();

    expect((bool) preg_match('/^a+$/', $model->getKey()))->toBeTrue();
    expect(Str::length($model->getKey()))->toBe(3);
});

it('creates nanoid with fixed format before saving', function (): void {
    $model = BasicModelWithFixedFormat::create();

    expect($model->getKey())->toBe('aaa-aaaa-aa');
});

it('creates nanoid with ranged format before saving', function (): void {
    $model = BasicModelWithRangedFormat::create();

    expect((bool) preg_match('/^aaa-a{3,4}-aa$/', $model->getKey()))->toBeTrue();
});

it('throws when nanoid format and length are used together', function (): void {
    expect(fn () => BasicModelWithFormatAndLength::create())
        ->toThrow(\InvalidArgumentException::class, 'Cannot use nanoidFormat and nanoidLength together.');
});

it('throws when nanoid format and prefix are used together', function (): void {
    expect(fn () => BasicModelWithFormatAndPrefix::create())
        ->toThrow(\InvalidArgumentException::class, 'Cannot use nanoidFormat and nanoidPrefix together.');
});

it('creates nanoid with multiple ids before saving', function (): void {
    $model = BasicModelWithMultipleIds::create();

    expect($model->getKey())->toBeString();
    expect($model->another_id)->toBeString();
});

it('creates multiple nanoids that are unique', function (): void {
    $model = BasicModelWithMultipleIds::create();

    expect($model->getKey())->toBeString();
    expect($model->another_id)->toBeString();

    expect($model->getKey())->not->toBe($model->another_id);
});

it("doesn't override existing id", function (): void {
    $model = BasicModelWithFillable::create(['another_id' => '123']);

    expect($model->another_id)->toBe('123');
});

it('creates nanoid for columns in models with auto-incrementing id', function (): void {
    $model = BasicModelWithDifferentNanoIdColumn::create();

    expect($model->nano_id)->toBeString();
});

it('creates per-column nanoid prefix and length', function (): void {
    $model = BasicModelWithPerColumnPrefixAndLength::create();

    expect((bool) preg_match('/^p-a{6}$/', $model->id))->toBeTrue();
    expect((bool) preg_match('/^u_a{8}$/', $model->another_id))->toBeTrue();
});

it('creates per-column nanoid with mixed format and length rules', function (): void {
    $model = BasicModelWithPerColumnMixedRules::create();

    expect((bool) preg_match('/^p-a{6}$/', $model->id))->toBeTrue();
    expect((bool) preg_match('/^u_a{4}-a{3,4}$/', $model->another_id))->toBeTrue();
});

it('creates per-column fixed and ranged lengths', function (): void {
    $model = BasicModelWithPerColumnLengthRanges::create();

    expect((bool) preg_match('/^i-a{7}$/', $model->id))->toBeTrue();
    expect((bool) preg_match('/^u_a{3,5}$/', $model->another_id))->toBeTrue();
});

it('creates per-column formats for multiple fields', function (): void {
    $model = BasicModelWithPerColumnFormats::create();

    expect((bool) preg_match('/^p-a{3}$/', $model->id))->toBeTrue();
    expect((bool) preg_match('/^u_a{2}-a{2}$/', $model->another_id))->toBeTrue();
});

it('distinguishes global length range from per-column fixed length', function (): void {
    $global = (new BasicModelWithLengthOneToTwo)->newUniqueId();
    $perColumn = (new BasicModelWithPerColumnFixedLengthTwo)->newUniqueId();

    expect(Str::length($global))->toBeGreaterThanOrEqual(1);
    expect(Str::length($global))->toBeLessThanOrEqual(2);
    expect(Str::length($perColumn))->toBe(2);
});

it('supports per-column ranged length value', function (): void {
    $model = BasicModelWithPerColumnLengthRangeValue::create();

    expect((bool) preg_match('/^a{2,4}$/', $model->id))->toBeTrue();
});

it('throws for invalid per-column length range definition', function (): void {
    expect(fn () => BasicModelWithInvalidPerColumnLengthRange::create())
        ->toThrow(\InvalidArgumentException::class, 'nanoidLength must be an integer or a [min, max] range.');
});

it('distinguishes global format from per-column format', function (): void {
    $global = BasicModelWithGlobalFormatForMultipleIds::create();
    $perColumn = BasicModelWithPerColumnSingleFormat::create();

    expect((bool) preg_match('/^a{2}-a{2}$/', $global->id))->toBeTrue();
    expect((bool) preg_match('/^a{2}-a{2}$/', $global->another_id))->toBeTrue();
    expect((bool) preg_match('/^a{2}$/', $perColumn->id))->toBeTrue();
    expect($perColumn->another_id)->toBeString();
});

it('throws for invalid per-column format value', function (): void {
    expect(fn () => BasicModelWithInvalidPerColumnFormat::create())
        ->toThrow(\InvalidArgumentException::class, 'nanoidFormat must be a string.');
});

abstract class ModelTest extends Model
{
    use HasNanoids;

    protected $table = 'test_migrations_with_string_id';
}

class BasicModelWithDifferentNanoIdColumn extends ModelTest
{
    protected $table = 'test_migration_with_integer_id';

    public function uniqueIds(): array
    {
        return ['nano_id'];
    }
}

class BasicModel extends ModelTest {}

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
    protected $nanoidAlphabet = 'aa';
}

class BasicModelWithAlphabetAndLength extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidLength = 3;
}

class BasicModelWithFixedFormat extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidFormat = '{3}-{4}-{2}';
}

class BasicModelWithRangedFormat extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidFormat = '{3}-{3-4}-{2}';
}

class BasicModelWithFormatAndLength extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidLength = 6;

    protected $nanoidFormat = 'ORD-{6}';
}

class BasicModelWithFormatAndPrefix extends ModelTest
{
    protected $nanoidPrefix = 'ord_';

    protected $nanoidFormat = '{3}-{3}';
}

class BasicModelWithMultipleIds extends ModelTest
{
    public function uniqueIds(): array
    {
        return ['id', 'another_id'];
    }
}

class BasicModelWithFillable extends ModelTest
{
    protected $fillable = ['another_id'];

    public function uniqueIds(): array
    {
        return ['id', 'another_id'];
    }
}

class BasicModelWithPerColumnPrefixAndLength extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidPrefix = [
        'id' => 'p-',
        'another_id' => 'u_',
    ];

    protected $nanoidLength = [
        'id' => 6,
        'another_id' => 8,
    ];

    public function uniqueIds(): array
    {
        return ['id', 'another_id'];
    }
}

class BasicModelWithPerColumnMixedRules extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidPrefix = [
        'id' => 'p-',
    ];

    protected $nanoidLength = [
        'id' => 6,
    ];

    protected $nanoidFormat = [
        'another_id' => 'u_{4}-{3-4}',
    ];

    public function uniqueIds(): array
    {
        return ['id', 'another_id'];
    }
}

class BasicModelWithPerColumnLengthRanges extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidPrefix = [
        'id' => 'i-',
        'another_id' => 'u_',
    ];

    protected $nanoidLength = [
        'id' => 7,
        'another_id' => [3, 5],
    ];

    public function uniqueIds(): array
    {
        return ['id', 'another_id'];
    }
}

class BasicModelWithPerColumnFormats extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidFormat = [
        'id' => 'p-{3}',
        'another_id' => 'u_{2}-{2}',
    ];

    public function uniqueIds(): array
    {
        return ['id', 'another_id'];
    }
}

class BasicModelWithLengthOneToTwo extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidLength = [1, 2];
}

class BasicModelWithPerColumnFixedLengthTwo extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidLength = ['id' => 2];
}

class BasicModelWithPerColumnLengthRangeValue extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidLength = ['id' => [2, 4]];
}

class BasicModelWithInvalidPerColumnLengthRange extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidLength = ['id' => [2]];
}

class BasicModelWithGlobalFormatForMultipleIds extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidFormat = '{2}-{2}';

    public function uniqueIds(): array
    {
        return ['id', 'another_id'];
    }
}

class BasicModelWithPerColumnSingleFormat extends ModelTest
{
    protected $nanoidAlphabet = 'aa';

    protected $nanoidFormat = [
        'id' => '{2}',
    ];

    public function uniqueIds(): array
    {
        return ['id', 'another_id'];
    }
}

class BasicModelWithInvalidPerColumnFormat extends ModelTest
{
    protected $nanoidFormat = [
        'id' => ['{2}'],
    ];
}
