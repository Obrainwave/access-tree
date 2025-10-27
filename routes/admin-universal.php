<?php

use Illuminate\Support\Facades\Route;
use Obrainwave\AccessTree\Http\Controllers\Admin\UniversalTableController;

// Universal table management routes
// This will handle any table dynamically

Route::prefix('admin/accesstree')
    ->name('accesstree.admin.')
    ->middleware(['web', 'accesstree.admin'])
    ->group(function () {
    
    // Universal table routes - handles any table
    // Note: More specific routes must be defined before generic ones
    Route::get('tables/{table}/create', [UniversalTableController::class, 'create'])->name('tables.create');
    Route::get('tables/{table}/{id}/edit', [UniversalTableController::class, 'edit'])->name('tables.edit');
    Route::get('tables/{table}/{id}', [UniversalTableController::class, 'show'])->name('tables.show');
    Route::post('tables/{table}', [UniversalTableController::class, 'store'])->name('tables.store');
    Route::put('tables/{table}/{id}', [UniversalTableController::class, 'update'])->name('tables.update');
    Route::delete('tables/{table}/{id}', [UniversalTableController::class, 'destroy'])->name('tables.destroy');
    Route::get('tables/{table}', [UniversalTableController::class, 'index'])->name('tables.index');
    
    // Table discovery and management
    Route::get('tables', function () {
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        $tableNames = array_map(function($table) {
            return array_values((array)$table)[0];
        }, $tables);
        
        // Filter out system tables
        $excludeTables = [
            'migrations', 'password_resets', 'failed_jobs', 
            'personal_access_tokens', 'sessions', 'cache', 
            'cache_locks', 'jobs', 'job_batches'
        ];
        
        $userTables = array_filter($tableNames, function($table) use ($excludeTables) {
            return !in_array($table, $excludeTables);
        });
        
        return view('accesstree::admin.tables.index', [
            'tables' => array_values($userTables)
        ]);
    })->name('tables.overview');
    
});
