<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class DebugAdminRoutesCommand extends Command
{
    protected $signature = 'accesstree:debug-routes';
    protected $description = 'Debug AccessTree admin routes and check if they are properly registered';

    public function handle()
    {
        $this->info('Checking AccessTree admin routes...');

        $adminRoutes = [
            'accesstree.admin.dashboard',
            'accesstree.admin.permissions.index',
            'accesstree.admin.roles.index',
            'accesstree.admin.users.index',
        ];

        $this->table(['Route Name', 'Status', 'URL'], collect($adminRoutes)->map(function ($routeName) {
            try {
                $url = route($routeName);
                return [$routeName, '✅ Working', $url];
            } catch (\Exception $e) {
                return [$routeName, '❌ Error: ' . $e->getMessage(), 'N/A'];
            }
        }));

        $this->info('Checking if views exist...');

        $views = [
            'accesstree::admin.dashboard',
            'accesstree::admin.permissions.index',
            'accesstree::admin.roles.index',
            'accesstree::admin.users.index',
        ];

        $this->table(['View Name', 'Status'], collect($views)->map(function ($viewName) {
            try {
                if (view()->exists($viewName)) {
                    return [$viewName, '✅ Exists'];
                } else {
                    return [$viewName, '❌ Not Found'];
                }
            } catch (\Exception $e) {
                return [$viewName, '❌ Error: ' . $e->getMessage()];
            }
        }));

        $this->info('Checking controllers...');

        $controllers = [
            'Obrainwave\AccessTree\Http\Controllers\Admin\AdminController',
            'Obrainwave\AccessTree\Http\Controllers\Admin\PermissionController',
            'Obrainwave\AccessTree\Http\Controllers\Admin\RoleController',
            'Obrainwave\AccessTree\Http\Controllers\Admin\UserController',
        ];

        $this->table(['Controller', 'Status'], collect($controllers)->map(function ($controller) {
            if (class_exists($controller)) {
                return [$controller, '✅ Exists'];
            } else {
                return [$controller, '❌ Not Found'];
            }
        }));

        $this->info('Debug complete!');
    }
}
