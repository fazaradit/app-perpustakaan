# App Perpustakaan

## Tujuan Aplikasi
Aplikasi Perpustakaan ini dibangun untuk mempermudah manajemen peminjaman, pengembalian, dan pendataan buku di perpustakaan secara digital.

## Cara Menjalankan Project Secara Lokal

1. Clone repository ini:
   ```bash
   git clone https://github.com/fazaradit/app-perpustakaan.git
   cd app-perpustakaan
   ```
2. Install dependensi PHP menggunakan Composer:
   ```bash
   composer install
   ```
3. Salin file konfigurasi lingkungan:
   ```bash
   cp .env.example .env
   ```
4. Generate application key Laravel:
   ```bash
   php artisan key:generate
   ```
5. Jalankan migration:
   ```bash
   php artisan migrate
   ```
6. Jalankan development server:
   ```bash
   php artisan serve
   ```
7. Buka browser dan akses aplikasi melalui `http://localhost:8000`.



## Perbedaan Model, View, dan Controller (MVC):

Model adalah komponen yang bertanggung jawab untuk berinteraksi dengan database, mengelola data, dan menerapkan aturan bisnis aplikasi. View adalah antarmuka visual (UI) yang bertugas mempresentasikan atau menampilkan data tersebut kepada pengguna akhir. Sedangkan Controller bertindak sebagai penghubung yang menerima permintaan (request) dari pengguna, mengambil atau menyimpan data melalui Model, dan meneruskan hasilnya untuk ditampilkan oleh View. 
