<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

use Illuminate\Database\Eloquent\Model;
use Malico\LaravelNanoid\HasNanoids;

const ITERATIONS = 100000;

abstract class NanoIdBenchModel extends Model
{
    use HasNanoids;

    public $timestamps = false;

    protected $guarded = [];
}

class BasicBenchModel extends NanoIdBenchModel
{
    protected $nanoidPrefix = 'p-';

    protected $nanoidLength = 12;
}

class FormatBenchModel extends NanoIdBenchModel
{
    protected $nanoidFormat = 'u_{4}-{4}-{4}';
}

class MultiFieldBenchModel extends NanoIdBenchModel
{
    protected $nanoidAlphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    protected $nanoidPrefix = [
        'id' => 'p-',
    ];

    protected $nanoidLength = [
        'id' => 12,
    ];

    protected $nanoidFormat = [
        'username' => 'u_{8}',
    ];

    public function uniqueIds(): array
    {
        return ['id', 'username'];
    }
}

function benchmark(string $label, callable $callback, int $iterations): void
{
    $start = hrtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $callback();
    }

    $durationMs = (hrtime(true) - $start) / 1000000;
    $microSecondsPerId = ($durationMs * 1000) / $iterations;

    printf("%-30s %10.2f ms | %8.3f us/op\n", $label, $durationMs, $microSecondsPerId);
}

$basic = new BasicBenchModel;
$format = new FormatBenchModel;
$multi = new MultiFieldBenchModel;

echo "Laravel Nanoid micro benchmark\n";
echo 'Iterations: '.ITERATIONS."\n\n";

benchmark('Basic prefix + length', fn () => $basic->newUniqueId(), ITERATIONS);
benchmark('Format only', fn () => $format->newUniqueId(), ITERATIONS);
benchmark('Per-column setUniqueIds', function () use ($multi): void {
    $multi->id = null;
    $multi->username = null;
    $multi->setUniqueIds();
}, ITERATIONS);
