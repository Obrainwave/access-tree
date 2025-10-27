<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class TestRouteAccessCommand extends Command
{
    protected $signature = 'accesstree:test-route-access {table} {id=1}';
    protected $description = 'Test actual route access for universal tables';

    public function handle()
    {
        $table = $this->argument('table');
        $id = $this->argument('id');
        
        $this->info("Testing route access for table: {$table}, ID: {$id}");
        
        // Check if table exists
        try {
            $exists = DB::select("SHOW TABLES LIKE '{$table}'");
            if (empty($exists)) {
                $this->error("✗ Table '{$table}' does not exist");
                return 1;
            }
            $this->info("✓ Table '{$table}' exists");
        } catch (\Exception $e) {
            $this->error("✗ Error checking table: " . $e->getMessage());
            return 1;
        }
        
        // Check if record exists
        try {
            $record = DB::table($table)->where('id', $id)->first();
            if (!$record) {
                $this->warn("⚠ Record with ID {$id} not found in table '{$table}'");
                $this->info("Available IDs:");
                $ids = DB::table($table)->pluck('id')->take(5);
                foreach ($ids as $availableId) {
                    $this->line("  - {$availableId}");
                }
            } else {
                $this->info("✓ Record with ID {$id} exists");
            }
        } catch (\Exception $e) {
            $this->error("✗ Error checking record: " . $e->getMessage());
        }
        
        // Test route generation
        try {
            $showRoute = route('accesstree.admin.tables.show', [$table, $id]);
            $this->info("✓ Show route: {$showRoute}");
        } catch (\Exception $e) {
            $this->error("✗ Show route failed: " . $e->getMessage());
        }
        
        try {
            $editRoute = route('accesstree.admin.tables.edit', [$table, $id]);
            $this->info("✓ Edit route: {$editRoute}");
        } catch (\Exception $e) {
            $this->error("✗ Edit route failed: " . $e->getMessage());
        }
        
        // Check managed tables config
        $managedTables = config('accesstree.managed_tables', []);
        if (empty($managedTables)) {
            $this->info("✓ All tables are managed (no restrictions)");
        } else {
            if (in_array($table, $managedTables)) {
                $this->info("✓ Table '{$table}' is in managed tables list");
            } else {
                $this->error("✗ Table '{$table}' is NOT in managed tables list");
                $this->info("Managed tables: " . implode(', ', $managedTables));
            }
        }
        
        return 0;
    }
}
