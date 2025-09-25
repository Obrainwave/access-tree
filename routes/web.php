<?php

use Illuminate\Support\Facades\Route;

Route::get('/accesstree/error', function () {
    $message = session('message', 'An unknown error occurred.');
    return view('accesstree::error', compact('message')); // Note the ::
})->name('accesstree.error');