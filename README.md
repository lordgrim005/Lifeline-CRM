# 🚀 Cara Menjalankan Project Lifeline CRM

Ikuti langkah-langkah berikut untuk menjalankan project di lokal:

## 1. Clone Repository

```bash
git clone https://github.com/alzahraramadhani/lifelinemlg-crm.git
cd lifelinemlg-crm
```

## 2. Install Dependency Backend (PHP)

```bash
composer install
```

## 3. Install Dependency Frontend

```bash
npm install
npm run build
```

## 4. Setup File Environment

Copy file `.env`:

```bash
cp .env.example .env
```

Lalu buka file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lifeline_db
DB_USERNAME=root
DB_PASSWORD=
```

## 5. Generate Application Key

```bash
php artisan key:generate
```

## 6. Migrasi Database

Pastikan kamu sudah membuat database dengan nama `lifeline_db` di MySQL, lalu jalankan:

```bash
php artisan migrate
```

## 7. Jalankan Project

```bash
php artisan serve
```

Buka di browser:

```
http://127.0.0.1:8000
```
