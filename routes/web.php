<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('hello');
});

// RESTfulルーティング（7つのアクションが自動定義される）
Route::resource('tasks', TaskController::class);
