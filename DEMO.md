# Skenario Demo UAS - app-perpustakaan

Dokumen ini berisi urutan demo aplikasi `app-perpustakaan` untuk UAS Workshop Pemrograman Framework.
Ikuti langkahnya dari atas ke bawah. Semua langkah dilakukan langsung di browser dan Postman, bukan lewat slide.

Repositori: <https://github.com/fazaradit/app-perpustakaan>

---

## Persiapan sebelum demo

### 1. Kebutuhan

- PHP 8.2 atau lebih baru
- Composer
- MySQL (XAMPP/Laragon) dengan database `db_perpustakaan`
- Browser dan Postman sudah terbuka

### 2. Reset database ke kondisi demo

```bash
php artisan migrate:fresh --seed
```

Perintah ini menghapus seluruh tabel lalu mengisinya kembali dengan data dummy dari Seeder,
sehingga angka yang tampil saat demo selalu konsisten:

| Data | Jumlah |
|---|---|
| Buku | 20 |
| Anggota | 15 |
| Peminjaman | 10 |
| Kategori | 5 |

### 3. Jalankan dua server

Skenario ini membutuhkan **dua terminal** yang berjalan bersamaan.

Terminal 1, server utama untuk trafik browser:

```bash
php artisan serve --port=8000
```

Terminal 2, server khusus panggilan API internal:

```bash
php artisan serve --port=8011
```

Keduanya harus menampilkan tulisan `Server running on [http://127.0.0.1:xxxx]`.

**Kenapa perlu dua server?**
`php artisan serve` memproses satu request dalam satu waktu. Halaman Dashboard dan Laporan
memanggil API aplikasi sendiri lewat `Http::get()`. Kalau server hanya satu, server sedang sibuk
memproses request halaman sambil menunggu balasan dari request API yang tidak akan pernah diterima.
Halaman akan menggantung sampai timeout. Dengan port kedua, request API ditangani proses PHP lain
yang membaca database yang sama.

### 4. Akun untuk login

| Role | Email | Password |
|---|---|---|
| Admin | `admin@pens.ac.id` | `password` |
| Petugas | `petugas1@pens.ac.id` | `password` |
| Petugas | `petugas2@pens.ac.id` | `password` |

Hafalkan kredensial ini supaya saat demo tidak perlu membuka database.

---

## Urutan demo

### Langkah 1 - Login sebagai admin

1. Buka `http://127.0.0.1:8000/login`.
2. Masuk dengan `admin@pens.ac.id` / `password`.
3. Setelah berhasil, perhatikan:
   - redirect diarahkan ke halaman Dashboard (`/`), bukan ke `/books`
   - muncul flash message "Login berhasil, selamat datang Bambang Sutrisno."
   - navbar menampilkan "Bambang Sutrisno (Admin)"
   - menu Kategori ikut muncul karena role-nya admin

**Yang dijelaskan:** redirect ke Dashboard adalah hasil perubahan Pertemuan 10
(`AuthController@login` memakai `route('dashboard')`).

### Langkah 2 - Tampilkan dashboard statistik

1. Pastikan berada di halaman Dashboard.
2. Tunjukkan tiga kartu statistik:

| Kartu | Nilai |
|---|---|
| Total Buku | 20 |
| Total Anggota | 15 |
| Peminjaman Aktif | sesuai data hasil seeding (hasil `LoanSeeder` memakai status acak) |

**Yang dijelaskan:** angka ini tidak diambil dengan query Eloquent langsung. `DashboardController@index`
memanggil `Http::get()` ke `GET /api/stats`, lalu membaca hasil `json()` dan mengirimkannya ke
`dashboard.blade.php`. Kalau server port 8011 dimatikan, halaman tetap tampil dengan angka 0 karena
ada `try/catch ConnectionException`. Ini bisa didemokan sebagai bukti penanganan error.

### Langkah 3 - Tambah kategori baru

1. Buka menu **Kategori** (atau `http://127.0.0.1:8000/categories`).
2. Klik **Tambah Kategori**.
3. Isi contoh data:
   - Nama Kategori: `Majalah`
   - Deskripsi: `Terbitan berkala berisi artikel dan berita.`
4. Klik **Simpan**.

**Hasil yang ditunjukkan:** kembali ke daftar kategori, muncul flash message
`Kategori "Majalah" berhasil ditambahkan.` dan baris baru terlihat di tabel.

### Langkah 4 - Tambah buku dengan kategori

1. Buka menu **Buku** (atau `http://127.0.0.1:8000/books`).
2. Klik **Tambah Buku**.
3. Isi contoh data:
   - Judul: `Teknologi Informasi Kampus`
   - Penulis: `Radityarma Naufal Fazani`
   - Penerbit: `Informatika Bandung`
   - Tahun Terbit: `2025`
   - ISBN: `978-000-0000-00-9`
   - Stok: `5`
   - Kategori: pilih `Majalah` (kategori yang baru dibuat di langkah 3)
4. Klik **Simpan**.

**Hasil yang ditunjukkan:** buku muncul di daftar, dan kolom kategori menampilkan nama
`Majalah`, bukan angka `category_id`. Ini membuktikan relasi `belongsTo` kategori terpasang benar.

### Langkah 5 - Tambah anggota baru

1. Buka menu **Anggota** (atau `http://127.0.0.1:8000/members`).
2. Klik **Tambah Anggota**.
3. Isi contoh data:
   - Nama: `Ayu Lestari`
   - NIM: `3125600099`
   - Email: `ayu.lestari@pens.ac.id`
   - No. Telepon: `081234567890`
   - Alamat: `Surabaya`
   - Status: `Aktif`
4. Klik **Simpan**.

**Hasil yang ditunjukkan:** anggota baru tampil di daftar dengan flash message sukses.

**Bukti validasi:** klik **Tambah Anggota** lagi, isi NIM `3125600099` atau email
`ayu.lestari@pens.ac.id` yang sama, lalu simpan. Form kembali muncul dengan pesan
`NIM sudah terdaftar.` atau `Email sudah terdaftar.`. Ini membuktikan validasi `unique`
di `StoreMemberRequest` bekerja di sisi server.

### Langkah 6 - Buat transaksi peminjaman

1. Buka menu **Peminjaman** (atau `http://127.0.0.1:8000/loans`).
2. Klik **+ Tambah Peminjaman**.
3. Isi form:
   - Anggota: pilih `Ayu Lestari (3125600099)`
   - Tanggal Pinjam: hari ini
   - Tanggal Kembali: tujuh hari setelah tanggal pinjam
   - Buku yang Dipinjam: centang `Teknologi Informasi Kampus` dan satu buku lain
4. Perhatikan teks **"Petugas pencatat: Bambang Sutrisno (otomatis dari akun yang login)."**
5. Klik **Simpan**.

**Yang dijelaskan:** tidak ada dropdown Petugas di form. Nilai `user_id` diisi otomatis oleh
`auth()->id()` di `LoanController@store`, karena identitas user sudah tersedia lewat session.
Satu transaksi juga bisa memuat lebih dari satu buku, dan setiap buku dicatat sebagai baris
terpisah di tabel `loan_items` lewat `$loan->loanItems()->create()`.

**Hasil yang ditunjukkan:** kembali ke daftar peminjaman dengan flash message
`Transaksi peminjaman berhasil dibuat.`

### Langkah 7 - Lihat daftar peminjaman aktif

1. Masih di halaman `/loans`.
2. Cari baris transaksi yang baru dibuat di langkah 6.
3. Tunjukkan bahwa baris tersebut berisi:
   - status `Dipinjam` dengan badge kuning
   - nama anggota `Ayu Lestari`, bukan ID
   - nama petugas `Bambang Sutrisno`, bukan ID
   - daftar judul buku yang dipinjam, dipisahkan koma

**Yang dijelaskan:** semua kolom itu berasal dari relasi `member`, `user`, dan `loanItems.book`
yang diambil sekaligus dengan eager loading `with([...])` di `LoanController@index`.
Tanpa eager loading, menampilkan 10 baris akan memicu puluhan query tambahan
(N+1 Query Problem).

### Langkah 8 - Proses pengembalian buku

1. Pada baris transaksi dari langkah 6, klik tombol **Kembalikan**.
2. Perhatikan flash message `Buku berhasil dikembalikan.`
3. Status baris tersebut berubah dari `Dipinjam` menjadi `Dikembalikan` dengan badge hijau, dan
   tombol Kembalikan hilang karena transaksi sudah selesai.

**Yang dijelaskan:** `LoanController@kembalikan` mengisi `status` menjadi `dikembalikan` dan
`tanggal_dikembalikan` dengan tanggal hari ini.

### Langkah 9 - Tampilkan halaman laporan

1. Buka menu **Laporan** (atau `http://127.0.0.1:8000/loans/report`).
2. Tunjukkan tabel berisi seluruh transaksi, termasuk transaksi yang baru diproses di langkah 6-8.
3. Tunjukkan navigasi halaman di bawah tabel kalau data lebih dari satu halaman.

**Yang dijelaskan:** halaman ini tidak memakai Eloquent. `LoanController@report` memanggil
`GET /api/loans` lewat `Http::get()`, lalu mengirim `data` dan `meta` hasil JSON ke view.
Karena sumbernya array biasa, view mengaksesnya dengan notasi array (`$loan['member']['nama']`)
dan membuat navigasi halaman manual dari `meta.current_page` dan `meta.last_page`.

**Demo bonus:** matikan Terminal 2 (port 8011), lalu refresh halaman ini. Tabel akan tampil
kosong dengan teks "Belum ada data peminjaman." dan bukan error 500. Nyalakan lagi server tersebut
setelah selesai.

### Langkah 10 - Demo API di Postman

Buka Postman dan siapkan dua request berikut. Header `Accept: application/json` wajib diisi.

**A. `GET http://127.0.0.1:8000/api/books`**

Tunjukkan:

- response JSON dengan struktur `data`, `links`, dan `meta`
- setiap buku punya objek `category` berisi `id` dan `nama_kategori`
- `meta.total` bernilai 20, dan `meta.last_page` lebih dari 1 karena jumlah data melebihi 10 per halaman
- filter bisa dicoba lewat query string, misalnya `?judul=Teknologi` atau `?category_id=1`

**Yang dijelaskan:** bentuk response diatur oleh `BookResource`, bukan `response()->json($book)` mentah.
Resource membuat format konsisten dan hanya mengirim kolom yang boleh dilihat klien.

**B. `GET http://127.0.0.1:8000/api/stats`**

Tunjukkan response:

```json
{
    "total_buku": 20,
    "total_anggota": 15,
    "peminjaman_aktif": 2
}
```

Angka `total_buku` dan `total_anggota` selalu 20 dan 15 karena berasal dari Seeder. Nilai `peminjaman_aktif` bergantung pada status hasil `LoanSeeder` dan akan berubah setelah transaksi diproses di langkah 8.
Bandingkan angka ini dengan kartu di Dashboard untuk membuktikan keduanya membaca sumber yang sama.

**Opsional, kalau waktu masih ada:**

| Request | Fungsi |
|---|---|
| `GET /api/members` | daftar anggota dengan pagination |
| `GET /api/loans` | daftar peminjaman lengkap dengan relasi anggota, petugas, dan buku |
| `POST /api/loans` | buat peminjaman lewat API, butuh `user_id` eksplisit karena API tidak memakai session |
| `PUT /api/loans/{id}/kembalikan` | proses pengembalian lewat API |
| `GET /api/books/999` | menunjukkan `404` untuk ID yang tidak ada |
| `POST /api/books` dengan body `{}` | menunjukkan `422` beserta daftar error validasi |

Dokumentasi lengkap seluruh endpoint ada di [`API.md`](./API.md).

### Langkah 11 - Tanya jawab konseptual

Dosen memilih beberapa pertanyaan. Ringkasan poin jawaban yang perlu disiapkan:

| No | Pertanyaan | Poin jawaban |
|---|---|---|
| 1 | Masalah kalau tanpa framework | Kode tersebar, tidak ada pemisahan tanggung jawab, perubahan kecil menyentuh banyak file. MVC memisahkan data, tampilan, dan alur. |
| 2 | Alur request tambah buku | Browser kirim POST, route mencocokkan ke `BookController@store`, middleware `auth` memeriksa sesi lebih dulu, `StoreBookRequest` memvalidasi, Model menyimpan, lalu redirect ke index. |
| 3 | Kenapa validasi tetap di server | Atribut HTML bisa dilewati dengan Postman atau menonaktifkan JavaScript. Server adalah batas kepercayaan terakhir. |
| 4 | Migration vs kelola tabel manual | Struktur tabel jadi versi terkontrol di Git, bisa dijalankan ulang di mesin lain, dan perubahan schema tercatat urut. |
| 5 | N+1 Query Problem | Menampilkan 10 peminjaman tanpa eager loading memicu satu query per relasi per baris. `with([...])` menggabungkannya menjadi beberapa query tetap. |
| 6 | Relasi Model vs foreign key | Foreign key menjaga integritas di level database. Relasi Eloquent memberi cara memanggil data terkait dari kode. Keduanya berbeda lapisan. |
| 7 | Autentikasi vs otorisasi | Autentikasi memastikan siapa penggunanya, ditangani `AuthController` dan middleware `auth`. Otorisasi membatasi apa yang boleh dilakukan, ditangani middleware `CheckAdminRole`. |
| 8 | Kenapa API mengembalikan JSON | Supaya klien non-browser (mobile, dashboard lain) bisa memakai data yang sama. HTML hanya berguna untuk browser. |
| 9 | Fungsi API Resource | Menyeragamkan bentuk response, menyembunyikan kolom sensitif, dan memisahkan cara penyajian dari Model. |
| 10 | Kenapa butuh dua server | `php artisan serve` satu request per waktu. Memanggil diri sendiri dari dalam request berjalan menyebabkan deadlock. Di produksi dengan Nginx dan PHP-FPM masalah ini tidak muncul. |
| 11 | Seeder vs Factory | Seeder mengisi data spesifik yang jumlahnya sedikit dan tetap. Factory menghasilkan banyak data acak lewat Faker. Dipisah karena tanggung jawabnya berbeda. |
| 12 | Menambah tabel baru | Migration, Model, Factory/Seeder, Controller, Form Request, route, view index/create/edit/show, lalu uji. |
| 13 | Kenapa dua branch | `dev` untuk pengembangan berjalan, `main` untuk kondisi stabil. Commit checkpoint per pertemuan membuat riwayat mudah ditelusuri. |

---

## Penutup demo

Sebelum meninggalkan sesi:

1. Pastikan kedua terminal server masih berjalan kalau dosen ingin mencoba sendiri.
2. Siapkan tautan repository GitHub yang bisa diakses publik.
3. Kalau ada langkah yang gagal, cek bagian troubleshooting di bawah sebelum menyimpulkan fitur tidak berfungsi.

---

## Troubleshooting

| Gejala | Penyebab | Solusi |
|---|---|---|
| Dashboard menampilkan 0 semua | Server port 8011 tidak jalan | Nyalakan terminal kedua dengan `php artisan serve --port=8011` |
| Laporan Peminjaman kosong | Sama seperti di atas | Nyalakan server port 8011 |
| Halaman menggantung lama lalu gagal | `INTERNAL_API_URL` menunjuk ke port yang sama dengan server utama | Pastikan `.env` berisi `INTERNAL_API_URL=http://127.0.0.1:8011` lalu jalankan `php artisan config:clear` |
| Akses `/categories` dapat 403 | Login sebagai petugas, bukan admin | Login ulang dengan `admin@pens.ac.id` |
| Angka statistik tidak sesuai | Database berisi sisa data uji coba | Jalankan `php artisan migrate:fresh --seed` lalu login ulang |
| Data dummy berbahasa Inggris | `APP_FAKER_LOCALE` masih `en_US` | Ubah ke `id_ID` di `.env`, lalu `php artisan config:clear` dan seed ulang |
