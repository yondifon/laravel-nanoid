<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Malico\LaravelNanoid\LaravelNanoidServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function setUpDatabase(): void
    {
        $this->app['db']
            ->connection('testing')
            ->getSchemaBuilder()
            ->create('test_migrations_with_string_id', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->timestamps();
            });
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelNanoidServiceProvider::class,
        ];
    }
}
