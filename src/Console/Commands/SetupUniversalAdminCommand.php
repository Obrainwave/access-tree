<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SetupUniversalAdminCommand extends Command
{
    protected $signature = 'accesstree:setup-universal-admin 
                            {--discover : Automatically discover and setup all tables}
                            {--exclude= : Comma-separated list of tables to exclude}
                            {--include= : Comma-separated list of tables to include only}';

    protected $description = 'Setup universal admin interface for all database tables';

    public function handle()
    {
        $this->info('🚀 Setting up Universal Admin Interface...');
        
        // Step 1: Discover tables
        $this->info('📊 Discovering database tables...');
        $tables = $this->discoverTables();
        
        if (empty($tables)) {
            $this->error('No tables found in the database.');
            return;
        }
        
        $this->info("Found " . count($tables) . " tables:");
        foreach ($tables as $table) {
            $this->line("  • {$table}");
        }
        
        // Step 2: Generate admin interfaces
        if ($this->option('discover')) {
            $this->info('🎨 Generating admin interfaces...');
            $this->generateAdminInterfaces($tables);
        }
        
        // Step 3: Create admin user if needed
        $this->info('👤 Checking admin user...');
        $this->createAdminUserIfNeeded();
        
        // Step 4: Clear caches
        $this->info('🧹 Clearing caches...');
        $this->clearCaches();
        
        $this->info('✅ Universal Admin Interface setup completed!');
        $this->displayAccessInfo();
    }
    
    private function discoverTables()
    {
        $excludeTables = $this->option('exclude') ? explode(',', $this->option('exclude')) : [];
        $includeTables = $this->option('include') ? explode(',', $this->option('include')) : [];
        
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        
        $tables = [];
        
        switch ($driver) {
            case 'mysql':
                $tables = DB::select('SHOW TABLES');
                $tables = array_map(function($table) {
                    return array_values((array)$table)[0];
                }, $tables);
                break;
                
            case 'pgsql':
                $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
                $tables = array_map(function($table) {
                    return $table->tablename;
                }, $tables);
                break;
                
            case 'sqlite':
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
                $tables = array_map(function($table) {
                    return $table->name;
                }, $tables);
                break;
        }
        
        // Default tables to exclude
        $defaultExclude = [
            'migrations', 'password_resets', 'failed_jobs', 
            'personal_access_tokens', 'sessions', 'cache', 
            'cache_locks', 'jobs', 'job_batches'
        ];
        
        $excludeTables = array_merge($excludeTables, $defaultExclude);
        
        $filtered = array_filter($tables, function($table) use ($excludeTables, $includeTables) {
            if (!empty($includeTables)) {
                return in_array($table, $includeTables);
            }
            return !in_array($table, $excludeTables);
        });
        
        return array_values($filtered);
    }
    
    private function generateAdminInterfaces($tables)
    {
        foreach ($tables as $table) {
            $this->line("  📝 Generating interface for {$table}...");
            $this->generateTableInterface($table);
        }
    }
    
    private function generateTableInterface($table)
    {
        // This would generate the specific views and routes for each table
        // For now, we'll use the universal controller approach
        $this->line("    ✅ Interface ready for {$table}");
    }
    
    private function createAdminUserIfNeeded()
    {
        $adminUser = DB::table('users')->where('email', 'admin@accesstree.com')->first();
        
        if (!$adminUser) {
            $this->info('  👤 Creating admin user...');
            
            $userId = DB::table('users')->insertGetId([
                'name' => 'Admin User',
                'email' => 'admin@accesstree.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Assign super admin role if it exists
            $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
            if ($superAdminRole) {
                DB::table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $superAdminRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $this->line('    ✅ Admin user created: admin@accesstree.com / password');
        } else {
            $this->line('    ✅ Admin user already exists');
        }
    }
    
    private function clearCaches()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            
            // Try to clear tagged cache
            try {
                \Illuminate\Support\Facades\Cache::tags(['accesstree'])->flush();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Cache::flush();
            }
            
            $this->line('    ✅ Caches cleared');
        } catch (\Exception $e) {
            $this->warn('    ⚠️ Some caches could not be cleared: ' . $e->getMessage());
        }
    }
    
    private function displayAccessInfo()
    {
        $this->info('');
        $this->info('🎉 Universal Admin Interface is ready!');
        $this->info('');
        $this->info('📋 Access Information:');
        $this->info('  • Admin URL: /admin/accesstree');
        $this->info('  • Login: admin@accesstree.com');
        $this->info('  • Password: password');
        $this->info('');
        $this->info('🔧 Available Commands:');
        $this->info('  • php artisan accesstree:discover-tables --generate-views');
        $this->info('  • php artisan accesstree:debug-routes');
        $this->info('  • php artisan accesstree:clear-cache');
        $this->info('');
        $this->info('📊 Your tables are now accessible via:');
        $this->info('  • /admin/tables - Overview of all tables');
        $this->info('  • /admin/tables/{table} - Manage specific table');
        $this->info('');
    }
}
