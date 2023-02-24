<?php

namespace Obrainwave\AccessTree\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Obrainwave\AccessTree\SkeletonServiceProvider;
use Obrainwave\AccessTree\AccessTreeServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Obrainwave\AccessTree\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            AccessTreeServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        // /*
        $migration = include __DIR__.'/../database/migrations/create_permission_table.php.stub';
        $migration->up();
        $migration = include __DIR__.'/../database/migrations/create_role_table.php.stub';
        $migration->up();
        // */
    }

}
