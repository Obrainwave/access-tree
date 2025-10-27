<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class TestTableRoutesCommand extends Command
{
    protected $signature = 'accesstree:test-table-routes';
    protected $description = 'Test if the table routes are properly registered';

    public function handle()
    {
        $this->info('🧪 Testing table routes...');
        
        $routes = Route::getRoutes();
        $tableRoutes = [];
        
        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name && str_contains($name, 'accesstree.admin.tables')) {
                $tableRoutes[] = [
                    'name' => $name,
                    'uri' => $route->uri(),
                    'methods' => implode('|', $route->methods())
                ];
            }
        }
        
        if (empty($tableRoutes)) {
            $this->error('❌ No table routes found!');
            $this->info('This might indicate that the routes are not being loaded properly.');
            return;
        }
        
        $this->info('✅ Found ' . count($tableRoutes) . ' table routes:');
        
        foreach ($tableRoutes as $route) {
            $this->line("  • {$route['name']} ({$route['methods']}) - {$route['uri']}");
        }
        
        // Test specific routes
        $this->info('');
        $this->info('🔍 Testing specific routes:');
        
        $testRoutes = [
            'accesstree.admin.tables.overview',
            'accesstree.admin.tables.index',
            'accesstree.admin.tables.create',
            'accesstree.admin.tables.store',
            'accesstree.admin.tables.show',
            'accesstree.admin.tables.edit',
            'accesstree.admin.tables.update',
            'accesstree.admin.tables.destroy'
        ];
        
        foreach ($testRoutes as $routeName) {
            if (Route::has($routeName)) {
                $this->line("  ✅ {$routeName} - EXISTS");
            } else {
                $this->line("  ❌ {$routeName} - MISSING");
            }
        }
        
        $this->info('');
        $this->info('💡 If routes are missing, try:');
        $this->info('   php artisan route:clear');
        $this->info('   php artisan accesstree:clear-cache');
    }
}
