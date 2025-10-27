<?php

namespace Obrainwave\AccessTree\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $stats = [
            'permissions' => Permission::count(),
            'roles' => Role::count(),
            'users' => $this->getUserModel()::count(),
            'active_permissions' => Permission::where('status', 1)->count(),
            'active_roles' => Role::where('status', 1)->count(),
            'managed_tables_count' => $this->getManagedTablesCount(),
            'managed_tables' => $this->getManagedTablesStats(),
        ];

        // Get recent activity
        $recentActivity = $this->getRecentActivity();

        return view('accesstree::admin.dashboard', compact('stats', 'recentActivity'));
    }
    
    protected function getManagedTablesCount()
    {
        $managedTables = config('accesstree.managed_tables', []);
        
        if (empty($managedTables)) {
            // Return count of all user tables (excluding system tables)
            $excludeTables = [
                'migrations', 'password_resets', 'failed_jobs', 
                'personal_access_tokens', 'sessions', 'cache', 
                'cache_locks', 'jobs', 'job_batches', 'permissions',
                'roles', 'role_has_permissions', 'user_roles'
            ];
            
            $allTables = \DB::select('SHOW TABLES');
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, $allTables);
            
            $userTables = array_filter($tableNames, function($table) use ($excludeTables) {
                return !in_array($table, $excludeTables);
            });
            
            return count($userTables);
        }
        
        return count($managedTables);
    }
    
    protected function getManagedTablesStats()
    {
        $managedTables = config('accesstree.managed_tables', []);
        $dashboardCards = config('accesstree.dashboard_table_cards', []);
        
        if (empty($managedTables)) {
            // Get all user tables
            $excludeTables = [
                'migrations', 'password_resets', 'failed_jobs', 
                'personal_access_tokens', 'sessions', 'cache', 
                'cache_locks', 'jobs', 'job_batches', 'permissions',
                'roles', 'role_has_permissions', 'user_roles'
            ];
            
            $allTables = \DB::select('SHOW TABLES');
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, $allTables);
            
            $userTables = array_filter($tableNames, function($table) use ($excludeTables) {
                return !in_array($table, $excludeTables);
            });
            
            $managedTables = array_values($userTables);
        }
        
        // Filter to only show tables specified in dashboard_table_cards (if configured)
        if (!empty($dashboardCards) && is_array($dashboardCards)) {
            $managedTables = array_filter($managedTables, function($table) use ($dashboardCards) {
                return in_array($table, $dashboardCards);
            });
        }
        
        // Get record counts for each managed table
        $stats = [];
        foreach ($managedTables as $table) {
            try {
                $count = \DB::table($table)->count();
                $stats[] = [
                    'name' => ucfirst(str_replace('_', ' ', $table)),
                    'table' => $table,
                    'count' => $count
                ];
            } catch (\Exception $e) {
                // Skip tables that can't be accessed
                continue;
            }
        }
        
        return $stats;
    }
    
    protected function getUserModel()
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
            
            throw new \Exception('User model not found. Please configure the user model in config/accesstree.php');
        }
        
        return $userModel;
    }
    
    protected function getRecentActivity()
    {
        $activities = [];
        
        // Get recent permissions
        $recentPermissions = Permission::latest('created_at')->limit(3)->get();
        foreach ($recentPermissions as $permission) {
            $activities[] = [
                'title' => "Permission '{$permission->name}' created",
                'time' => $permission->created_at->diffForHumans(),
                'icon' => 'fas fa-key',
                'color' => 'activity-icon-success'
            ];
        }
        
        // Get recent roles
        $recentRoles = Role::latest('created_at')->limit(3)->get();
        foreach ($recentRoles as $role) {
            $activities[] = [
                'title' => "Role '{$role->name}' created",
                'time' => $role->created_at->diffForHumans(),
                'icon' => 'fas fa-users-cog',
                'color' => 'activity-icon-info'
            ];
        }
        
        // Get recent users
        try {
            $recentUsers = $this->getUserModel()::latest('created_at')->limit(3)->get();
            foreach ($recentUsers as $user) {
                $activities[] = [
                    'title' => "User '{$user->name}' registered",
                    'time' => $user->created_at->diffForHumans(),
                    'icon' => 'fas fa-user-plus',
                    'color' => 'activity-icon-warning'
                ];
            }
        } catch (\Exception $e) {
            // Skip if user model not found
        }
        
        // Sort by time and limit to 10 most recent
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activities, 0, 10);
    }
}
