<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;

class TestUserModelCommand extends Command
{
    protected $signature = 'accesstree:test-user-model';
    protected $description = 'Test User model configuration and resolution';

    public function handle()
    {
        $this->info('🧪 Testing User model configuration...');
        
        // Test 1: Check config
        $this->info('📋 Configuration:');
        $userModel = config('accesstree.user_model', 'App\\Models\\User');
        $this->line("  • Config value: {$userModel}");
        
        // Test 2: Check if class exists
        $this->info('🔍 Class existence:');
        if (class_exists($userModel)) {
            $this->info("  ✅ Class '{$userModel}' exists");
        } else {
            $this->error("  ❌ Class '{$userModel}' not found");
            
            // Try alternatives
            $this->info('🔍 Trying alternatives:');
            $alternatives = [
                'App\\Models\\User',
                'App\\User',
                'Illuminate\\Foundation\\Auth\\User'
            ];
            
            foreach ($alternatives as $alternative) {
                if (class_exists($alternative)) {
                    $this->info("  ✅ Found: {$alternative}");
                } else {
                    $this->line("  ❌ Not found: {$alternative}");
                }
            }
        }
        
        // Test 3: Try to instantiate
        if (class_exists($userModel)) {
            $this->info('🏗️ Instantiation test:');
            try {
                $user = new $userModel();
                $this->info("  ✅ Successfully instantiated '{$userModel}'");
                
                // Test 4: Check for required methods/attributes
                $this->info('🔧 Model capabilities:');
                
                if (method_exists($user, 'roles')) {
                    $this->info("  ✅ Has 'roles' relationship");
                } else {
                    $this->warn("  ⚠️ Missing 'roles' relationship");
                }
                
                if (property_exists($user, 'is_root_user') || method_exists($user, 'getIsRootUserAttribute')) {
                    $this->info("  ✅ Has 'is_root_user' attribute");
                } else {
                    $this->warn("  ⚠️ Missing 'is_root_user' attribute");
                }
                
            } catch (\Exception $e) {
                $this->error("  ❌ Failed to instantiate: {$e->getMessage()}");
            }
        }
        
        // Test 5: Check UserController
        $this->info('🎛️ UserController test:');
        try {
            $controller = new \Obrainwave\AccessTree\Http\Controllers\Admin\UserController(
                app(\Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface::class)
            );
            $this->info("  ✅ UserController instantiated successfully");
            $this->line("  • Model class: " . $controller->model);
        } catch (\Exception $e) {
            $this->error("  ❌ UserController failed: {$e->getMessage()}");
        }
        
        $this->info('✅ User model test completed!');
    }
}
