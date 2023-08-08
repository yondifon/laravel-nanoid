<?php

namespace Malico\LaravelNanoid\Console\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class NanoidModelMakeCommand extends ModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:nanoid-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent Nanoid model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Nanoid Model';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return realpath(__DIR__.'/stubs/nanoid.model.stub');
    }

    /**
     * Create a migration file for the model.
     */
    protected function createMigration(): void
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        $this->call(NanoidMigrateMakeCommand::class, [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }
}
