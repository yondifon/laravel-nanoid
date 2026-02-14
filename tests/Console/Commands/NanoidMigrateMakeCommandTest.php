<?php

use Malico\LaravelNanoid\Console\Commands\NanoidMigrateMakeCommand;

beforeEach(function (): void {
    collect(glob(database_path('migrations/*_create_test_models_table.php')))->each(function ($file): void {
        unlink($file);
    });
});

test('it generates a migration', function (): void {
    $this->artisan(NanoidMigrateMakeCommand::class, ['name' => 'create_test_models_table'])->assertExitCode(0);

    $this->assertFileExists(glob(database_path('migrations/*_create_test_models_table.php'))[0]);
    $this->assertStringContainsString('$table->string(\'id\')->primary();', file_get_contents(glob(database_path('migrations/*_create_test_models_table.php'))[0]));
});
