<?php

namespace Malico\LaravelNanoid\Auth;

use Illuminate\Foundation\Auth\User as AuthUser;
use Malico\LaravelNanoid\Eloquent\InteractsWithNanoid;

class User extends AuthUser
{
    use InteractsWithNanoid;

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
