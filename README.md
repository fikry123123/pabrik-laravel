# PabrikPro - Sistem Manajemen Pabrik

Sistem informasi manajemen pabrik yang telah dimodernisasi dari PHP murni (Legacy) ke **Laravel 12/13**. Aplikasi ini menyediakan dua antarmuka utama:
1. **Web (Admin Panel)**: Menggunakan Blade Template & Session Auth untuk manajemen operasional di kantor/komputer.
2. **API (Mobile)**: Menggunakan Laravel Sanctum (JSON) yang siap dikonsumsi oleh aplikasi mobile seperti Flutter untuk operasional di lapangan.

---

## 🛠 Teknologi yang Digunakan
- **Framework:** Laravel 12 / 13
- **Database:** MySQL
- **Authentication:** Session (Web) & Laravel Sanctum (API)
- **Role Management:** Custom Middleware (`role:admin,editor,reviewer`)

---

## 🚀 Cara Menjalankan Project (Local Development)

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Konfigurasi Environment (`.env`)
Pastikan file `.env` sudah ada dan konfigurasinya sesuai. (Jika belum, copy dari `.env.example`).
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_pabrik
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Jalankan Migrasi & Seeder Database
*Catatan: Ini akan menghapus data lama dan membuat struktur tabel baru beserta akun default.*
```bash
php artisan migrate:fresh --seed
```

### 5. Jalankan Server
Buka dua terminal berbeda (pastikan posisinya di root project), jalankan:
```bash
# Terminal 1: Untuk backend (PHP/Laravel)
php artisan serve

# Terminal 2: Untuk frontend (Vite/Tailwind JS)
npm run dev
```

Project sekarang dapat diakses melalui browser di: **http://localhost:8000**

---

## 🔐 Akun Login Default

Setelah melakukan `db:seed`, sistem akan membuatkan beberapa user default untuk testing (login menggunakan **username**).

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Editor | `editor` | `editor123` |

---

## 📱 Dokumentasi API (Untuk Flutter/Mobile)

Base URL: `http://localhost:8000/api`

Header wajib untuk semua request yang butuh otentikasi:
```json
{
    "Accept": "application/json",
    "Authorization": "Bearer <YOUR_TOKEN_HERE>"
}
```

### Authentication
- `POST /login` : Login user (Body: `username`, `password`) - *Mengembalikan Token*
- `POST /logout` : Logout user (Wajib Bearer Token)
- `GET /me` : Cek data user login saat ini

### Modul Utama (Contoh Endpoint)
*(Endpoint berikut ini membutuhkan Bearer Token)*

| Modul | Method | Endpoint | Akses |
|-------|--------|----------|-------|
| **Dashboard** | GET | `/dashboard` | Semua Role |
| **Inventory** | GET | `/inventory` | Semua Role |
| **Inventory** | POST | `/inventory` | Admin, Editor |
| **Recipe/BOM** | GET | `/recipes` | Semua Role |
| **Production** | POST | `/production/start` | Admin, Editor |
| **Outbound** | GET | `/outbound` | Semua Role |

---

## 📦 Perbedaan dengan Versi PHP Murni Sebelumnya

1. **Struktur MVC:** Kode lebih terstruktur, memisahkan Logika (Controller), Database (Model), dan Tampilan (View).
2. **Keamanan:** Terlindung dari serangan SQL Injection berkat Eloquent ORM Laravel.
3. **Sistem Login:** Tidak lagi menggunakan `$_SESSION` manual. Sekarang menggunakan Laravel Auth yang aman.
4. **Validasi:** Semua input sekarang difilter dengan `$request->validate()`.
5. **Database:** Struktur tabel diatur secara otomatis dengan fitur *Migration*, tidak perlu repot import/export `.sql` manual.

