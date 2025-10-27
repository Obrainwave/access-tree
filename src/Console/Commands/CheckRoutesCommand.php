<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CheckRoutesCommand extends Command
{
    protected $signature = 'accesstree:check-routes';
    protected $description = 'Check if AccessTree routes are properly registered';

    public function handle()
    {
        $this->info('🔍 Checking AccessTree routes...');
        
        $routes = Route::getRoutes();
        $accesstreeRoutes = [];
        
        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name && str_contains($name, 'accesstree.admin')) {
                $accesstreeRoutes[] = [
                    'name' => $name,
                    'uri' => $route->uri(),
                    'methods' => implode('|', $route->methods())
                ];
            }
        }
        
        if (empty($accesstreeRoutes)) {
            $this->error('❌ No AccessTree admin routes found!');
            $this->info('This might indicate that the routes are not being loaded properly.');
            return;
        }
        
        $this->info('✅ Found ' . count($accesstreeRoutes) . ' AccessTree admin routes:');
        
        foreach ($accesstreeRoutes as $route) {
            $this->line("  • {$route['name']} ({$route['methods']}) - {$route['uri']}");
        }
        
        // Check for specific problematic routes
        $problematicRoutes = [
            'accesstree.admin.users.create',
            'accesstree.admin.users.edit',
            'accesstree.admin.users.store',
            'accesstree.admin.users.update',
            'accesstree.admin.users.destroy'
        ];
        
        $this->info('');
        $this->info('🔍 Checking for problematic routes:');
        
        foreach ($problematicRoutes as $routeName) {
            if (Route::has($routeName)) {
                $this->line("  ✅ {$routeName} - EXISTS");
            } else {
                $this->line("  ❌ {$routeName} - MISSING");
            }
        }
        
        $this->info('');
        $this->info('💡 Note: Users routes are intentionally limited to index, show, roles, sync-roles, and toggle-root.');
        $this->info('   This is by design - users are managed through the main application, not created through AccessTree.');
    }
}
