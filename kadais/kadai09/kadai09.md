## 課題09　CRUD処理の完成（CREATE・UPDATE・DELETE）

```
この課題のゴール

タスクの新規登録（CREATE）機能を実装できるようになる

タスクの編集（UPDATE）機能を実装できるようになる

タスクの削除（DELETE）機能を実装できるようになる

フォームリクエストでバリデーションを実装できるようになる

RESTfulなルーティング設計を理解する

ここでは、http://127.0.0.1:8000/tasks 画面から実際にタスクの登録・編集・削除を
行えるようにして、完全なCRUDアプリケーションを完成させます。

課題08では「Read（一覧表示）」のみでしたが、課題09でCRUDの全機能を実装します
```

## 現在の実装状況（kadai08で完成している部分）

### 1. Taskモデル
- `app/Models/Task.php` に以下のカラムが $fillable で設定済み
  - title（タスク名）
  - description（説明）
  - is_done（完了フラグ）
  - due_date（期日）

### 2. TaskController の index メソッド（READ）
```php
public function index()
{
    $tasks = Task::orderByDesc('created_at')->get();
    return view('tasks.index', compact('tasks'));
}
```

### 3. ルーティング
```php
Route::get('/tasks', [TaskController::class, 'index']);
```

### 4. ビュー
- `resources/views/tasks/index.blade.php` でタスク一覧をテーブル表示
- ID、タイトル、完了状態、期日、作成日を表示
- 件数表示と「タスクがありません」の分岐あり

## やることの全体像

```
1. タスク新規登録機能（CREATE）
   - 新規登録フォーム画面を作る（create）
   - 登録処理を実装する（store）
   - バリデーションを実装する

2. タスク編集機能（UPDATE）
   - 編集フォーム画面を作る（edit）
   - 更新処理を実装する（update）
   - バリデーションを実装する

3. タスク削除機能（DELETE）
   - 削除処理を実装する（destroy）
   - 削除確認の仕組みを考える

4. 画面遷移の改善
   - 一覧画面に「新規登録」ボタンを追加
   - 一覧画面の各行に「編集」「削除」ボタンを追加
   - 登録・更新・削除後は一覧画面にリダイレクト
   - フラッシュメッセージで操作結果を表示

5. RESTfulルーティングの設定
   - Route::resource を使って7つのアクションを定義
```

## 実装手順

### 1. RESTfulルーティングの設定

`routes/web.php` を以下のように変更：

```php
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('hello');
});

// RESTfulルーティング（7つのアクションが自動定義される）
Route::resource('tasks', TaskController::class);
```

これで以下のルートが自動生成されます：
- GET /tasks → index（一覧）
- GET /tasks/create → create（新規作成フォーム）
- POST /tasks → store（登録処理）
- GET /tasks/{task} → show（詳細表示）※今回は使わない
- GET /tasks/{task}/edit → edit（編集フォーム）
- PUT/PATCH /tasks/{task} → update（更新処理）
- DELETE /tasks/{task} → destroy（削除処理）

### 2. TaskController にメソッドを追加

`app/Http/Controllers/TaskController.php` に以下のメソッドを追加：

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 一覧表示（既存）
    public function index()
    {
        $tasks = Task::orderByDesc('created_at')->get();
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
```

### 3. 一覧画面の更新

`resources/views/tasks/index.blade.php` を以下のように更新：

```html
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
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 3px; display: inline-block; }
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
```

### 4. 新規登録フォームの作成

`resources/views/tasks/create.blade.php` を作成：

```html
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク新規登録</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 20px auto; padding: 0 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="date"], textarea {
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;
        }
        textarea { height: 100px; }
        .btn { padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; text-decoration: none; display: inline-block; }
        .error { color: #dc3545; font-size: 0.9em; }
        .buttons { display: flex; gap: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>タスク新規登録</h1>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">タイトル <span style="color: red;">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required>
            @error('title')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">説明</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
            @error('description')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="due_date">期日</label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}">
            @error('due_date')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="buttons">
            <button type="submit" class="btn btn-primary">登録</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>
</body>
</html>
```

### 5. 編集フォームの作成

`resources/views/tasks/edit.blade.php` を作成：

```html
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
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;
        }
        textarea { height: 100px; }
        input[type="checkbox"] { margin-right: 5px; }
        .btn { padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; text-decoration: none; display: inline-block; }
        .error { color: #dc3545; font-size: 0.9em; }
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
```

## 動作確認

### 1. タスク一覧画面にアクセス
```
http://127.0.0.1:8000/tasks
```

### 2. 新規登録を試す
- 「新規登録」ボタンをクリック
- フォームに入力して「登録」ボタンをクリック
- 一覧画面にリダイレクトされ、「タスクを登録しました」と表示される
- 新しいタスクが一覧に追加されている

### 3. 編集を試す
- 任意のタスクの「編集」ボタンをクリック
- フォームの内容を変更して「更新」ボタンをクリック
- 一覧画面にリダイレクトされ、「タスクを更新しました」と表示される
- タスクの内容が更新されている

### 4. 削除を試す
- 任意のタスクの「削除」ボタンをクリック
- 確認ダイアログで「OK」をクリック
- 一覧画面にリダイレクトされ、「タスクを削除しました」と表示される
- タスクが一覧から削除されている

## 学習のポイント

### 1. RESTfulルーティング
- Route::resource で7つのアクションが自動定義される
- 命名規則に従うことで route('tasks.index') などの名前付きルートが使える

### 2. CSRF対策
- フォームに @csrf を必ず含める
- Laravelが自動的にCSRFトークンを検証する

### 3. HTTPメソッドの使い分け
- POST → 新規作成（store）
- PUT/PATCH → 更新（update）
- DELETE → 削除（destroy）
- HTMLフォームでは @method('PUT') や @method('DELETE') を使う

### 4. バリデーション
- $request->validate() でルールを定義
- エラーメッセージは自動で $errors に格納される
- @error ディレクティブでエラー表示

### 5. フラッシュメッセージ
- with('success', 'メッセージ') でセッションに一時保存
- session('success') で取り出して表示

### 6. ルートモデルバインディング
- コントローラのメソッド引数で Task $task と型指定
- Laravelが自動的にIDからモデルを取得してくれる

## 発展課題

1. バリデーションルールをFormRequestクラスに分離する
2. 完了/未完了を一覧画面からワンクリックで切り替えられるようにする
3. タスクの並び順を変更できるようにする（期日順、タイトル順など）
4. ページネーションを実装する（件数が多い場合）
5. 検索機能を追加する（タイトルで検索など）
