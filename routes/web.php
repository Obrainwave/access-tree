<?php

use Illuminate\Support\Facades\Route;
use Obrainwave\AccessTree\Http\Controllers\PermissionController;

Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
Route::get('/permissions/{permission_id}', [PermissionController::class, 'show'])->name('permissions.show');
Route::post('/create-permission', [PermissionController::class, 'createPermission'])->name('permissions.create.permission');
