<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク一覧</title>
    <style>
        body { font-family: sans-serif; max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #f5f5f5; text-align: left; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 3px; display: inline-block; border: none; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 3px; margin-bottom: 20px; }
        .actions { display: flex; gap: 5px; }
    </style>
</head>
<body>
    <h1>タスク一覧</h1>
    <p>{{ $tasks->count() }}件</p>

    <!-- 成功メッセージ -->
    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <!-- 新規登録ボタン -->
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">新規登録</a>

    @if ($tasks->isEmpty())
        <p>タスクがありません</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>タイトル</th>
                    <th>完了</th>
                    <th>期日</th>
                    <th>作成日</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->title }}</td>
                        <td>{{ $task->is_done ? '完了' : '未完了' }}</td>
                        <td>{{ $task->due_date ?? '-' }}</td>
                        <td>{{ $task->created_at }}</td>
                        <td>
                            <div class="actions">
                                <!-- 編集ボタン -->
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">編集</a>

                                <!-- 削除ボタン -->
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                      onsubmit="return confirm('本当に削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">削除</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
