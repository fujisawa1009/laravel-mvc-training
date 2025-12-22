<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('hello');
});

Route::get('/tasks', [TaskController::class, 'index']);
