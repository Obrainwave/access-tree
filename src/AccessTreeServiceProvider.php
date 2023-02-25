<?php

namespace Obrainwave\AccessTree;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AccessTreeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('accesstree')
            ->hasConfigFile()
            // ->hasViews()
            // ->hasRoutes(['web'])
            ->hasMigrations([
                'create_permission_table', 
                'create_role_table', 
                'create_role_has_permission_table', 
                'create_user_role_table',
                'add_is_root_user_to_user_table',
            ]);
            // ->hasCommand(SkeletonCommand::class);
    }
}