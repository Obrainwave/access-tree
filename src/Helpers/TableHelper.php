<?php

namespace Obrainwave\AccessTree\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TableHelper
{
    /**
     * Get all user tables with formatted names
     */
    public static function getUserTables(): array
    {
        $managedTables = config('accesstree.managed_tables', []);
        
        return Cache::remember('accesstree.user_tables', 300, function () use ($managedTables) {
            $tables = DB::select('SHOW TABLES');
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables);
            
            // Filter out system tables
            $excludeTables = [
                'migrations', 'password_resets', 'failed_jobs', 
                'personal_access_tokens', 'sessions', 'cache', 
                'cache_locks', 'jobs', 'job_batches', 'permissions',
                'roles', 'role_has_permissions', 'user_roles'
            ];
            
            $userTables = array_filter($tableNames, function($table) use ($excludeTables) {
                return !in_array($table, $excludeTables);
            });
            
            // If specific tables are configured, filter to only those tables
            if (!empty($managedTables) && is_array($managedTables)) {
                $userTables = array_filter($userTables, function($table) use ($managedTables) {
                    return in_array($table, $managedTables);
                });
            }
            
            // Format table names for display
            $formattedTables = [];
            foreach ($userTables as $table) {
                $formattedTables[$table] = self::formatTableName($table);
            }
            
            return $formattedTables;
        });
    }
    
    /**
     * Format table name for display
     */
    public static function formatTableName(string $tableName): string
    {
        // Convert snake_case to Title Case
        return Str::title(str_replace('_', ' ', $tableName));
    }
    
    /**
     * Get table icon based on table name
     */
    public static function getTableIcon(string $tableName): string
    {
        $iconMap = [
            'posts' => 'fas fa-newspaper',
            'products' => 'fas fa-box',
            'orders' => 'fas fa-shopping-cart',
            'users' => 'fas fa-users',
            'categories' => 'fas fa-tags',
            'comments' => 'fas fa-comments',
            'reviews' => 'fas fa-star',
            'wallets' => 'fas fa-wallet',
            'transactions' => 'fas fa-exchange-alt',
            'notifications' => 'fas fa-bell',
            'settings' => 'fas fa-cog',
            'logs' => 'fas fa-file-alt',
        ];
        
        $lowerTable = strtolower($tableName);
        
        // Check for exact matches first
        if (isset($iconMap[$lowerTable])) {
            return $iconMap[$lowerTable];
        }
        
        // Check for partial matches
        foreach ($iconMap as $key => $icon) {
            if (str_contains($lowerTable, $key)) {
                return $icon;
            }
        }
        
        // Default icon
        return 'fas fa-table';
    }
    
    /**
     * Clear the table cache
     */
    public static function clearCache(): void
    {
        Cache::forget('accesstree.user_tables');
    }
}
