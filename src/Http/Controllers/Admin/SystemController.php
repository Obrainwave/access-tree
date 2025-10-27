<?php

namespace Obrainwave\AccessTree\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function settings()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'database_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
        ];

        // Get additional stats
        $stats = [];
        try {
            $stats['total_users'] = $this->getUserModel()::count();
        } catch (\Exception $e) {
            $stats['total_users'] = 0;
        }

        // Get storage information
        $storageInfo = $this->getStorageInfo();

        return view('accesstree::admin.system.settings', compact('systemInfo', 'stats', 'storageInfo'));
    }

    public function logs(Request $request)
    {
        $lines = $request->get('lines', 100);
        $lines = min(max(1, $lines), 1000); // Limit between 1 and 1000
        
        $logContent = $this->readLogFile($lines);

        return view('accesstree::admin.system.logs', compact('logContent', 'lines'));
    }

    public function refreshLogs(Request $request)
    {
        $lines = $request->get('lines', 100);
        $lines = min(max(1, $lines), 1000);
        
        $logContent = $this->readLogFile($lines);

        return response()->json([
            'success' => true,
            'content' => nl2br(e($logContent)),
            'lines' => $lines
        ]);
    }

    public function downloadLogs()
    {
        $logFile = $this->getLogFilePath();
        
        if (!file_exists($logFile)) {
            return redirect()->back()->with('error', 'Log file not found');
        }

        return response()->download($logFile, 'laravel.log', [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function clearLogs()
    {
        $logFile = $this->getLogFilePath();
        
        if (file_exists($logFile)) {
            File::put($logFile, '');
        }

        return redirect()->back()->with('success', 'Logs cleared successfully');
    }

    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');

        return response()->json([
            'success' => true,
            'message' => 'All caches cleared successfully'
        ]);
    }

    public function optimizeApp()
    {
        \Artisan::call('optimize:clear');
        \Artisan::call('config:cache');
        \Artisan::call('route:cache');
        \Artisan::call('view:cache');

        return response()->json([
            'success' => true,
            'message' => 'Application optimized successfully'
        ]);
    }

    private function readLogFile($lines = 100)
    {
        $logFile = $this->getLogFilePath();
        
        if (!file_exists($logFile)) {
            return 'Log file not found.';
        }

        // Read the last N lines
        $handle = fopen($logFile, 'r');
        if (!$handle) {
            return 'Unable to read log file.';
        }

        $lineArray = [];
        while (!feof($handle)) {
            $line = fgets($handle);
            if ($line !== false) {
                $lineArray[] = $line;
                // Keep only last N lines in memory
                if (count($lineArray) > $lines) {
                    array_shift($lineArray);
                }
            }
        }
        fclose($handle);

        return implode('', $lineArray);
    }

    private function getLogFilePath()
    {
        $logPath = storage_path('logs/laravel.log');
        
        // Try different possible log file names based on environment
        if (!file_exists($logPath)) {
            $env = app()->environment();
            $altPath = storage_path("logs/laravel-{$env}.log");
            if (file_exists($altPath)) {
                return $altPath;
            }
        }
        
        return $logPath;
    }

    private function getStorageInfo()
    {
        $storagePath = storage_path();
        $publicPath = public_path();
        
        $totalSize = 0;
        $dbSize = 0;

        // Calculate storage size
        try {
            if (is_dir($storagePath)) {
                $totalSize = $this->getDirectorySize($storagePath);
            }
        } catch (\Exception $e) {
            $totalSize = 0;
        }

        // Try to get database size
        try {
            $dbSize = $this->getDatabaseSize();
        } catch (\Exception $e) {
            $dbSize = 0;
        }

        return [
            'total_size' => $totalSize,
            'database_size' => $dbSize,
        ];
    }

    private function getDirectorySize($directory)
    {
        $size = 0;
        if (is_dir($directory)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        return $size;
    }

    private function getDatabaseSize()
    {
        try {
            $databaseName = config('database.connections.' . config('database.default') . '.database');
            $result = \DB::select("SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb 
                FROM information_schema.TABLES 
                WHERE table_schema = ?", [$databaseName]);
            
            return isset($result[0]) ? $result[0]->size_mb : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getUserModel()
    {
        $userModel = config('accesstree.user_model', 'App\\Models\\User');
        
        if (!class_exists($userModel)) {
            $alternatives = [
                'App\\User',
                'App\\Models\\User',
                'Illuminate\\Foundation\\Auth\\User'
            ];
            
            foreach ($alternatives as $alternative) {
                if (class_exists($alternative)) {
                    return $alternative;
                }
            }
            
            throw new \Exception('User model not found.');
        }
        
        return $userModel;
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
