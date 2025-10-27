<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;

class CleanupTestDataCommand extends Command
{
    protected $signature = 'accesstree:cleanup-test-data 
                            {--force : Force cleanup without confirmation}';
    protected $description = 'Clean up test data from AccessTree';

    public function handle()
    {
        $this->info('🧹 Cleaning up AccessTree test data...');
        
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete all test permissions and roles. Continue?')) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }
        
        $deletedPermissions = 0;
        $deletedRoles = 0;
        
        try {
            // Delete test permissions
            $testPermissions = Permission::where('name', 'like', 'Test Permission%')
                ->orWhere('name', 'like', 'Test%')
                ->get();
                
            foreach ($testPermissions as $permission) {
                $permission->delete();
                $deletedPermissions++;
            }
            
            // Delete test roles
            $testRoles = Role::where('name', 'like', 'Test Role%')
                ->orWhere('name', 'like', 'Test%')
                ->get();
                
            foreach ($testRoles as $role) {
                $role->delete();
                $deletedRoles++;
            }
            
            $this->info("✅ Cleanup completed:");
            $this->line("  • Deleted {$deletedPermissions} test permissions");
            $this->line("  • Deleted {$deletedRoles} test roles");
            
        } catch (\Exception $e) {
            $this->error("❌ Cleanup failed: {$e->getMessage()}");
            return 1;
        }
        
        $this->info('🎉 Test data cleanup completed successfully!');
        return 0;
    }
}
