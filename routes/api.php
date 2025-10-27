<?php

use Illuminate\Support\Facades\Route;
use Obrainwave\AccessTree\Http\Controllers\Api\PermissionController;
use Obrainwave\AccessTree\Http\Controllers\Api\RoleController;
use Obrainwave\AccessTree\Http\Controllers\Api\UserController;

Route::prefix('api/accesstree')
     ->name('accesstree.api.')
     ->middleware(['api', 'auth:sanctum'])
     ->group(function () {
         
         // Permissions API
         Route::apiResource('permissions', PermissionController::class);
         
         // Roles API
         Route::apiResource('roles', RoleController::class);
         
         // Users API
         Route::get('users', [UserController::class, 'index'])->name('users.index');
         Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
         Route::post('users/{user}/roles', [UserController::class, 'syncRoles'])->name('users.sync-roles');
     });
