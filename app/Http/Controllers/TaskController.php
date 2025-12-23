<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 一覧表示
    public function index()
    {
        // tasks テーブルから全件取得（作成日の新しい順）
        $tasks = Task::orderByDesc('created_at')->get();

        // 取得したデータをビューに渡す
        return view('tasks.index', compact('tasks'));
    }

    // 新規作成フォーム
    public function create()
    {
        return view('tasks.create');
    }

    // 登録処理
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:1000',
            'due_date' => 'nullable|date',
        ]);

        // タスク作成
        Task::create($validated);

        // 一覧画面にリダイレクト（成功メッセージ付き）
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを登録しました');
    }

    // 編集フォーム
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    // 更新処理
    public function update(Request $request, Task $task)
    {
        // バリデーション
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:1000',
            'due_date' => 'nullable|date',
            'is_done' => 'boolean',
        ]);

        // タスク更新
        $task->update($validated);

        // 一覧画面にリダイレクト（成功メッセージ付き）
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを更新しました');
    }

    // 削除処理
    public function destroy(Task $task)
    {
        $task->delete();

        // 一覧画面にリダイレクト（成功メッセージ付き）
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを削除しました');
    }
}
