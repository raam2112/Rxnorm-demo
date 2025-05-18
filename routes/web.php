<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DrugSearchController;

Route::get('/', function () {
    return view('drugs.search');
});

//Route::get('/', [DrugSearchController::class, 'showForm'])->name('drug.search.form');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);

use App\Http\Controllers\UserMedicationWebController;

Route::middleware('auth')->group(function () {
    Route::get('/user/drugs', [UserMedicationWebController::class, 'index'])->name('drugs.index');
    Route::get('/user/drugs/create', [UserMedicationWebController::class, 'create'])->name('drugs.create');
    Route::post('/user/drugs', [UserMedicationWebController::class, 'store'])->name('drugs.store');
    //Route::get('/user/drugs/destroy', [UserMedicationWebController::class, 'destroy'])->name('drugs.destroy');
    Route::delete('/user/drugs/{rxcui}', [UserMedicationWebController::class, 'destroy'])->name('drugs.destroy');
});


Route::get('/drug-search', [DrugSearchController::class, 'showForm'])->name('drug.search.form');
Route::get('/drug-search/results', [DrugSearchController::class, 'appsearch'])->name('drug.search.results');

require __DIR__.'/auth.php';
