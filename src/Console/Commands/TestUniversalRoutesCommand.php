<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class TestUniversalRoutesCommand extends Command
{
    protected $signature = 'accesstree:test-universal-routes';
    protected $description = 'Test universal table routes';

    public function handle()
    {
        $this->info('Testing Universal Table Routes...');
        
        // Test route generation
        try {
            $showRoute = route('accesstree.admin.tables.show', ['posts', 1]);
            $this->info("✓ Show route: {$showRoute}");
        } catch (\Exception $e) {
            $this->error("✗ Show route failed: " . $e->getMessage());
        }
        
        try {
            $editRoute = route('accesstree.admin.tables.edit', ['posts', 1]);
            $this->info("✓ Edit route: {$editRoute}");
        } catch (\Exception $e) {
            $this->error("✗ Edit route failed: " . $e->getMessage());
        }
        
        try {
            $createRoute = route('accesstree.admin.tables.create', ['posts']);
            $this->info("✓ Create route: {$createRoute}");
        } catch (\Exception $e) {
            $this->error("✗ Create route failed: " . $e->getMessage());
        }
        
        try {
            $indexRoute = route('accesstree.admin.tables.index', ['posts']);
            $this->info("✓ Index route: {$indexRoute}");
        } catch (\Exception $e) {
            $this->error("✗ Index route failed: " . $e->getMessage());
        }
        
        // List all table routes
        $this->info("\nAll table routes:");
        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            if (str_contains($route->getName(), 'accesstree.admin.tables')) {
                $methods = implode('|', $route->methods());
                $this->line("  {$methods} {$route->uri} -> {$route->getName()}");
            }
        }
        
        return 0;
    }
}