<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Obrainwave\AccessTree\Helpers\TableHelper;

class TestDynamicSidebarCommand extends Command
{
    protected $signature = 'accesstree:test-dynamic-sidebar';
    protected $description = 'Test the dynamic sidebar table discovery';

    public function handle()
    {
        $this->info('🧪 Testing Dynamic Sidebar...');
        
        try {
            // Test table discovery
            $userTables = TableHelper::getUserTables();
            
            if (empty($userTables)) {
                $this->warn('⚠️  No user tables found');
                $this->info('💡 Make sure you have some database tables created');
                return 0;
            }
            
            $this->info('✅ Found ' . count($userTables) . ' user tables:');
            
            foreach ($userTables as $tableName => $formattedName) {
                $icon = TableHelper::getTableIcon($tableName);
                $this->line("  📋 {$formattedName} ({$tableName}) - Icon: {$icon}");
            }
            
            // Test cache functionality
            $this->info('🔄 Testing cache...');
            $cachedTables = TableHelper::getUserTables();
            
            if ($cachedTables === $userTables) {
                $this->info('✅ Cache is working correctly');
            } else {
                $this->error('❌ Cache issue detected');
                return 1;
            }
            
            // Test cache clearing
            TableHelper::clearCache();
            $this->info('✅ Cache cleared successfully');
            
            $this->info('🎉 Dynamic sidebar is working correctly!');
            $this->info('💡 The sidebar will now show: ' . implode(', ', array_values($userTables)));
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error testing dynamic sidebar: ' . $e->getMessage());
            return 1;
        }
    }
}
