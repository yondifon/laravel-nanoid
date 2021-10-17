<?php

namespace Malico\LaravelNanoid;

use Illuminate\Support\ServiceProvider;
use Malico\LaravelNanoid\Console\Commands\NanoidMigrateMakeCommand;
use Malico\LaravelNanoid\Console\Commands\NanoidModelMakeCommand;

class LaravelNanoidServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                NanoidModelMakeCommand::class,
                NanoidMigrateMakeCommand::class,
            ]);
        }
    }
}
