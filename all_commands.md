20250707_all_commandsをGPTにて、カテゴリ分け済み(都度追加)

## 20250927追記mac Windows
# Macbook
MacbookAir/メモリ：8G/チップM1
ストレージ:20250927現在36.83→XXX/245.11GB
```
スクリーンショット（全画面）	Mac: Shift + Command + 3	/ Win: PrintScreen
スクリーンショット（選択範囲）	Mac: Shift + Command + 4	/Win: Win + Shift + S
```
## vscode
Compare Folders：プロジェクト間のdiff比較表示

# コマンド整理一覧（Laravel + Docker + Horizon 開発環境用）

# 【重要】再度macbookでの環境構築時の権限エラー対応(Readme移行予定)
```
■ホスト側
# laravelディレクトリ以下を無条件で誰でも読書・実行できるように
sudo chmod -R 777 ./laravel

# 万が一のため所有者を自分のユーザーに揃えるのもおすすめ
sudo chown -R $(id -u):$(id -g) ./laravel

■コンテナ側
docker exec -it -u root bp_app bash
# コンテナ内rootで下記を実行

# laravelディレクトリ丸ごと誰でも使えるように
chmod -R 777 /var/www/laravel

# オーナーはwww-dataにしても良いが指定なければrootでもOK
chown -R www-data:www-data /var/www/laravel

■DB作成
docker exec -it bp_db bash
mysql -u root -p
CREATE DATABASE laravel;
show databases;
```
---
## claude関連
```bash
# ログイン
claude "/login"

# スキップコマンド
claude --dangerously-skip-permissions

```

## 🔍 ファイル・フォルダ関連
```bash
# 日付ファイル作成
touch "$(date +%Y%m%d)"

# 文字検索/ファイル検索
grep -r "Perplexity" ./
find ./ -type d -name "Perplexity"
find ./  -name "*.vue"
find ./  -name "*.tsx"

# フォルダ容量順に並び替え
du -sh ./* | sort -hr

# .git フォルダ検索
find . -type d -name ".git"
````

---

## 🛠 Laravel Artisan コマンド

### 🔄 キャッシュクリア関連

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan horizon:clear
php artisan optimize:clear
composer dump-autoload

# Laravel 10以降は optimize:clear が便利
php artisan optimize:clear
```

### 📅 スケジューラ関連

```bash
php artisan schedule:work
php artisan schedule:list
```

### 🔃 ルーティング確認

```bash
php artisan route:list
php artisan route:list | grep admin/horizon/api
php artisan route:list | grep 'roles'
```

### 🧪 tinker 実行・ジョブ起動

```bash
php artisan tinker
dispatch(new \App\Jobs\SampleLogJob());
use App\Jobs\SampleLogJob;
dispatch(new SampleLogJob());

# パーミッションスキャン
app(\App\Services\PermissionScannerService::class)->scanAndRegisterControllerActions();
```

### 🌱 マイグレーション・シーディング

```bash
php artisan migrate
php artisan migrate:refresh --seed
php artisan db:seed --class=AclSeeder
php artisan db:seed --class=DatabaseSeeder
```

### 作成コマンド

```bash
php artisan make:model JobResult
```

---

## 🔁 Laravel Queue / Horizon 関連

### Horizon 起動・監視・管理

```bash
php artisan horizon
php artisan horizon:status
php artisan horizon:supervisors
php artisan horizon:list
php artisan horizon:terminate
php artisan horizon:pause
php artisan horizon:continue
php artisan horizon:pause-supervisor supervisor-heavy
php artisan horizon:continue-supervisor supervisor-heavy
```

### キュー処理確認

```bash
php artisan queue:listen redis
php artisan queue:monitor
php artisan queue:flush
php artisan queue:retry all
```

### 情報出力(--no-data laravel箇所はDB名)
```bash
# DB情報出力
docker exec -i bp_db sh -lc \
  'mysqldump -uroot -proot --host=127.0.0.1 --protocol=TCP --no-data laravel' \
  > schema.sql
```

### テスト実行
```bash
php artisan test --filter Organization
```

---

## 🐳 Docker 関連

```bash
docker compose up -d --build
docker compose build --no-cache
docker exec -it bp_app bash
docker exec -it -w /var/www/laravel bp_app composer update
docker exec -it -w /var/www/laravel bp_app cp .env.example .env
docker exec -it -w /var/www/laravel bp_app php artisan key:generate
docker exec -it -w /var/www/laravel bp_app php artisan migrate
docker exec -it -w /var/www/laravel bp_app npm install
docker exec -it -w /var/www/laravel bp_app npm run build
chown -R www-data:www-data laravel
```

---

## 🔑 Git / SSH 関連

### 初期リポジトリ設定

```bash
echo "# boi0702-env" >> README.md
git init
git add README.md
git commit -m "first commit"
git branch -M main
git remote add origin git@github.com:fujisawa1009/boi0702-env.git
git push -u origin main
# 20250908追記　githubアカウント切替
git config --local user.name "fujisawa1009"   # もしくは "yffty09"
git config --local user.email "mt.fuji1009@gmail.com"
## リモートURLを別名ホストへ差し替え
git remote -v
git remote set-url origin git@github-yuta:fujisawa1009/stock_boilerplate-env.git
git remote set-url origin git@github-yffty:ftgrp/boilerplate.git
git remote -v
## 追加対応：権限を厳しく（OpenSSHは緩い権限だと設定を無視します）
chmod 700 ~/.ssh
chmod 600 ~/.ssh/config ~/.ssh/id_rsa_yuta
##アクセス権テスト
git ls-remote -h origin
## 設定確認
ssh -T github-yffty     # => Hi yffty09!
ssh -T github-yuta      # => Hi fujisawa1009!
```

### Stash & Rebase

```bash
git stash -u
git stash save "permission確認退避"
git stash list
git stash apply stash@{0}
git rebase -i HEAD~3
git reset --hard 1f2407b3
```

### Git pulus one

```bash
# 特定のファイルの変更履歴を確認するコマンド
git log -p ファイル名
# コミット履歴のグラフを表示するコマンド
git log --graph --oneline
# 最新のコミットメッセージを修正するコマンド
git commit --amend -m "新しいコミットメッセージ"
# 最新のコミットをリセットするコマンド
git reset --soft HEAD~1
# 特定のファイルをgit管理リポジトリから削除するコマンド
git rm ファイル名
# ステージングエリアからファイルを除外するコマンド
git reset HEAD ファイル名
# `.gitignore` ファイルを作成し、特定のファイルを無視する手順
echo "無視するファイル名" >> .gitignore
git add .gitignore
git commit -m "Add .gitignore"
# リベース中に競合が発生した場合の解決手順
# 手動で競合を解決後、以下を実行：
git rebase --continue
# 特定のコミットを別のブランチに適用するコマンド
git cherry-pick <コミットハッシュ>
```

### SSH 設定 # ssh-agentを起動して秘密鍵をssh-agentに登録
```bash
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_327XXX_rsa
ssh-add ~/.ssh/id_rsa_327XXX
ssh-add ~/.ssh/id_ed25XXX.pub
sudo chown $USER /var/
```

### 所有者と所有グループ変更、書き込み権限追加
```bash
sudo chmod +w *
sudo chown $USER:$USER /var/
sudo chmod u+w /var/
sudo chmod g+w /var/

# ユーザ追加切替
adduser fujisawa1009
usermod -aG sudo fujisawa1009
su - fujisawa1009

```


---

## 🧹 Prettier（コード整形）

### インストール

```bash
npm install --save-dev prettier prettier-plugin-organize-imports prettier-plugin-tailwindcss
npm install --save-dev @prettier/plugin-php prettier-plugin-blade
```

### 実行例

```bash
docker exec -it -w /var/www/laravel bp_app npm run lint
docker exec -it -w /var/www/laravel bp_app npx prettier --write .
docker exec -it -w /var/www/laravel bp_app npx prettier --check laravel/resources/js/layouts/
docker exec -it -w /var/www/laravel bp_app npx prettier --write laravel/resources/js/layouts/
```

### `package.json` スクリプト例

```json
"scripts": {
  "format": "prettier --write resources/",
  "format:check": "prettier --check resources/",
  "lint": "eslint . --fix"
}
```

### フォーマットリセット（元に戻す）

```bash
git restore --source <コミットハッシュ> <ファイルパス>
```

---

## 📊 DB操作・確認

```bash
# DBテーブル確認
docker exec -it bp_db mysql -uroot -proot
use laravel;
DESCRIBE users;

# tinker 内で DB 操作
\User::all();
User::find(1);
```

---

## 🔐 権限・パーミッション関連tinkerにまとめる

```php
Permission::create(['name' => 'RoleController@index']);
Role::create(['name' => 'admin']);
$user = User::where('email', 'fujisawa_yuta@ftgroup.co.jp')->first();
$user->assignRole('admin');
$user->givePermissionTo('roles.index');
```

```bash
php artisan permission:sync
```

---

## 🖥 Supervisor コマンド

```bash
supervisorctl status
supervisorctl restart all
supervisorctl stop horizon
supervisorctl remove horizon
supervisorctl reread
supervisorctl update
docker compose exec app supervisorctl status
```

---

## 📝 その他メモ

### 権限変更

```bash
sudo chown 327312:327312 /var/db-backups/entre
sudo chmod u+w /var/db-backups/entre
sudo chmod g+w /var/db-backups/entre
sudo chown $USER /var/develop/dockers/ruby/TL/*
```

### Horizon メモ補足

* Horizon は Redis 専用
* ジョブ実行後のフック（Horizon::afterJobProcessed）で DB ログ保存が可能

---

## 🔚 結論メモ

* Horizon UI の管理・監視においては `supervisor` や `queue` 系 Artisan コマンドと組み合わせる
* Laravel キャッシュや環境構築系コマンドは定期的に整理しておくと運用が楽

## vscodeコマンドメモ
* エディタ指定
select-editor
* EDITORの確認
echo $EDITOR
* VISUALの確認
echo $VISUAL
* 両方の環境変数を確認
env | grep -E "EDITOR|VISUAL"


# tinkerお宝 20250805

転記するお宝
```bash
■【重要】tinkerで発行されているSQLを確認
DB::enableQueryLog();       // クエリログを有効化
DB::getQueryLog()　　//SQL発行されるコマンド実行後に実行
```

■【重要】アクセサの確認方法
```bash
php artisan tinker
>>> \App\Models\Organization::first()->indented_name
```

■名前空間を除いたクラス名のみを取得
```bash
> class_basename(User::class)
= "User"

> class_basename(\App\Models\User::class)
= "User"
■【重要】そのクラスで利用可能なメソッド返す
> get_class_methods(User::class)
```

■複数のモデルクラス名を一度に取得
```bash
collect(['User', 'Post', 'Comment'])->map(fn($model) => "App\\Models\\{$model}")->toArray()
= [
    "App\Models\User",
    "App\Models\Post",
    "App\Models\Comment",
  ]
```

■【再重要】Reflection を使用した詳細情報取得
```bash
$reflection = new ReflectionClass(User::class);
> $reflection->getName();　// クラス名
= "App\Models\User"  
$reflection->getNamespaceName();  // 名前空間
= "App\Models"
$reflection->getShortName();  // 短縮名
= "User"
```

■実務用
```bash
$reflection = new ReflectionClass(User::class);
echo $reflection->getName();  // 結果を表示
$namespace = $reflection->getNamespaceName();  // 変数に保存

# OPcacheクリア　tinkerでキャシュエラーのとき
php artisan optimize:clear
```

■以下はテスト時
単体
```bash
$org = Organization::find('01985de1-9dd8-731e-bd81-9441e9ad79c5');
> var_dump(method_exists($org, 'getNodeProperty'));
bool(true)
= null
> $org->getNodeProperty('nodeRelatedModel');
= "App\Models\User"
> $org->getNodeProperty('nodeRelationshipColumn');
= "organization_id"
 ```

■ReflectionClassを使用すると、以下のようなことが可能
```bash
// クラス名を取得
$className = $reflection->getName();

// 定義されている定数一覧を取得
$constants = $reflection->getConstants();

// メソッドの一覧を取得
$methods = $reflection->getMethods();

// プロパティの一覧を取得  
$properties = $reflection->getProperties();

```

■最終案の動作
```bash
php artisan tinker
$org = App\Models\Organization::first(); 
$org = Organization::find('01985de1-9dd8-731e-bd81-9441e9ad79c5');

■デフォルト時(初期値なし、モデル指定無)
$org->getdisplayColumn();
$org->getrelatedModel();
$org->getrelationshipName();
$org->relationshipColumn();
$org->getIndentPrefix();

■モデルカスタマイズ時(、モデル指定あり)
$org->getDisplayColumn();
= "name"
$org->getRelationName();
= "users"
$org->getForeignKey();
= "organization_id"
$org->getRelatedModelClass();
= "App\Models\User"
$org->getIndentPrefix();
= "--"
```

✅ 結論
「リレーションのメソッド名（例: users）は逆引きできない」が、
「定義されているリレーションの一覧と、各リレーションが指すモデルクラス名はプログラム的に取得可能なためリファクタ
✅ つまりこういうこと
$org にどんなリレーション（=メソッド名）が定義されていて、
それぞれがどのモデルクラスを返すか？は調べられます。
Laravelの標準機能ではないが、リフレクションを使えば可能です。
✅ あなたの目的に対する最小コード
$org = Organization::find('01985dda-d3db-7242-8d9d-712cd5fc108c');
get_class($org->users()->getRelated());
= "App\Models\User"
＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
# tinkerでインスタンス操作
```bash
DB::enableQueryLog();       // クエリログを有効化
DB::getQueryLog()　　//SQL発行されるコマンド実行後に実行
$org = Organization::find('01985ddb-2af1-7102-9f76-4bc3d274c1c6');
組織に属するユーザー一覧
$org->users; //社員A,社員B,社員Cの情報
組織に属するユーザー数をカウント
$org->users()->count();
ユーザー名のリストを取得
$org->users->pluck('name');
リレーションの詳細を確認　// 発行されるSQLを確認できる
$org->users()->getQuery()->toSql();
ユーザーと組織の情報を同時取得
$org->load('users');
親組織を取得
$org->parent;
子組織を取得
$org->children;
先祖組織（上位階層）一覧
$org->ancestors;
子孫組織（下位階層）一覧
$org->descendants;
自分自身を含む子孫組織
$org->descendantsAndSelf();
インデント付きの組織名表示
$org->getIndentedNameAttribute();
組織階層にユーザーが存在するかチェック
$org->hasRelatedDataInSubtree();
ユーザーを持つ子孫組織の詳細
$org->getDescendantsWithRelatedData();
モデルの配列変換
$org->toArray();
JSON形式で出力
echo $org->toJson();
// 関連モデルのインスタンスを取得
$relatedModel = $org->users()->getRelated();
// クラス名を取得
$className = get_class($org->users()->getRelated());
// 結果: "App\Models\User"
// 基底クラス名のみ取得
$baseName = class_basename($org->users()->getRelated());
// 結果: "User"
```

Railsでの以下操作ができない
```bash
# すべてのリレーション情報を取得
user.class.reflections
# 関連モデルクラス名を取得
user.class.reflections.map { |name, reflection| [name, reflection.class_name] }.to_h
# すべてのアソシエーションを取得
user.class.reflect_on_all_associations
# has_manyリレーションのみ
user.class.reflect_on_all_associations(:has_many)
# belongs_toリレーションのみ
user.class.reflect_on_all_associations(:belongs_to)
# has_oneリレーションのみ
user.class.reflect_on_all_associations(:has_one)
```

＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
FIX)Laravelでリレーション名を指定せずにリレーション情報を取得できないためそれぞれ決めてモデル側で設定する、していない場合デフォルト値を使用。
詳しくは以下
http://10.65.1.185:3000/issues/771
公式参考
https://laravel.com/docs/12.x/eloquent-relationships

リフレクションを用いた動的メソッド呼び出しは廃止

佐藤さんと話して廃止
```
ReflectionClass使わないようにしたのは正解かもしれません。
モデルを対象とした場合、継承関係やTrait（Traitの継承元）も全て読み込むみたいで、
更に処理コスト高くなるらしい...
```
PHPリフレクションを用いた動的メソッド呼び出し
https://qiita.com/ta_Chuck/items/8140695d5fceed990428
リフレクションとは？
リフレクションは、クラスやメソッド、プロパティについてのメタ情報（プログラムの構造、特性、および挙動）を取得し、それを操作するPHPの機能

【重要】【Laravel】引数でタイプヒントしただけでインスタンスがもらえるのはなぜ？Laravelの魔法を解明してみる。
https://qiita.com/minato-naka/items/a4531797af611688db97
リフレクションとは
PHPには「リフレクション」という機能があり、サービスコンテナのmake()メソッドではこの「リフレクション」を活用して依存解決を行っている。
依存解決の仕組みを知るうえで「リフレクション」というものが何なのかを知っておく必要がある。
