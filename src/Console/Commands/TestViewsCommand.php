<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;

class TestViewsCommand extends Command
{
    protected $signature = 'accesstree:test-views';
    protected $description = 'Test if AccessTree views are properly accessible';

    public function handle()
    {
        $this->info('🧪 Testing AccessTree views...');
        
        $views = [
            'accesstree::admin.universal.index',
            'accesstree::admin.universal.form',
            'accesstree::admin.universal.show',
            'accesstree::admin.tables.index',
            'accesstree::admin.layouts.app',
            'accesstree::admin.dashboard',
        ];
        
        $allPassed = true;
        
        foreach ($views as $view) {
            try {
                if (View::exists($view)) {
                    $this->info("✅ {$view} - EXISTS");
                } else {
                    $this->error("❌ {$view} - NOT FOUND");
                    $allPassed = false;
                }
            } catch (\Exception $e) {
                $this->error("❌ {$view} - ERROR: {$e->getMessage()}");
                $allPassed = false;
            }
        }
        
        if ($allPassed) {
            $this->info('🎉 All views are accessible!');
        } else {
            $this->error('❌ Some views are missing or inaccessible.');
            $this->info('💡 Try running: php artisan accesstree:clear-cache');
        }
        
        return $allPassed ? 0 : 1;
    }
}
