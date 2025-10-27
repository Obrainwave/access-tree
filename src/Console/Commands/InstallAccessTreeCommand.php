<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;

class InstallAccessTreeCommand extends Command
{
    protected $signature = 'accesstree:install 
                            {--with-admin : Install with admin interface}
                            {--with-gates : Install with Laravel Gates integration}
                            {--force : Overwrite existing files}';
    
    protected $description = 'Install AccessTree with optional admin interface and Gates integration';

    public function handle()
    {
        $this->info('Installing AccessTree...');

        // Publish migrations
        $this->call('vendor:publish', [
            '--tag' => 'accesstree-migrations',
            '--force' => $this->option('force')
        ]);

        // Run migrations
        $this->call('migrate');

        // Publish config
        $this->call('vendor:publish', [
            '--tag' => 'accesstree-config',
            '--force' => $this->option('force')
        ]);

        // Seed default data
        $this->call('accesstree:seed');

        if ($this->option('with-admin')) {
            $this->info('Installing admin interface...');
            $this->call('accesstree:install-admin', [
                '--force' => $this->option('force')
            ]);
        }

        if ($this->option('with-gates')) {
            $this->info('Laravel Gates integration is enabled by default.');
        }

        $this->info('AccessTree installed successfully!');
        
        if ($this->option('with-admin')) {
            $this->info('Admin interface available at: /admin/accesstree');
        }
        
        $this->info('Run "php artisan accesstree:seed" to create default permissions and roles.');
    }
}
