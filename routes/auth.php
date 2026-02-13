<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    
});

Route::any('logout', App\Livewire\Actions\Logout::class)->name('logout');
