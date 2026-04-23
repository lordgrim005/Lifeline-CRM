
**Project Name:** LIFELINEMLG Inventory & Rental System

**Platform:** Web Application

**Target Audience:** Admin/Owner LIFELINEMLG

## 1. Product Overview

Sistem Inventory dan Rental Online berbasis web yang dirancang khusus untuk mitra LIFELINEMLG (penyewaan kamera Instax di Malang). Sistem ini bertujuan untuk mendigitalisasi pencatatan stok kamera, manajemen pelanggan, dan alur transaksi penyewaan. Aplikasi ini dibangun dengan prinsip _strict MVC_ dan antarmuka yang responsif untuk memudahkan operasional dari berbagai perangkat.

## 2. Target User

Sistem ini bersifat _internal tool_ dengan sistem otentikasi tunggal (Single Role). Pengguna satu-satunya adalah **Admin (Pemilik Usaha)** yang memiliki akses penuh terhadap seluruh fitur (Create, Read, Update, Delete) di dalam sistem.

## 3. Tech Stack

- **Architecture Strategy:** Server-Side Rendered (Strict MVC)
    
- **Language/Framework:** PHP dengan Laravel + TailwindCSS untuk styling.
    
- **Database:** MySQL
    
- **UI Icons/Components:** FontAwesome / Heroicons.
    

## 4. User Flow

1. **Login:** Admin memasukkan kredensial (Username/Password) di halaman login.
    
2. **Dashboard View:** Admin melihat ringkasan ketersediaan kamera secara _real-time_.
    
3. **Rent Item (Transaksi Baru):** Admin masuk ke menu _Transaction_ -> Klik _New Transaction_ -> Pilih/Tambah Customer -> Pilih Model & Kamera -> Atur durasi sewa -> Sistem menghitung total dan deposit -> Simpan transaksi -> Status kamera otomatis menjadi "Rented".
    
4. **Return Item (Pengembalian):** Admin membuka detail transaksi yang sedang berjalan -> Klik tombol _Return_ -> Sistem mengecek apakah ada keterlambatan dan menghitung denda otomatis -> Transaksi selesai -> Status kamera otomatis kembali menjadi "Available".
    

## 5. Functional Requirements

**A. Authentication**

- Sistem menyediakan halaman Login dan Logout khusus Admin.
    
- Sistem memblokir akses ke halaman _dashboard_ jika Admin belum login (Middleware/Session protected).
    

**B. Dashboard**

- Menampilkan total angka keseluruhan kamera.
    
- Menampilkan rincian status kamera (Jumlah _Available_, _Booked_, _Rented_).
    

**C. Master Data: Camera Models (CRUD)**

- Fitur untuk menambah, melihat, mengedit, dan menghapus (Soft Delete) jenis/model kamera (misal: Instax Mini 11, Instax Square SQ1).
    

**D. Inventory Management (CRUD)**

- Menampilkan daftar inventaris kamera dalam bentuk tabel dengan _Search bar_ dan _Filter_ berdasarkan status.
    
- Form tambah/edit kamera mencakup: Serial Number, Camera Model (diambil dari tabel Camera Models), Initial Status, dan Condition Notes.
    
- Penghapusan data kamera menggunakan metode _Soft Delete_.
    

**E. Customer Management (CRUD)**

- Menampilkan daftar pelanggan dalam bentuk tabel beserta _Search bar_.
    
- Form tambah/edit pelanggan mencakup: Full Name, WhatsApp, Instagram Handle, dan Alamat.
    
- Penghapusan data pelanggan menggunakan metode _Soft Delete_.
    

**F. Transaction Management (CRUD & Logic)**

- Form pembuatan transaksi bisa memilih pelanggan lama atau membuat data pelanggan baru (otomatis tersimpan ke tabel Customer).
    
- Bisa menambahkan lebih dari satu kamera (Multiple items) dalam satu transaksi.
    
- Otomatis menghitung _Line Total_ (berdasarkan _Rate Type_ per jam/hari dikali durasi).
    
- Otomatis menghitung _Grand Total_ (Rental Total + Security Deposit).
    
- Memiliki pelacakan status pembayaran: **Unpaid**, **Partial/DP**, dan **Paid**.
    
- **Logika Pengembalian:** Tombol _Return_ akan menyelesaikan transaksi, membebaskan unit kamera kembali ke status _Available_, dan otomatis memunculkan denda (_Late Fee_) jika waktu aktual pengembalian melebihi _End Time_.
    

## 6. Non-Functional Requirements

- **Architecture:** Wajib mengimplementasikan pemisahan logika yang ketat antara Model (Database), View (UI), dan Controller (Business Logic).
    
- **Responsiveness:** UI harus menyesuaikan dengan baik di perangkat _mobile_, _tablet_, maupun _desktop_.
    
- **UI/UX:** Harus memiliki _Sidebar Navigation_ yang rapi dan fitur _Toggle Dark/Light Mode_ di area bawah sidebar.
    
- **Data Integrity:** Menggunakan _Soft Delete_ agar data transaksi di masa lalu yang terikat dengan pelanggan atau kamera yang sudah dihapus tidak mengalami _error_ / hilang.
    

## 7. Database Schema Suggestion

|**Table Name**|**Description**|**Key Columns**|
|---|---|---|
|**users**|Akun Admin|id, username, password, created_at|
|**camera_models**|Jenis-jenis kamera|id, model_name, deleted_at|
|**cameras**|Data unit fisik kamera|id, model_id (FK), serial_number, status, condition_notes, deleted_at|
|**customers**|Data penyewa|id, full_name, whatsapp, instagram, address, deleted_at|
|**transactions**|Header transaksi sewa|id, customer_id (FK), start_time, end_time, payment_status, deposit, late_fee_amount, is_returned, deleted_at|
|**transaction_items**|Detail kamera yg disewa|id, transaction_id (FK), camera_id (FK), rate, rate_type, line_total|

## 8. UI/UX Guidelines

- **Theme:** Minimalis dan profesional, menonjolkan data agar mudah dibaca.
    
- **Color Palette:** Gunakan warna `#255D5D` `#F2F2F2` `#FFAD33` `#FFFFFF` `#000000` an warna senada lainnya, sesuaikan untuk mode gelapnya.
    
- **Forms:** Input form harus memiliki _label_ yang jelas dan pesan _error_ (validasi) jika data yang dimasukkan kosong atau salah.
    
- **Tables:** Gunakan _pagination_ (halaman) pada tabel jika data sudah melebihi 10 baris agar halaman tidak terlalu panjang.