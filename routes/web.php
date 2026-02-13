<?php
use App\Livewire\Dashboard;
use App\Livewire\UserCrud;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'auth.session',])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/users', UserCrud::class)->name('users');
    
   
});

require __DIR__ . '/auth.php';
