# Laravel Nanoid

Generate Nanoid-based, Stripe-style IDs for your Eloquent models.

## Installation

```bash
composer require malico/laravel-nanoid
```

## Quick Start

Add `HasNanoids` to your model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Malico\LaravelNanoid\HasNanoids;

class Book extends Model
{
    use HasFactory;
    use HasNanoids;
}
```

Use a string primary key in your migration:

```php
public function up(): void
{
    Schema::create('books', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->timestamps();
    });
}
```

You can also scaffold migrations with:

```bash
php artisan make:nanoid-migration
```

`make:nanoid-migration` accepts the same arguments as `make:migration`.

## Configuration

You can configure generated IDs per model with these properties (or methods with the same names):

- `nanoidPrefix`: static prefix, for example `ord_`
- `nanoidLength`: fixed length (`10`) or random range (`[10, 20]`)
- `nanoidAlphabet`: allowed characters
- `nanoidFormat`: structured format like `{3}-{4}`

Each option supports either:

- A single value for all unique ID columns
- A keyed array per column

### `nanoidPrefix`

```php
// One prefix for all generated ids
protected $nanoidPrefix = 'p-';

// Per-column prefixes
protected $nanoidPrefix = ['id' => 'p-', 'username' => 'u_'];
```

### `nanoidLength`

```php
// One fixed length for all generated ids
protected $nanoidLength = 12;

// Random length between min and max for all generated ids
protected $nanoidLength = [2, 5];

// Per-column fixed length
protected $nanoidLength = ['id' => 7, 'username' => 12];

// Per-column ranged length (min, max)
protected $nanoidLength = ['id' => [2, 5], 'username' => [8, 10]];

// Invalid: range must have exactly two values [min, max]
protected $nanoidLength = ['id' => [2]];
```

### `nanoidAlphabet`

```php
// One alphabet for all generated ids
protected $nanoidAlphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

// Per-column alphabets
protected $nanoidAlphabet = [
    'id' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
    'username' => 'abcdefghijklmnopqrstuvwxyz',
];
```

### `nanoidFormat`

```php
// One format for all unique id columns
protected $nanoidFormat = 'u_{4}-{4}';

// Per-column formats
protected $nanoidFormat = ['id' => 'p-{7}', 'username' => 'u_{4}-{4}'];
```

### Full model example

```php
<?php

use Illuminate\Database\Eloquent\Model;
use Malico\LaravelNanoid\HasNanoids;

class SessionToken extends Model
{
    use HasNanoids;

    protected $nanoidAlphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    protected $nanoidPrefix = [
        'id' => 'p-',
    ];
    protected $nanoidLength = 12;
    protected $nanoidFormat = [
        'username' => 'u_{8}',
    ];

    public function uniqueIds(): array
    {
        return ['id', 'username'];
    }
}

// id: p-8F4Z2K9T7Q1M
// username: u_A8K9P2QW
```

## Format Patterns

Use `nanoidFormat` when you want readable, segmented IDs.

Supported placeholders:

- `{n}` generates exactly `n` random characters
- `{min-max}` generates a random length between `min` and `max`

All other characters are kept as-is (`-`, `_`, `.`, spaces, and so on).

```php
class TrackingCode extends Model
{
    use HasNanoids;

    protected $nanoidFormat = 'TRK-{3}-{3-4}-{6}';
}

// TRK-X9a-k2Pm-8Qw1Zr
// TRK-L0p-r7A-2bV9tK
```

```php
class Coupon extends Model
{
    use HasNanoids;

    protected $nanoidAlphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    protected $nanoidFormat = '{4} {4}';
}

// 7KQ2 M9TZ
```

Rules:

- If `nanoidFormat` is set, it fully controls the generated shape
- `nanoidFormat` cannot be combined with `nanoidPrefix`
- `nanoidFormat` cannot be combined with `nanoidLength`
- Without `nanoidFormat`, generation falls back to `nanoidPrefix + nanoidLength`
- With per-column arrays, these conflict rules apply per column

## Benchmark

Run the included micro-benchmark:

```bash
php benchmarks/nanoid.php
```

## Choosing an ID Type

- Use this package when you want random, non-sequential, human-friendly IDs (for example `p-8F4Z2K9T7Q1M`)
- Use Laravel `HasUlids` when you want sortable IDs that preserve creation order better
- Use auto-incrementing integers when you need simple sequential IDs and the best insert locality

If your system requires strictly incremental IDs, this package is not the right tool.

## Safety Notes

- Short IDs are easier to collide and easier to guess; increase length for public or sensitive resources
- NanoID's own guidance compares default NanoID entropy with UUID v4 (similar collision profile)
- Always choose size/alphabet based on your scale and threat model
- For custom sizes, check collision estimates with: <https://zelark.github.io/nano-id-cc/>

If you are upgrading from `0.x`, see [UPGRADE.MD](UPGRADE.MD).

## Author

Ndifon Desmond Yong
