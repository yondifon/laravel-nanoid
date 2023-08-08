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
        return realpath(__DIR__.'/../../Eloquent/stubs/nanoid.model.stub');
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        // Remove the pivot option from the parent class.
        return array_filter(
            parent::getOptions(),
            fn (array $option): bool => $option[0] !== 'pivot'
        );
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        $this->call(NanoidMigrateMakeCommand::class, [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Get the value of a command option.
     *
     * @param  null|string  $key
     * @return null|array|bool|string
     */
    public function option($key = null)
    {
        if ($key === 'pivot') {
            return false;
        }

        return parent::option($key);
    }
}
