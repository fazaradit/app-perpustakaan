# Dokumentasi REST API app-perpustakaan

Dokumentasi seluruh endpoint REST API hasil Praktikum Pertemuan 9 (Laravel 12).
Contoh response di bawah adalah hasil pemanggilan langsung ke aplikasi (identik dengan yang ditampilkan Postman), bukan contoh yang dikarang.

## Informasi Umum

| Item | Keterangan |
|---|---|
| Base URL | `http://127.0.0.1:8000/api` |
| Format data | JSON (`Content-Type: application/json`) |
| Header wajib | `Accept: application/json` |
| Autentikasi | Tidak ada (endpoint publik pada pertemuan ini) |
| Prefix route | Otomatis `/api` dari `routes/api.php` |

Struktur response daftar (menggunakan pagination Laravel):

```json
{
  "data": [ ... ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "last_page": 1, "per_page": 10, "total": 3 }
}
```

Kode status yang dipakai:

| Status | Arti |
|---|---|
| `200 OK` | Berhasil membaca / memperbarui |
| `201 Created` | Berhasil membuat resource baru |
| `404 Not Found` | ID yang diminta tidak ada (`findOrFail`) |
| `422 Unprocessable Entity` | Data gagal validasi (ada object `errors`) |

> Catatan: beberapa contoh response memakai data uji sementara (buku id `9`, pinjaman id `5`) yang sudah dihapus setelah pengujian. Data lain (buku id `3`, `6`, `7`, anggota id `1`) adalah data asli aplikasi.

---

## Ringkasan 11 Endpoint

| No | Method | URL | Body | Status | Contoh Response (ringkas) |
|---|---|---|---|---|---|
| 1 | `GET` | `/api/books` | – | `200` | `{"data":[{"id":3,"judul":"Pemrograman Web Modern Revisi","category":{"id":1,"nama_kategori":"testingcoba"}}],"links":{...},"meta":{"total":3,...}}` |
| 2 | `GET` | `/api/books/{id}` | – | `200` | `{"data":{"id":3,"judul":"Pemrograman Web Modern Revisi","stok":8,"category":{"id":1,"nama_kategori":"testingcoba"}}}` |
| 3 | `POST` | `/api/books` | `{"judul":"...","penulis":"...","penerbit":"...","tahun_terbit":2024,"isbn":"...","stok":10,"category_id":1}` | `201` | `{"data":{"id":9,"judul":"Buku Uji API Dokumentasi","category":{"id":1,"nama_kategori":"testingcoba"}}}` |
| 4 | `PUT` | `/api/books/{id}` | `{"judul":"...","penulis":"...","penerbit":"...","tahun_terbit":2024,"isbn":"...","stok":8,"category_id":1}` | `200` | `{"data":{"id":9,"judul":"Buku Uji API Dokumentasi Revisi","stok":8}}` |
| 5 | `DELETE` | `/api/books/{id}` | – | `200` | `{"message":"Buku berhasil dihapus."}` |
| 6 | `GET` | `/api/members` | – | `200` | `{"data":[{"id":1,"nama":"Radityarma Naufal Fazani","nim":"3125600008"}],"links":{...},"meta":{"total":10,...}}` |
| 7 | `GET` | `/api/members/{id}` | – | `200` | `{"data":{"id":1,"nama":"Radityarma Naufal Fazani","nim":"3125600008","status":"aktif"}}` |
| 8 | `GET` | `/api/loans` | – | `200` | `{"data":[{"id":2,"member":{"id":1,"nama":"..."},"petugas":{"id":1,"name":"Petugas Perpustakaan"},"status":"dikembalikan","books":[{"id":3,"judul":"..."}]}],"links":{...},"meta":{"total":3,...}}` |
| 9 | `POST` | `/api/loans` | `{"member_id":1,"user_id":1,"tanggal_pinjam":"2026-11-02","tanggal_kembali":"2026-11-09","book_ids":[7]}` | `201` | `{"data":{"id":5,"member":{"id":1,"nama":"..."},"petugas":{...},"status":"dipinjam","books":[{"id":7,"judul":"Mastering Laravel 11"}]}}` |
| 10 | `PUT` | `/api/loans/{id}/kembalikan` | – | `200` | `{"data":{"id":5,"status":"dikembalikan","tanggal_dikembalikan":"2026-11-02","books":[...]}}` |
| 11 | `GET` | `/api/stats` | – | `200` | `{"total_buku":3,"total_anggota":10,"peminjaman_aktif":0}` |

### Filter dan Query String `GET /api/books`

| Query | Contoh | Fungsi |
|---|---|---|
| `judul` | `/api/books?judul=Laravel` | Pencarian sebagian judul (`LIKE %kata%`) |
| `category_id` | `/api/books?category_id=1` | Filter kategori secara persis |

### Skenario Gagal

| Endpoint | Kondisi | Status | Contoh Response |
|---|---|---|---|
| `POST /api/books` | Body `{}` | `422` | `{"message":"Judul buku wajib diisi. (and 5 more errors)","errors":{"judul":["Judul buku wajib diisi."],...,"category_id":["Kategori wajib dipilih."]}}` |
| `GET /api/books/999` | ID tidak ada | `404` | `{"message":"No query results for model [App\\Models\\Book] 999"}` |
| `PUT /api/books/999` | ID tidak ada | `404` | `{"message":"No query results for model [App\\Models\\Book] 999"}` |
| `DELETE /api/books/999` | ID tidak ada | `404` | `{"message":"No query results for model [App\\Models\\Book] 999"}` |
| `GET /api/members/999` | ID tidak ada | `404` | `{"message":"No query results for model [App\\Models\\Member] 999"}` |
| `POST /api/loans` | Body `{}` | `422` | `{"message":"Anggota wajib dipilih. (and 4 more errors)","errors":{"member_id":["Anggota wajib dipilih."],...,"book_ids":["Pilih minimal satu buku."]}}` |
| `PUT /api/loans/999/kembalikan` | ID tidak ada | `404` | `{"message":"No query results for model [App\\Models\\Loan] 999"}` |

---

## Detail Endpoint

### 1. `GET /api/books` - Daftar Buku

- Request: tanpa body.
- Response `200 OK`:

```json
{
    "data": [
        {
            "id": 3,
            "judul": "Pemrograman Web Modern Revisi",
            "penulis": "Radityarma",
            "penerbit": "Informatika",
            "tahun_terbit": "2024",
            "isbn": "978-602-9999-00-1",
            "stok": 8,
            "sampul": null,
            "category": {
                "id": 1,
                "nama_kategori": "testingcoba"
            },
            "created_at": "2026-09-30T02:35:59.000000Z",
            "updated_at": "2026-11-02T12:13:41.000000Z"
        },
        {
            "id": 6,
            "judul": "Pemrograman Web Modern",
            "penulis": "John Doe",
            "penerbit": "Informatika",
            "tahun_terbit": "2024",
            "isbn": null,
            "stok": 5,
            "sampul": null,
            "category": {
                "id": 1,
                "nama_kategori": "testingcoba"
            },
            "created_at": "2026-09-30T02:53:54.000000Z",
            "updated_at": "2026-09-30T02:53:54.000000Z"
        },
        {
            "id": 7,
            "judul": "Mastering Laravel 11",
            "penulis": "Jane Smith",
            "penerbit": "Tech Press",
            "tahun_terbit": "2024",
            "isbn": null,
            "stok": 3,
            "sampul": null,
            "category": {
                "id": 1,
                "nama_kategori": "testingcoba"
            },
            "created_at": "2026-09-30T02:53:54.000000Z",
            "updated_at": "2026-09-30T02:53:54.000000Z"
        }
    ],
    "links": {
        "first": "http://127.0.0.1:8000/api/books?page=1",
        "last": "http://127.0.0.1:8000/api/books?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "page": null,
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/books?page=1",
                "label": "1",
                "page": 1,
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "page": null,
                "active": false
            }
        ],
        "path": "http://127.0.0.1:8000/api/books",
        "per_page": 10,
        "to": 3,
        "total": 3
    }
}
```

#### Filter `judul` - `GET /api/books?judul=Laravel`

- Request: tanpa body.
- Response `200 OK` (hanya buku yang judulnya mengandung "Laravel"):

```json
{
    "data": [
        {
            "id": 7,
            "judul": "Mastering Laravel 11",
            "penulis": "Jane Smith",
            "penerbit": "Tech Press",
            "tahun_terbit": "2024",
            "isbn": null,
            "stok": 3,
            "sampul": null,
            "category": {
                "id": 1,
                "nama_kategori": "testingcoba"
            },
            "created_at": "2026-09-30T02:53:54.000000Z",
            "updated_at": "2026-09-30T02:53:54.000000Z"
        }
    ],
    "links": {
        "first": "http://127.0.0.1:8000/api/books?page=1",
        "last": "http://127.0.0.1:8000/api/books?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "page": null,
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/books?page=1",
                "label": "1",
                "page": 1,
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "page": null,
                "active": false
            }
        ],
        "path": "http://127.0.0.1:8000/api/books",
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

#### Filter `category_id` - `GET /api/books?category_id=1`

- Response `200 OK`: seluruh buku dengan `category.id = 1`, bentuk response sama seperti daftar buku (3 item, `meta.total = 3`).

### 2. `GET /api/books/{id}` - Detail Buku

- Request: tanpa body.
- Response `200 OK`:

```json
{
    "data": {
        "id": 3,
        "judul": "Pemrograman Web Modern Revisi",
        "penulis": "Radityarma",
        "penerbit": "Informatika",
        "tahun_terbit": "2024",
        "isbn": "978-602-9999-00-1",
        "stok": 8,
        "sampul": null,
        "category": {
            "id": 1,
            "nama_kategori": "testingcoba"
        },
        "created_at": "2026-09-30T02:35:59.000000Z",
        "updated_at": "2026-11-02T12:13:41.000000Z"
    }
}
```

- Response `404 Not Found` untuk id `999`:

```json
{
    "message": "No query results for model [App\\Models\\Book] 999"
}
```

### 3. `POST /api/books` - Tambah Buku

- Body (raw JSON):

```json
{
    "judul": "Buku Uji API Dokumentasi",
    "penulis": "Radityarma",
    "penerbit": "Informatika",
    "tahun_terbit": 2024,
    "isbn": "978-000-0000-00-1",
    "stok": 10,
    "category_id": 1
}
```

- Response `201 Created`:

```json
{
    "data": {
        "id": 9,
        "judul": "Buku Uji API Dokumentasi",
        "penulis": "Radityarma",
        "penerbit": "Informatika",
        "tahun_terbit": 2024,
        "isbn": "978-000-0000-00-1",
        "stok": 10,
        "sampul": null,
        "category": {
            "id": 1,
            "nama_kategori": "testingcoba"
        },
        "created_at": "2026-11-02T12:43:38.000000Z",
        "updated_at": "2026-11-02T12:43:38.000000Z"
    }
}
```

- Response `422 Unprocessable Entity` bila body `{}`:

```json
{
    "message": "Judul buku wajib diisi. (and 5 more errors)",
    "errors": {
        "judul": [
            "Judul buku wajib diisi."
        ],
        "penulis": [
            "Nama penulis wajib diisi."
        ],
        "penerbit": [
            "Nama penerbit wajib diisi."
        ],
        "tahun_terbit": [
            "Tahun terbit wajib diisi."
        ],
        "stok": [
            "Stok wajib diisi."
        ],
        "category_id": [
            "Kategori wajib dipilih."
        ]
    }
}
```

### 4. `PUT /api/books/{id}` - Update Buku

- Body (raw JSON, semua field wajib dikirim):

```json
{
    "judul": "Buku Uji API Dokumentasi Revisi",
    "penulis": "Radityarma",
    "penerbit": "Informatika",
    "tahun_terbit": 2024,
    "isbn": "978-000-0000-00-1",
    "stok": 8,
    "category_id": 1
}
```

- Response `200 OK`:

```json
{
    "data": {
        "id": 9,
        "judul": "Buku Uji API Dokumentasi Revisi",
        "penulis": "Radityarma",
        "penerbit": "Informatika",
        "tahun_terbit": 2024,
        "isbn": "978-000-0000-00-1",
        "stok": 8,
        "sampul": null,
        "category": {
            "id": 1,
            "nama_kategori": "testingcoba"
        },
        "created_at": "2026-11-02T12:43:38.000000Z",
        "updated_at": "2026-11-02T12:43:39.000000Z"
    }
}
```

- `404 Not Found` bila id tidak ada; `422` bila field wajib tidak dikirim.

### 5. `DELETE /api/books/{id}` - Hapus Buku

- Request: tanpa body.
- Response `200 OK`:

```json
{
    "message": "Buku berhasil dihapus."
}
```

- `404 Not Found` bila id tidak ada.

### 6. `GET /api/members` - Daftar Anggota

- Response `200 OK` (10 anggota, ditampilkan 3 pertama; `...` = item lain dari response asli):

```json
{
    "data": [
        {
            "id": 1,
            "nama": "Radityarma Naufal Fazani",
            "nim": "3125600008",
            "email": "fazaradityarma@gmail.com",
            "nomor_telepon": "081239345766",
            "alamat": "Tuban",
            "status": "aktif",
            "created_at": "2026-09-23T02:58:02.000000Z",
            "updated_at": "2026-09-23T02:58:02.000000Z"
        },
        {
            "id": 3,
            "nama": "Reyvan",
            "nim": "3125600002",
            "email": "Reyvan@gmail.com",
            "nomor_telepon": "0987656781923",
            "alamat": "Test",
            "status": "aktif",
            "created_at": "2026-09-25T17:51:48.000000Z",
            "updated_at": "2026-09-25T18:27:00.000000Z"
        },
        {
            "id": 5,
            "nama": "Fieza",
            "nim": "3125600004",
            "email": "Fieza@gmail.com",
            "nomor_telepon": "0812343586",
            "alamat": "surabaya",
            "status": "aktif",
            "created_at": "2026-09-25T18:05:17.000000Z",
            "updated_at": "2026-09-25T18:05:17.000000Z"
        }
    ],
    "links": {
        "first": "http://127.0.0.1:8000/api/members?page=1",
        "last": "http://127.0.0.1:8000/api/members?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 10,
        "to": 10,
        "total": 10
    }
}
```

### 7. `GET /api/members/{id}` - Detail Anggota

- Response `200 OK`:

```json
{
    "data": {
        "id": 1,
        "nama": "Radityarma Naufal Fazani",
        "nim": "3125600008",
        "email": "fazaradityarma@gmail.com",
        "nomor_telepon": "081239345766",
        "alamat": "Tuban",
        "status": "aktif",
        "created_at": "2026-09-23T02:58:02.000000Z",
        "updated_at": "2026-09-23T02:58:02.000000Z"
    }
}
```

- Response `404 Not Found` untuk id `999`:

```json
{
    "message": "No query results for model [App\\Models\\Member] 999"
}
```

### 8. `GET /api/loans` - Daftar Peminjaman

- Response `200 OK` (relasi `member`, `petugas`, dan `books` sudah terisi):

```json
{
    "data": [
        {
            "id": 2,
            "member": {
                "id": 1,
                "nama": "Radityarma Naufal Fazani"
            },
            "petugas": {
                "id": 1,
                "name": "Petugas Perpustakaan"
            },
            "tanggal_pinjam": "2026-09-30",
            "tanggal_kembali": "2026-10-07",
            "tanggal_dikembalikan": "2026-09-30",
            "status": "dikembalikan",
            "books": [
                {
                    "id": 3,
                    "judul": "Pemrograman Web Modern Revisi"
                }
            ]
        },
        {
            "id": 3,
            "member": {
                "id": 3,
                "nama": "Reyvan"
            },
            "petugas": {
                "id": 1,
                "name": "Petugas Perpustakaan"
            },
            "tanggal_pinjam": "2026-09-29",
            "tanggal_kembali": "2026-09-30",
            "tanggal_dikembalikan": "2026-09-30",
            "status": "dikembalikan",
            "books": [
                {
                    "id": 6,
                    "judul": "Pemrograman Web Modern"
                }
            ]
        },
        {
            "id": 4,
            "member": {
                "id": 1,
                "nama": "Radityarma Naufal Fazani"
            },
            "petugas": {
                "id": 1,
                "name": "Petugas Perpustakaan"
            },
            "tanggal_pinjam": "2026-11-01",
            "tanggal_kembali": "2026-11-08",
            "tanggal_dikembalikan": "2026-11-02",
            "status": "dikembalikan",
            "books": [
                {
                    "id": 3,
                    "judul": "Pemrograman Web Modern Revisi"
                },
                {
                    "id": 6,
                    "judul": "Pemrograman Web Modern"
                }
            ]
        }
    ],
    "links": {
        "first": "http://127.0.0.1:8000/api/loans?page=1",
        "last": "http://127.0.0.1:8000/api/loans?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 10,
        "to": 3,
        "total": 3
    }
}
```

### 9. `POST /api/loans` - Tambah Peminjaman

- Body (raw JSON):

```json
{
    "member_id": 1,
    "user_id": 1,
    "tanggal_pinjam": "2026-11-02",
    "tanggal_kembali": "2026-11-09",
    "book_ids": [7]
}
```

- Response `201 Created` (perhatikan `status` bernilai `"dipinjam"`, bukan `null`):

```json
{
    "data": {
        "id": 5,
        "member": {
            "id": 1,
            "nama": "Radityarma Naufal Fazani"
        },
        "petugas": {
            "id": 1,
            "name": "Petugas Perpustakaan"
        },
        "tanggal_pinjam": "2026-11-02",
        "tanggal_kembali": "2026-11-09",
        "tanggal_dikembalikan": null,
        "status": "dipinjam",
        "books": [
            {
                "id": 7,
                "judul": "Mastering Laravel 11"
            }
        ]
    }
}
```

- Response `422 Unprocessable Entity` bila body `{}`:

```json
{
    "message": "Anggota wajib dipilih. (and 4 more errors)",
    "errors": {
        "member_id": [
            "Anggota wajib dipilih."
        ],
        "user_id": [
            "Petugas (user_id) wajib diisi."
        ],
        "tanggal_pinjam": [
            "Tanggal pinjam wajib diisi."
        ],
        "tanggal_kembali": [
            "Tanggal kembali wajib diisi."
        ],
        "book_ids": [
            "Pilih minimal satu buku."
        ]
    }
}
```

### 10. `PUT /api/loans/{id}/kembalikan` - Kembalikan Buku

- Request: tanpa body.
- Response `200 OK`:

```json
{
    "data": {
        "id": 5,
        "member": {
            "id": 1,
            "nama": "Radityarma Naufal Fazani"
        },
        "petugas": {
            "id": 1,
            "name": "Petugas Perpustakaan"
        },
        "tanggal_pinjam": "2026-11-02",
        "tanggal_kembali": "2026-11-09",
        "tanggal_dikembalikan": "2026-11-02",
        "status": "dikembalikan",
        "books": [
            {
                "id": 7,
                "judul": "Mastering Laravel 11"
            }
        ]
    }
}
```

- `404 Not Found` bila id tidak ada:

```json
{
    "message": "No query results for model [App\\Models\\Loan] 999"
}
```

### 11. `GET /api/stats` - Statistik Ringkasan

- Response `200 OK`:

```json
{
    "total_buku": 3,
    "total_anggota": 10,
    "peminjaman_aktif": 0
}
```

Keterangan field:
- `total_buku` - jumlah seluruh baris tabel `books`
- `total_anggota` - jumlah seluruh baris tabel `members`
- `peminjaman_aktif` - jumlah `loans` dengan `status = 'dipinjam'`

---

## Catatan Implementasi

- Seluruh endpoint didaftarkan di `routes/api.php` dan otomatis mendapat prefix `/api` melalui `bootstrap/app.php`.
- `books`, `members`, dan `loans` memakai API Resource (`BookResource`, `MemberResource`, `LoanResource`) agar format response konsisten dan kolom sensitif tidak ikut terkirim.
- `stats` memakai `response()->json()` langsung karena bukan representasi satu Model.
- `index` pada `books`, `members`, dan `loans` memakai `paginate(10)` sehingga response selalu berisi `data`, `links`, dan `meta`.
- `findOrFail()` menghasilkan `404` otomatis, dan validasi gagal menghasilkan `422` otomatis pada route API.
