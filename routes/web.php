<?php

use App\Http\Controllers\userController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [userController::class, 'index'])->name('user.index');
Route::post('users-import', [userController::class, 'import'])->name('user.import');
