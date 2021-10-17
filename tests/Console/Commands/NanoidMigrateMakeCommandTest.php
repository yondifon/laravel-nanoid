<?php

use Malico\LaravelNanoid\Console\Commands\NanoidMigrateMakeCommand;

beforeEach(function () {
    collect(glob(database_path('migrations/*_create_test_models_table.php')))->each(function ($file) {
        unlink($file);
    });
});

test('it generates a migration', function () {
    $this->artisan(NanoidMigrateMakeCommand::class, ['name' => 'create_test_models_table'])->assertExitCode(0);

    $this->assertFileExists(glob(database_path('migrations/*_create_test_models_table.php'))[0]);
    $this->assertStringContainsString('$table->string(\'id\')->primary();', file_get_contents(glob(database_path('migrations/*_create_test_models_table.php'))[0]));
});
