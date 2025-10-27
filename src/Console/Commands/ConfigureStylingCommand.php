<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;

class ConfigureStylingCommand extends Command
{
    protected $signature = 'accesstree:configure-styling 
                            {--framework= : CSS framework (bootstrap, tailwind, custom)}
                            {--theme= : Theme (modern, classic, minimal)}
                            {--dark-mode : Enable dark mode}
                            {--no-animations : Disable animations}';
    
    protected $description = 'Configure AccessTree admin interface styling';

    public function handle()
    {
        $this->info('🎨 AccessTree Styling Configuration');
        $this->line('');

        // Get current configuration
        $config = config('accesstree.styling', []);
        
        // Framework selection
        $framework = $this->option('framework') ?: $this->choice(
            'Select CSS Framework',
            ['bootstrap', 'tailwind', 'custom'],
            $config['framework'] ?? 'bootstrap'
        );

        // Theme selection
        $theme = $this->option('theme') ?: $this->choice(
            'Select Theme',
            ['modern', 'classic', 'minimal'],
            $config['theme'] ?? 'modern'
        );

        // Dark mode
        $darkMode = $this->option('dark-mode') ?: $this->confirm(
            'Enable dark mode?',
            $config['dark_mode'] ?? false
        );

        // Animations
        $animations = !$this->option('no-animations') && $this->confirm(
            'Enable animations?',
            $config['animations'] ?? true
        );

        // Custom CSS
        $customCss = $this->ask('Custom CSS (optional)', $config['custom_css'] ?? '');
        
        // Custom JS
        $customJs = $this->ask('Custom JavaScript (optional)', $config['custom_js'] ?? '');

        // Update configuration
        $newConfig = [
            'framework' => $framework,
            'theme' => $theme,
            'dark_mode' => $darkMode,
            'animations' => $animations,
            'custom_css' => $customCss,
            'custom_js' => $customJs,
        ];

        // Update config file
        $configPath = config_path('accesstree.php');
        $configContent = file_get_contents($configPath);
        
        // Replace styling configuration
        $stylingConfig = "'styling' => [\n";
        $stylingConfig .= "        'framework' => env('ACCESSTREE_CSS_FRAMEWORK', '{$framework}'),\n";
        $stylingConfig .= "        'theme' => env('ACCESSTREE_THEME', '{$theme}'),\n";
        $stylingConfig .= "        'custom_css' => env('ACCESSTREE_CUSTOM_CSS', ''),\n";
        if ($customCss) {
            $stylingConfig .= " // {$customCss}";
        }
        $stylingConfig .= "\n";
        $stylingConfig .= "        'custom_js' => env('ACCESSTREE_CUSTOM_JS', ''),\n";
        if ($customJs) {
            $stylingConfig .= " // {$customJs}";
        }
        $stylingConfig .= "\n";
        $stylingConfig .= "        'dark_mode' => env('ACCESSTREE_DARK_MODE', " . ($darkMode ? 'true' : 'false') . "),\n";
        $stylingConfig .= "        'animations' => env('ACCESSTREE_ANIMATIONS', " . ($animations ? 'true' : 'false') . "),\n";
        $stylingConfig .= "    ],";

        // Find and replace the styling section
        $pattern = "/'styling' => \[[\s\S]*?\],/";
        $configContent = preg_replace($pattern, $stylingConfig, $configContent);
        
        file_put_contents($configPath, $configContent);

        $this->info('✅ Styling configuration updated!');
        $this->line('');
        
        $this->info('📋 Configuration Summary:');
        $this->line("   Framework: {$framework}");
        $this->line("   Theme: {$theme}");
        $this->line("   Dark Mode: " . ($darkMode ? 'Enabled' : 'Disabled'));
        $this->line("   Animations: " . ($animations ? 'Enabled' : 'Disabled'));
        
        if ($customCss) {
            $this->line("   Custom CSS: {$customCss}");
        }
        
        if ($customJs) {
            $this->line("   Custom JS: {$customJs}");
        }

        $this->line('');
        $this->info('💡 You can also set these via environment variables:');
        $this->line('   ACCESSTREE_CSS_FRAMEWORK=' . $framework);
        $this->line('   ACCESSTREE_THEME=' . $theme);
        $this->line('   ACCESSTREE_DARK_MODE=' . ($darkMode ? 'true' : 'false'));
        $this->line('   ACCESSTREE_ANIMATIONS=' . ($animations ? 'true' : 'false'));

        return 0;
    }
}
