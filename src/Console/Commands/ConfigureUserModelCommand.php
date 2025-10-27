<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigureUserModelCommand extends Command
{
    protected $signature = 'accesstree:configure-user-model 
                            {--model= : Specify the User model class}
                            {--auto-detect : Automatically detect the User model}';

    protected $description = 'Configure the User model for AccessTree';

    public function handle()
    {
        $this->info('🔧 Configuring User model for AccessTree...');
        
        if ($this->option('auto-detect')) {
            $this->autoDetectUserModel();
        } elseif ($this->option('model')) {
            $this->setUserModel($this->option('model'));
        } else {
            $this->interactiveConfiguration();
        }
    }
    
    private function autoDetectUserModel()
    {
        $this->info('🔍 Auto-detecting User model...');
        
        $commonModels = [
            'App\\Models\\User',
            'App\\User',
            'Illuminate\\Foundation\\Auth\\User'
        ];
        
        foreach ($commonModels as $model) {
            if (class_exists($model)) {
                $this->info("✅ Found User model: {$model}");
                $this->setUserModel($model);
                return;
            }
        }
        
        $this->error('❌ No User model found in common locations.');
        $this->info('Please specify the User model manually:');
        $this->info('php artisan accesstree:configure-user-model --model="Your\\User\\Model"');
    }
    
    private function interactiveConfiguration()
    {
        $this->info('Please specify the User model class:');
        
        $model = $this->ask('User model class', 'App\\Models\\User');
        
        if (!class_exists($model)) {
            $this->error("❌ Class '{$model}' not found.");
            
            if ($this->confirm('Would you like to try auto-detection?')) {
                $this->autoDetectUserModel();
                return;
            }
            
            $this->error('Please ensure the User model exists and try again.');
            return;
        }
        
        $this->setUserModel($model);
    }
    
    private function setUserModel($model)
    {
        $this->info("⚙️ Setting User model to: {$model}");
        
        // Update the config file
        $configPath = config_path('accesstree.php');
        
        if (!File::exists($configPath)) {
            $this->error('❌ AccessTree config file not found. Please publish the config first.');
            $this->info('Run: php artisan vendor:publish --tag=accesstree-config');
            return;
        }
        
        $config = File::get($configPath);
        
        // Update the user_model configuration
        $config = preg_replace(
            "/'user_model' => env\('ACCESSTREE_USER_MODEL', '[^']*'\),/",
            "'user_model' => env('ACCESSTREE_USER_MODEL', '{$model}'),",
            $config
        );
        
        File::put($configPath, $config);
        
        $this->info('✅ User model configured successfully!');
        $this->info("📝 You can also set this in your .env file:");
        $this->info("   ACCESSTREE_USER_MODEL={$model}");
        
        // Test the configuration
        $this->testUserModel($model);
    }
    
    private function testUserModel($model)
    {
        $this->info('🧪 Testing User model configuration...');
        
        try {
            $user = new $model();
            $this->info('✅ User model instantiated successfully');
            
            // Check if the model has required methods
            if (method_exists($user, 'roles')) {
                $this->info('✅ User model has roles relationship');
            } else {
                $this->warn('⚠️ User model may not have roles relationship');
            }
            
            if (method_exists($user, 'is_root_user')) {
                $this->info('✅ User model has is_root_user attribute');
            } else {
                $this->warn('⚠️ User model may not have is_root_user attribute');
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error testing User model: {$e->getMessage()}");
        }
    }
}
