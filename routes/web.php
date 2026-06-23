<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/peta', [HomeController::class, 'peta']);
Route::get('/visualisasi', [HomeController::class, 'visualisasi']);
Route::get('/laporan', [HomeController::class, 'laporan']);
