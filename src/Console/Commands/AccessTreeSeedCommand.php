<?php
namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Obrainwave\AccessTree\Database\Seeders\PermissionSeeder as PackageSeeder;

class AccessTreeSeedCommand extends Command
{
    protected $signature = 'accesstree:seed
                            {--fresh : Run seeder after refreshing migrations}';

    protected $description = 'Seed default permissions, roles, and assign admin role based on config settings.';

    public function handle()
    {
        $this->info('Starting AccessTree Seeder...');

        if ($this->option('fresh')) {
            $this->warn('Running in FRESH mode.');
            $this->warn('Only AccessTree-related tables will be cleared (NOT the entire database).');

            $tables = config('accesstree.fresh_tables', []);

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->line("Truncated: {$table}");
                }
            }
        }

        // Check if developer has published the seeder
        $publishedSeederPath = database_path('seeders/PermissionSeeder.php');
        $seederClass         = 'Database\\Seeders\\PermissionSeeder';

        if (File::exists($publishedSeederPath)) {
            $this->info('Using published PermissionSeeder...');
        } else {
            $this->info('Using package PermissionSeeder...');
            $seederClass = PackageSeeder::class;
        }

        $this->callSilent('db:seed', [
            '--class' => $seederClass,
        ]);

        $this->info('AccessTree seeding completed successfully!');
    }

}
