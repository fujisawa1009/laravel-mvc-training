<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク一覧</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #f5f5f5; text-align: left; }
    </style>
</head>
<body>
    <h1>タスク一覧</h1>
    <p>{{ $tasks->count() }}件</p>

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
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
