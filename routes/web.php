<?php

use App\Http\Controller\PageController;
use Illuminate\Facade\Route;

Route::get('/', [PageController::class, 'index'])->middleware('first');
Route::post('/csrf', [PageController::class, 'store']);
