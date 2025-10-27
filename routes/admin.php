<?php

use Illuminate\Support\Facades\Route;
use Obrainwave\AccessTree\Http\Controllers\Admin\AdminController;
use Obrainwave\AccessTree\Http\Controllers\Admin\AuthController;
use Obrainwave\AccessTree\Http\Controllers\Admin\PermissionController;
use Obrainwave\AccessTree\Http\Controllers\Admin\RoleController;
use Obrainwave\AccessTree\Http\Controllers\Admin\UserController;
use Obrainwave\AccessTree\Http\Controllers\Admin\SystemController;

// Authentication routes (no auth middleware)
Route::prefix('admin/accesstree')
     ->name('accesstree.admin.')
     ->middleware(['web'])
     ->group(function () {
         
         // Auth routes
         Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
         Route::post('login', [AuthController::class, 'login']);
         Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
         Route::post('register', [AuthController::class, 'register']);
         Route::post('logout', [AuthController::class, 'logout'])->name('logout');
     });

// Protected admin routes
Route::prefix('admin/accesstree')
     ->name('accesstree.admin.')
     ->middleware(['web', 'accesstree.admin'])
     ->group(function () {
         
         // Dashboard
         Route::get('/', [AdminController::class, 'index'])->name('dashboard');
         
         // Permissions
         Route::resource('permissions', PermissionController::class);
         
         // Roles
         Route::resource('roles', RoleController::class);
         
        // Users
        Route::resource('users', UserController::class);
        Route::get('users/{user}/roles', [UserController::class, 'showRoles'])->name('users.roles');
        Route::post('users/{user}/roles', [UserController::class, 'syncRoles'])->name('users.sync-roles');
        Route::post('users/{user}/toggle-root', [UserController::class, 'toggleRootUser'])->name('users.toggle-root');

        // System routes
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('settings', [SystemController::class, 'settings'])->name('settings');
            Route::get('logs', [SystemController::class, 'logs'])->name('logs');
            Route::get('logs/refresh', [SystemController::class, 'refreshLogs'])->name('logs.refresh');
            Route::get('logs/download', [SystemController::class, 'downloadLogs'])->name('logs.download');
            Route::post('logs/clear', [SystemController::class, 'clearLogs'])->name('logs.clear');
            Route::post('clear-cache', [SystemController::class, 'clearCache'])->name('clear-cache');
            Route::post('optimize', [SystemController::class, 'optimizeApp'])->name('optimize');
        });
     });
