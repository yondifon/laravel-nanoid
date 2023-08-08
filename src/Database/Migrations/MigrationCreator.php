<?php

namespace Malico\LaravelNanoid\Database\Migrations;

use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator extends BaseMigrationCreator
{
    public function __construct(Filesystem $files, $customStubPath = null)
    {
        parent::__construct($files, $customStubPath);
    }

    public function stubPath()
    {
        return __DIR__.'/../../Console/Commands/stubs';
    }
}
