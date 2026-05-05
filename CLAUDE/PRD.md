
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
    
- **UI Icons/Components:** lucide icons.
    

## 4. User Flow

1. **Login:** Admin memasukkan kredensial (Username/Password) di halaman login.
    
2. **Dashboard View:** Admin melihat ringkasan ketersediaan kamera secara _real-time_.
    
3. **Rent Item (Transaksi Baru):** Admin masuk ke menu _Transaction_ -> Klik _New Transaction_ -> Pilih/Tambah Customer -> Pilih Model Kamera & **Pilih Paket (contoh: Bronze, Gold)** -> Pilih Unit Fisik Kamera -> Atur durasi sewa (dalam hitungan hari) -> Sistem menghitung Grand Total (Harga Paket x Durasi) -> Admin mencatat pembayaran penuh -> Simpan transaksi -> Status unit kamera otomatis menjadi "Rented".
    
4. **Return Item (Pengembalian):** Admin membuka detail transaksi yang sedang berjalan -> Klik tombol _Return_ ->  Transaksi selesai -> Status kamera otomatis kembali menjadi "Available".
    

## 5. Functional Requirements

**A. Authentication**

- Sistem menyediakan halaman Login dan Logout khusus Admin.
    
- Sistem memblokir akses ke halaman _dashboard_ jika Admin belum login (Middleware/Session protected).
    

**B. Dashboard**

- Menampilkan total angka keseluruhan kamera.
    
- Menampilkan rincian status kamera (Jumlah _Available_, _Rented_, dan _Maintenance_). _(Menghapus status Booked)_   

**C. Master Data: Camera Models (CRUD)**

- Fitur untuk menambah, melihat, mengedit, dan menghapus (Soft Delete) jenis/model kamera (misal: Instax Mini 11, Instax Square SQ1).
    
- **Package Management:** Di dalam halaman detail Camera Model, Admin dapat menambahkan opsi **Paket Sewa (Packages)** secara dinamis. Setiap paket memuat: Nama Paket (contoh: Bronze/Gold), Detail Isi (contoh: Camera Only / Camera + White Refill), dan Harga Sewa per Hari.

**D. Inventory Management (CRUD)**

- Menampilkan daftar inventaris kamera dalam bentuk tabel dengan _Search bar_ dan _Filter_ berdasarkan status.
    
- Form tambah/edit kamera mencakup: Serial Number, Camera Model (diambil dari tabel Camera Models), dan Initial Status.
    
- Penghapusan data kamera menggunakan metode _Soft Delete_.
    

**E. Customer Management (CRUD)**

- Menampilkan daftar pelanggan dalam bentuk tabel beserta _Search bar_.
    
- Form tambah/edit pelanggan mencakup: Full Name, WhatsApp, Instagram Handle, dan Alamat.
    
- Penghapusan data pelanggan menggunakan metode _Soft Delete_.
    

**F. Transaction Management (CRUD & Logic)**

- Form pembuatan transaksi bisa memilih pelanggan lama atau membuat data pelanggan baru.
    
- Bisa menambahkan lebih dari satu item (Multiple items/keranjang) dalam satu transaksi. Setiap item harus menentukan Unit Kamera dan Paket yang dipilih.
    
- Otomatis menghitung _Subtotal_ berdasarkan **Harga Paket per Hari dikali Durasi (Hari)**.
    
- Memiliki pelacakan status pembayaran yang disederhanakan: **Unpaid** (Belum Lunas) dan **Paid** (Lunas). Tidak ada sistem DP/Partial.
    
- **Logika Pengembalian:** Tombol _Return_ akan menyelesaikan transaksi, membebaskan unit kamera kembali ke status _Available.
    

## 6. Non-Functional Requirements

- **Architecture:** Wajib mengimplementasikan pemisahan logika yang ketat antara Model (Database), View (UI), dan Controller (Business Logic).
    
- **Responsiveness:** UI harus menyesuaikan dengan baik di perangkat _mobile_, _tablet_, maupun _desktop_.
    
- **UI/UX:** Harus memiliki _Sidebar Navigation_ yang rapi dan fitur _Toggle Dark/Light Mode_ di area bawah sidebar.
    
- **Data Integrity:** Menggunakan _Soft Delete_ agar data transaksi di masa lalu yang terikat dengan pelanggan atau kamera yang sudah dihapus tidak mengalami _error_ / hilang.
    

## 7. Database Schema Suggestion

| **Table Name**        | **Description**                  | **Key Columns**                                                                                          |
| --------------------- | -------------------------------- | -------------------------------------------------------------------------------------------------------- |
| **users**             | Akun Admin                       | id, username, password, created_at                                                                       |
| **camera_models**     | Jenis/merek kamera               | id, brand, name, description, deleted_at                                                                 |
| **camera_packages**   | **[BARU]** Paket sewa tiap model | id, camera_model_id (FK), package_name, includes, daily_price, deleted_at                                |
| **cameras**           | Data unit fisik kamera           | id, camera_model_id (FK), serial_number, status (available/rented/maintenance), deleted_at               |
| **customers**         | Data penyewa                     | id, name, phone, email, Instagram, address, deleted_at                                                   |
| **transactions**      | Header transaksi sewa            | id, customer_id (FK), start_date, end_date, grand_total, status (active/completed), late_fee, deleted_at |
| **transaction_items** | Detail kamera & paket disewa     | id, transaction_id (FK), camera_id (FK), camera_package_id (FK), price_per_day, subtotal                 |

## 8. UI/UX Design Specifications

- **Visual Style:** Mengusung tema _Modern High-End Dashboard_ dengan sudut melengkung (_rounded corners_) yang halus dan bayangan yang sangat lembut (_soft drop shadows_) untuk menciptakan efek kedalaman.
    
- **Light Mode:** Menggunakan latar belakang putih bersih dengan kartu-kartu yang memiliki _border_ sangat tipis. Aksen menggunakan gradien biru ke ungu untuk elemen penting seperti saldo atau statistik utama.
    
- **Dark Mode:** Menggunakan latar belakang gelap dengan nuansa biru/ungu pekat (bukan hitam pekat). Elemen aktif atau grafik menggunakan warna ungu neon yang kontras.
    
- **Navigation:** **Sidebar:** Kita akan isi dengan menu: _Dashboard, Camera Models, Inventory, Customers, dan Transactions_. dan tombol _toggle_ mode gelap/terang di bagian bawah. lalu bawahnya ada menu logout.

