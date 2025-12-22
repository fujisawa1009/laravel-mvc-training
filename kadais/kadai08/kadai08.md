## 課題08　一覧表示（READ）で「M と V」をつなぐ
```
この課題のゴール

コントローラ（Controller）を新規作成できるようになる

コントローラの中で モデル（Task）からデータを取得してビューに渡す流れ を理解する

ルーティングを設定して、URL → コントローラ → ビュー の一連の流れを体験する

Blade を使って、タスク一覧をテーブルで表示する画面（READ） を作れるようになる

ここでは、tasks テーブルの中身をブラウザで一覧表示するページを作ります。

タスクのCRUDの「Read（一覧表示）」部分を完成させる
```

## やることの全体像
```
TaskController を作成し、index メソッドを定義する

index メソッドで Task モデルから全件取得し、ビューに渡す

/tasks への GET アクセスで TaskController@index が呼ばれるようにルートを設定する

resources/views/tasks/index.blade.php を作成し、タスク一覧をテーブルで表示する

ページの上部に「タイトル」と「レコード件数（n件）」を表示する

（追加）0件のときは「タスクがありません」と表示する

```

#　回答例 

routes/web.php に追記する
```
use App\Http\Controllers\TaskController;

Route::get('/tasks', [TaskController::class, 'index']);
```
1. TaskController の作成
php artisan make:controller TaskController

生成物：
app/Http/Controllers/TaskController.php

3) indexで「取得 → ビューへ渡す」をコードで明確にする

「全件取得」なら Task::all() でもOKだけど、実務寄りにするなら orderBy を入れると “一覧らしさ” が出ます。
（期日順や最新順のどちらでも）
```
use App\Models\Task;

public function index()
{
    $tasks = Task::orderByDesc('created_at')->get();
    return view('tasks.index', compact('tasks'));
}

```

4) Bladeビューの「出力」「件数表示」の要件を具体化したい

要件として「タイトル＋n件」って書いてあるのは良いです。
ただ、どの変数で件数を出すかを例示すると迷いが減ります。
$tasks->count() を使う（Collectionの場合）
{{ }} でエスケープ出力する（XSS対策の基本）
<h1>タスク一覧</h1>
<p>{{ $tasks->count() }}件</p>

```

```