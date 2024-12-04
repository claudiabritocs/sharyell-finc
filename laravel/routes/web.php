<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MetasController;

use Illuminate\Support\Facades\Route;


Route::get('/', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

//ROTAS AUTENTICADAS
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Rotas de inclusão de valores direto da Dashboard
    Route::post('/dashboard/earn', [DashboardController::class, 'earnAdd'])->name('dashboard-earn');
    Route::post('/dashboard/spend', [DashboardController::class, 'spendAdd'])->name('dashboard-spend');
    Route::post('/dashboard/setGoal', [DashboardController::class, 'setGoal'])->name('dashboard-goal');

    //CRUD de Metas por usuário
    Route::resource('metas', MetasController::class);
});


//Rotas de Login
require __DIR__.'/auth.php';
