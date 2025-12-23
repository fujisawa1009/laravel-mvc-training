<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク編集</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 20px auto; padding: 0 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="date"], textarea {
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; box-sizing: border-box;
        }
        textarea { height: 100px; }
        input[type="checkbox"] { margin-right: 5px; }
        .btn { padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .error { color: #dc3545; font-size: 0.9em; margin-top: 5px; }
        .buttons { display: flex; gap: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>タスク編集</h1>

    <form action="{{ route('tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">タイトル <span style="color: red;">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" required>
            @error('title')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">説明</label>
            <textarea name="description" id="description">{{ old('description', $task->description) }}</textarea>
            @error('description')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="due_date">期日</label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $task->due_date) }}">
            @error('due_date')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="is_done" value="1"
                    {{ old('is_done', $task->is_done) ? 'checked' : '' }}>
                完了
            </label>
            @error('is_done')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="buttons">
            <button type="submit" class="btn btn-primary">更新</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>
</body>
</html>
