# 作成起動まで
```
laravel new laravel-mvc-training
composer update
cp .env.example .env
php artisan key:generate
php artisan config:clear
apt install php8.3-sqlite3
touch database/database.sqlite
php artisan migrate:refresh --seed
npm install
npm run build
php artisan serve
```bash