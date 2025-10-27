<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ClearAccessTreeCacheCommand extends Command
{
    protected $signature = 'accesstree:clear-cache';
    protected $description = 'Clear all AccessTree related caches and rebuild autoloader';

    public function handle()
    {
        $this->info('Clearing AccessTree caches...');

        try {
            // Clear Laravel caches
            Artisan::call('config:clear');
            $this->info('✓ Configuration cache cleared');

            Artisan::call('route:clear');
            $this->info('✓ Route cache cleared');

            Artisan::call('view:clear');
            $this->info('✓ View cache cleared');

            Artisan::call('cache:clear');
            $this->info('✓ Application cache cleared');

            // Clear AccessTree specific caches
            try {
                Cache::tags(['accesstree'])->flush();
                $this->info('✓ AccessTree cache cleared');
            } catch (\Exception $e) {
                // If tagging is not supported, clear all cache
                Cache::flush();
                $this->info('✓ All cache cleared (tagging not supported)');
            }

            // Rebuild autoloader
            $this->info('Rebuilding autoloader...');
            exec('composer dump-autoload');
            $this->info('✓ Autoloader rebuilt');

            $this->info('All caches cleared successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to clear caches: ' . $e->getMessage());
            return 1;
        }
    }
}
