<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestActualRouteCommand extends Command
{
    protected $signature = 'accesstree:test-actual-route {table} {id}';
    protected $description = 'Test actual route execution';

    public function handle()
    {
        $table = $this->argument('table');
        $id = $this->argument('id');
        
        $this->info("Testing actual route execution for: {$table}/{$id}");
        
        try {
            // Create a request
            $request = Request::create("/admin/accesstree/tables/{$table}/{$id}", 'GET');
            
            // Get the route
            $route = Route::getRoutes()->match($request);
            
            if ($route) {
                $this->info("✓ Route matched: " . $route->getName());
                $this->info("✓ Controller: " . $route->getActionName());
                $this->info("✓ Middleware: " . implode(', ', $route->gatherMiddleware()));
            } else {
                $this->error("✗ No route matched");
            }
            
        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
        }
        
        // Test with edit route
        try {
            $request = Request::create("/admin/accesstree/tables/{$table}/{$id}/edit", 'GET');
            $route = Route::getRoutes()->match($request);
            
            if ($route) {
                $this->info("✓ Edit route matched: " . $route->getName());
            } else {
                $this->error("✗ Edit route not matched");
            }
            
        } catch (\Exception $e) {
            $this->error("✗ Edit route error: " . $e->getMessage());
        }
        
        return 0;
    }
}
