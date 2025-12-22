<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // tasks テーブルから全件取得（作成日の新しい順）
        $tasks = Task::orderByDesc('created_at')->get();

        // 取得したデータをビューに渡す
        return view('tasks.index', compact('tasks'));
    }
}
