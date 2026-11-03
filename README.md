# App Perpustakaan

## Tujuan Aplikasi

Aplikasi Perpustakaan ini dibangun untuk mempermudah manajemen peminjaman, pengembalian, dan pendataan buku di perpustakaan secara digital.

Project ini merupakan hasil praktikum Workshop Pemrograman Framework Pertemuan 1 sampai 10 dengan Laravel 12.

## Fitur

- CRUD buku, kategori, anggota, dan peminjaman
- Autentikasi login/logout berbasis session dan middleware role admin/petugas
- Halaman profil dan ganti password
- REST API dengan response API Resource
- Dashboard dan Laporan Peminjaman yang mengonsumsi API internal lewat Laravel HTTP Client
- Seeder dan Factory untuk data dummy bergaya Indonesia

## Kebutuhan

- PHP 8.2 atau lebih baru
- Composer
- MySQL (XAMPP atau Laragon)
- Git

## Cara Clone dan Install

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

## Konfigurasi `.env`

Buka file `.env` lalu sesuaikan variabel berikut.

| Variabel | Nilai | Keterangan |
|---|---|---|
| `APP_URL` | `http://localhost` | Alamat aplikasi |
| `APP_FAKER_LOCALE` | `id_ID` | Membuat data Factory bergaya Indonesia |
| `DB_CONNECTION` | `mysql` | Driver database |
| `DB_DATABASE` | `db_perpustakaan` | Nama database, buat dulu di phpMyAdmin |
| `DB_USERNAME` | `root` | User database |
| `DB_PASSWORD` | kosong | Password database |
| `INTERNAL_API_URL` | `http://127.0.0.1:8011` | Base URL server kedua untuk panggilan API internal |

### Tentang `INTERNAL_API_URL`

Variabel ini wajib ada dan portnya harus berbeda dari server utama. Nilainya dibaca di `config/services.php` sebagai `services.internal_api.base_url`, lalu dipakai `DashboardController` dan `LoanController@report` untuk memanggil `GET /api/stats` dan `GET /api/loans`.

Kalau variabel ini tidak ada, aplikasi memakai nilai default `http://127.0.0.1:8011`.

## Migrate dan Seed Database

Pastikan database `db_perpustakaan` sudah dibuat, lalu jalankan:

```bash
php artisan migrate:fresh --seed
```

Perintah ini menjalankan seluruh migration dari nol lalu mengisi data dummy lewat `DatabaseSeeder`. Urutan Seeder mengikuti ketergantungan foreign key:

1. `UserSeeder` - 3 akun user (1 admin, 2 petugas)
2. `CategorySeeder` - 5 kategori tetap
3. `BookSeeder` - 20 buku lewat `BookFactory`
4. `MemberSeeder` - 15 anggota lewat `MemberFactory`
5. `LoanSeeder` - 10 transaksi peminjaman beserta item bukunya

Kalau hanya ingin mengisi ulang data tanpa menghapus tabel, jalankan `php artisan db:seed`.

## Menjalankan Dua Server

Aplikasi ini butuh **dua** proses server yang berjalan bersamaan.

Terminal 1, server utama untuk trafik browser:

```bash
php artisan serve --port=8000
```

Terminal 2, server khusus API internal:

```bash
php artisan serve --port=8011
```

| Server | Port | Fungsi |
|---|---|---|
| Utama | 8000 | Melayani halaman web yang dibuka browser |
| API internal | 8011 | Menerima panggilan `Http::get()` dari Dashboard dan Laporan |

### Kenapa Harus Dua Server

`php artisan serve` memakai PHP built-in web server yang memproses satu request dalam satu waktu. Halaman Dashboard dan Laporan Peminjaman memanggil API aplikasi sendiri. Kalau hanya ada satu server, server sedang sibuk memproses request halaman sambil menunggu balasan dari request API yang tidak akan pernah diterima. Halaman akan menggantung sampai timeout.

Dengan port kedua, request API ditangani proses PHP lain yang membaca database yang sama, sehingga tidak saling mengunci.

Kalau hanya satu server yang jalan, halaman Dashboard akan menampilkan angka 0 dan halaman Laporan akan tampil kosong. Aplikasi tidak error, tetapi datanya tidak muncul.

Di server produksi (Nginx dengan PHP-FPM, atau Laravel Octane) masalah ini tidak muncul karena banyak request bisa diproses bersamaan.

## Akun Default

Semua akun memakai password `password`.

| Role | Email | Nama |
|---|---|---|
| Admin | `admin@pens.ac.id` | Bambang Sutrisno |
| Petugas | `petugas1@pens.ac.id` | Siti Rahmawati |
| Petugas | `petugas2@pens.ac.id` | Ahmad Fauzi |

## Halaman Utama

| URL | Halaman | Akses |
|---|---|---|
| `/login` | Login | Publik |
| `/` | Dashboard statistik | Semua user login |
| `/books` | Daftar buku | Semua user login |
| `/categories` | Daftar kategori | Admin saja |
| `/members` | Daftar anggota | Semua user login |
| `/loans` | Daftar peminjaman | Semua user login |
| `/loans/report` | Laporan peminjaman dari API | Semua user login |
| `/profil` | Profil dan ganti password | Semua user login |

## Dokumentasi API

Seluruh endpoint REST API beserta contoh response lengkap ada di [API.md](./API.md).

Base URL: `http://127.0.0.1:8000/api`

## Skenario Demo

Urutan demo aplikasi untuk UAS ada di [DEMO.md](./DEMO.md).

## Perbedaan Model, View, dan Controller (MVC)

Model adalah komponen yang bertanggung jawab untuk berinteraksi dengan database, mengelola data, dan menerapkan aturan bisnis aplikasi. View adalah antarmuka visual (UI) yang bertugas mempresentasikan atau menampilkan data tersebut kepada pengguna akhir. Sedangkan Controller bertindak sebagai penghubung yang menerima permintaan (request) dari pengguna, mengambil atau menyimpan data melalui Model, dan meneruskan hasilnya untuk ditampilkan oleh View.
