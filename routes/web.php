<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('welcome');

    Route::resource('users', UserController::class);

    Route::resource('roles', RoleController::class);
    
    Route::get('/users/{user}/roles', [RoleController::class, 'getUserRoles'])->name('users.roles');
    Route::post('/users/{user}/roles', [RoleController::class, 'assignToUser'])->name('users.roles.update');
});

require __DIR__.'/auth.php';
