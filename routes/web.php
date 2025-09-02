<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');
    
    Route::get('/users/trashed', function () {
        return view('users.trashed');
    })->name('users.trashed');

    Route::get('/roles', function () {
        return view('roles.index');
    })->name('roles.index');
    
    Route::get('/roles/trashed', function () {
        return view('roles.trashed');
    })->name('roles.trashed');
    
    Route::get('/permissions', function () {
        return view('permissions.index');
    })->name('permissions.index');

    Route::get('/permissions/trashed', function () {
        return view('permissions.trashed');
    })->name('permissions.trashed');
});
