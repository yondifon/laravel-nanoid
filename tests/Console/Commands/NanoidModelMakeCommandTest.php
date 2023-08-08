<?php

use Malico\LaravelNanoid\Console\Commands\NanoidModelMakeCommand;

beforeEach(function () {
    collect(glob(database_path('migrations/*_create_test_models_table.php')))->each(function ($file) {
        unlink($file);
    });

    if (file_exists(app_path('Models/TestModel.php'))) {
        unlink(app_path('Models/TestModel.php'));
    }
});

test('it generates a model', function () {
    $this->artisan(NanoidModelMakeCommand::class, ['name' => 'TestModel'])->assertExitCode(0);

    $modelContent = file_get_contents(app_path('Models/TestModel.php'));

    $this->assertStringContainsString('use Malico\LaravelNanoid\HasNanoids', $modelContent);
    $this->assertStringContainsString('extends Model', $modelContent);
    $this->assertStringContainsString('use HasNanoids;', $modelContent);

});

test('it generates a migration if specified', function () {
    $this->artisan(NanoidModelMakeCommand::class, ['name' => 'TestModel', '--migration' => true])->assertExitCode(0);

    $this->assertFileExists(
        glob(database_path('migrations/*_create_test_models_table.php'))[0]
    );
});
