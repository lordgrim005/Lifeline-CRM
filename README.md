1. **Clone Repository**
git clone [https://github.com/alzahraramadhani/lifelinemlg-crm.git](https://github.com/alzahraramadhani/lifelinemlg-crm.git)
cd lifelinemlg-crm

# Install package PHP
composer install

# Install package Frontend
npm install
npm run build

# setup Environment File
cp .env.example .env
Buka file .env dan sesuaikan konfigurasi database DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lifeline_db
DB_USERNAME=root
DB_PASSWORD=.

# Generate Application Key
php artisan key:generate

# Migrate Database
Pastikan database lifeline_db sudah dibuat di MySQL, lalu jalankan:
php artisan migrate

# Jalankan Project
php artisan serve
Buka http://127.0.0.1:8000 di browser kamu.   