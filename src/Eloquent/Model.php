<?php

namespace Malico\LaravelNanoid\Eloquent;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    use InteractsWithNanoid;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Nano id length.
     *
     * @var array|int
     */
    protected $nanoidLength;

    /**
     * Nano id prefix.
     *
     * @var string
     */
    protected $nanoidPrefix = '';

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
