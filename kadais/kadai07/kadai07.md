## 課題07　Model と Migration を使って「タスク」テーブルを作る
```
この課題のゴール

Artisan コマンドを使って、モデル（Model）とマイグレーション（Migration）を自動生成できるようになる

マイグレーションファイルを編集して、自分でテーブル定義（カラム構成）を書けるようになる

php artisan migrate で 実際にDBにテーブルを作成する流れを理解する

app/Models/Task.php に $fillable もしくは $guarded を設定して、今後のCRUD実装で一括代入が使えるようにしておく

この課題では、「タスク管理」のための tasks テーブルを作ることをテーマに進めます。
```

## やることの全体像
```
Artisan コマンドで Task モデル + マイグレーションファイルを作成する

マイグレーションファイルを編集して、tasks テーブルのカラムを定義する

マイグレーションを実行して、実際に DB にテーブルを作る

app/Models/Task.php に $fillable もしくは $guarded を設定する

上記で設定するtasksテーブルには以下カラムを設定してください。

・id
・タスクのタイトル
・タスクの詳細説明文
・タスク完了フラグ
・期日（締め切り日）
・created_at / updated_at


ただし、コマンド実行することでのなにが行われるかの理解と確認は履歴に残してください
また、実際のテーブル確認などはphp artisan tinkerで確認してください。
```

#　回答例 

コンソール確認コマンド
```
php artisan tinker
```

Model と Migration の同時作成
php artisan make:model Task -m

このコマンドで自動生成されるもの

app/Models/Task.php
→ タスク用の Eloquent モデルクラス

database/migrations/xxxx_xx_xx_xxxxxx_create_tasks_table.php
→ tasks テーブルを作るための マイグレーションファイル
（ファイル名の先頭の日時部分は自動で変わります）

2. マイグレーションファイルの編集（tasks テーブルを設計する）

生成されたマイグレーションファイル
database/migrations/xxxx_xx_xx_xxxxxx_create_tasks_table.php
を開いて、up メソッド内の Schema::create('tasks', function (Blueprint $table) { ... }); の中身を編集します。

目標とするテーブル構成

tasks テーブルのカラムは以下の通りにします：

id
自動インクリメント、主キー（Laravel標準の $table->id(); を利用）

title
タスクのタイトル　string 型
必須を想定（DBレベルでは nullable() を付けない）

description
タスクの詳細説明文

text 型
入力がない場合も想定するので nullable

is_done
タスク完了フラグ　boolean 型
デフォルト値は false（未完了）

due_date
期日（締め切り日）

date 型
期日が未定の場合もあるので nullable

timestamps
created_at / updated_at

Laravel 標準の timestamps() でまとめて定義

```
public function up(): void
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();                                // id（自動インクリメント、主キー）
        $table->string('title');                    // title（string, 必須想定）
        $table->text('description')->nullable();    // description（text, nullable）
        $table->boolean('is_done')->default(false); // is_done（boolean, デフォルト false）
        $table->date('due_date')->nullable();       // due_date（date, nullable）
        $table->timestamps();                       // created_at / updated_at
    });
}

```

3. マイグレーションの実行とテーブル確認
3-1. マイグレーションの実行
php artisan migrate


3-2. テーブルが本当にできたか確認する
DBの種類は任意（SQLite, MySQL, PostgreSQL など）です。
以下のいずれかの方法で、tasks テーブルが存在することを確認します。

DBクライアントツール（TablePlus, DBeaver, phpMyAdmin など）でテーブル一覧をチェックする

SQLite の場合

database/database.sqlite を作成しているなら、SQLiteブラウザツールで開いて確認する

php artisan tinker で簡易確認する（例）

```
php artisan tinker
>>> \Schema::hasTable('tasks');
# true が返ってくればOK

```

4. Task モデルの設定（$fillable / $guarded）
4-1. Task モデルを開く

app/Models/Task.php を開きます。
クラスが Illuminate\Database\Eloquent\Model を継承していることを確認します。
```
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // ...
}

```

4-2. 一括代入用の設定をする

今後、コントローラから
```
Task::create($request->all());

```

のように 一括代入（Mass Assignment） を行うために、$fillable または $guarded を設定します。

パターンA：$fillable を使う（どのカラムを一括代入OKにするか指定）

```
class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_done',
        'due_date',
    ];
}

```

パターンB：$guarded を使う（基本すべて許可、特定カラムだけ禁止）

学習用・ローカル環境なら、簡易的に以下もアリです：

```
class Task extends Model
{
    protected $guarded = []; // すべてのカラムを一括代入OKにする
}

```
※実務ではセキュリティ・予期せぬ上書きを避けるために $fillable を使うことが多い、という補足をしておくとよいです。

