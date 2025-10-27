<?php
namespace Obrainwave\AccessTree;

use Obrainwave\AccessTree\Console\Commands\AccessTreeSeedCommand;
use Obrainwave\AccessTree\Console\Commands\InstallAdminInterfaceCommand;
use Obrainwave\AccessTree\Console\Commands\InstallAccessTreeCommand;
use Obrainwave\AccessTree\Console\Commands\TestAccessTreeCommand;
use Obrainwave\AccessTree\Console\Commands\CreateAdminUserCommand;
use Obrainwave\AccessTree\Console\Commands\ClearAccessTreeCacheCommand;
use Obrainwave\AccessTree\Console\Commands\DebugAdminRoutesCommand;
use Obrainwave\AccessTree\Console\Commands\DiscoverTablesCommand;
use Obrainwave\AccessTree\Console\Commands\SetupUniversalAdminCommand;
use Obrainwave\AccessTree\Console\Commands\CheckRoutesCommand;
use Obrainwave\AccessTree\Console\Commands\ConfigureUserModelCommand;
use Obrainwave\AccessTree\Console\Commands\TestUserModelCommand;
use Obrainwave\AccessTree\Console\Commands\TestTableRoutesCommand;
use Obrainwave\AccessTree\Console\Commands\CleanupTestDataCommand;
use Obrainwave\AccessTree\Console\Commands\TestViewsCommand;
use Obrainwave\AccessTree\Console\Commands\TestUniversalRoutesCommand;
use Obrainwave\AccessTree\Console\Commands\TestDynamicSidebarCommand;
use Obrainwave\AccessTree\Console\Commands\ConfigureStylingCommand;
use Obrainwave\AccessTree\Console\Commands\PublishModernCSSCommand;
use Obrainwave\AccessTree\Console\Commands\TestRouteAccessCommand;
use Obrainwave\AccessTree\Console\Commands\TestActualRouteCommand;
use Obrainwave\AccessTree\Console\Commands\TestRouteWithAuthCommand;
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
            ->hasCommand(AccessTreeSeedCommand::class)
            ->hasCommand(InstallAdminInterfaceCommand::class)
            ->hasCommand(InstallAccessTreeCommand::class)
            ->hasCommand(TestAccessTreeCommand::class)
            ->hasCommand(CreateAdminUserCommand::class)
            ->hasCommand(ClearAccessTreeCacheCommand::class)
            ->hasCommand(DebugAdminRoutesCommand::class)
            ->hasCommand(DiscoverTablesCommand::class)
            ->hasCommand(SetupUniversalAdminCommand::class)
            ->hasCommand(CheckRoutesCommand::class)
            ->hasCommand(ConfigureUserModelCommand::class)
            ->hasCommand(TestUserModelCommand::class)
            ->hasCommand(TestTableRoutesCommand::class)
            ->hasCommand(CleanupTestDataCommand::class)
            ->hasCommand(TestViewsCommand::class)
            ->hasCommand(TestUniversalRoutesCommand::class)
            ->hasCommand(TestDynamicSidebarCommand::class)
            ->hasCommand(ConfigureStylingCommand::class)
            ->hasCommand(PublishModernCSSCommand::class)
            ->hasCommand(TestRouteAccessCommand::class)
            ->hasCommand(TestActualRouteCommand::class)
            ->hasCommand(TestRouteWithAuthCommand::class);
    }

    public function packageBooted()
    {
        // Register view namespace
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'accesstree');
        
        // Register custom pagination views
        $this->loadViewsFrom(__DIR__ . '/../resources/views/admin/pagination', 'pagination');

        // Allow developers to publish the default PermissionSeeder
        $this->publishes([
            __DIR__ . '/../stubs/PermissionSeeder.stub' => database_path('seeders/PermissionSeeder.php'),
        ], 'accesstree-seeders');

        // Publish admin views
        $this->publishes([
            __DIR__ . '/../resources/views/admin' => resource_path('views/vendor/accesstree/admin'),
        ], 'accesstree-admin-views');

        // Publish admin routes
        $this->publishes([
            __DIR__ . '/../routes/admin.php' => base_path('routes/accesstree-admin.php'),
        ], 'accesstree-admin-routes');

        // Publish modern dashboard CSS
        $this->publishes([
            __DIR__ . '/../public/css/modern-dashboard.css' => public_path('css/modern-dashboard.css'),
        ], 'accesstree-modern-css');

        $this->app['router']->aliasMiddleware(
            'accesstree',
            CheckAccessMiddleware::class
        );

        $this->app['router']->aliasMiddleware(
            'accesstree.admin',
            \Obrainwave\AccessTree\Http\Middleware\AdminAuthMiddleware::class
        );

        // Load admin routes if they exist
        if (file_exists(base_path('routes/accesstree-admin.php'))) {
            $this->loadRoutesFrom(base_path('routes/accesstree-admin.php'));
        } else {
            $this->loadRoutesFrom(__DIR__ . '/../routes/admin.php');
        }

        // Load API routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        
        // Load universal table routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/admin-universal.php');

        // Register Laravel Gates
        if (config('accesstree.gates.enabled', true)) {
            $this->app->make(\Obrainwave\AccessTree\Services\GateService::class)->registerGates();
        }
    }

    public function registeringPackage()
    {
        // Register repositories
        $this->app->singleton(\Obrainwave\AccessTree\Repositories\PermissionRepository::class);
        $this->app->singleton(\Obrainwave\AccessTree\Repositories\RoleRepository::class);
        $this->app->singleton(\Obrainwave\AccessTree\Repositories\UserRepository::class);
        
        // Register cache manager
        $this->app->singleton(\Obrainwave\AccessTree\Services\CacheManager::class);
        
        // Register gate service
        $this->app->singleton(\Obrainwave\AccessTree\Services\GateService::class);
        
        // Register the service
        $this->app->singleton(\Obrainwave\AccessTree\Services\AccessTreeService::class);
        $this->app->singleton(\Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface::class, \Obrainwave\AccessTree\Services\AccessTreeService::class);

        // Register the facade alias
        $this->app->alias(\Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface::class, 'AccessTree');
    }
}
