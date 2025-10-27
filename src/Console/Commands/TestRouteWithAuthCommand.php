<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestRouteWithAuthCommand extends Command
{
    protected $signature = 'accesstree:test-route-with-auth {table} {id}';
    protected $description = 'Test route with authentication';

    public function handle()
    {
        $table = $this->argument('table');
        $id = $this->argument('id');
        
        $this->info("Testing route with authentication for: {$table}/{$id}");
        
        // Check if user is authenticated
        if (Auth::check()) {
            $this->info("✓ User is authenticated: " . Auth::user()->email);
        } else {
            $this->warn("⚠ User is NOT authenticated");
            $this->info("This is likely why you're getting 404 - the middleware is redirecting to login");
        }
        
        // Check if there are any users in the database
        try {
            $userCount = DB::table('users')->count();
            $this->info("Users in database: {$userCount}");
            
            if ($userCount > 0) {
                $users = DB::table('users')->select('id', 'name', 'email')->take(3)->get();
                $this->info("Sample users:");
                foreach ($users as $user) {
                    $this->line("  - ID: {$user->id}, Name: {$user->name}, Email: {$user->email}");
                }
            }
        } catch (\Exception $e) {
            $this->error("Error checking users: " . $e->getMessage());
        }
        
        // Test the actual route URL
        $url = url("/admin/accesstree/tables/{$table}/{$id}");
        $this->info("Full URL: {$url}");
        
        return 0;
    }
}
