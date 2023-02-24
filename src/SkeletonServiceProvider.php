<?php

namespace Obrainwave\AccessTree;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Obrainwave\AccessTree\Commands\SkeletonCommand;

class SkeletonServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        // $package
        //     ->name('skeleton')
        //     ->hasConfigFile()
        //     ->hasViews()
        //     ->hasMigrations(['create_permission_table', 'create_role_table'])
        //     ->hasCommand(SkeletonCommand::class);
    }
}
