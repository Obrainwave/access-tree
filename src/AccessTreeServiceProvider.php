<?php
namespace Obrainwave\AccessTree;

use Obrainwave\AccessTree\Console\Commands\AccessTreeSeedCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Obrainwave\AccessTree\Http\Middleware\CheckAccessMiddleware;

class AccessTreeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('accesstree')
            ->hasConfigFile()
            ->hasMigrations([
                'create_permission_table',
                'create_role_table',
                'create_role_has_permission_table',
                'create_user_role_table',
                'add_is_root_user_to_user_table',
            ])
            ->hasCommand(AccessTreeSeedCommand::class);
    }

    public function packageBooted()
    {
        // Allow developers to publish the default PermissionSeeder
        $this->publishes([
            __DIR__ . '/../stubs/PermissionSeeder.stub' => database_path('seeders/PermissionSeeder.php'),
        ], 'accesstree-seeders');

        $this->app['router']->aliasMiddleware(
            'accesstree',
            CheckAccessMiddleware::class
        );

    }

    public function registeringPackage()
    {
        // Register the service
        $this->app->singleton(AccessTreeService::class);

        // Register the facade alias
        $this->app->alias(AccessTreeService::class, 'AccessTree');
    }
}
