# HCIS Dev Project (Frontend & Backend)

Proyek ini dibangun menggunakan arsitektur headless yang memisahkan Frontend (Vue 3 / Vite) dan Backend (Laravel 11), dan dijalankan sepenuhnya menggunakan Docker Compose.

## Persyaratan
- Docker
- Docker Compose

## Struktur Folder
- `backend/` : Berisi source code Laravel 11.
- `frontend/` : Berisi source code Vue 3 (Vite).
- `docker-compose.yml` : Konfigurasi layanan Docker.

## Cara Menjalankan Project

1. **Start Services**
   Jalankan perintah berikut di root folder project:
   ```bash
   docker compose up -d --build
   ```
   Tiga container akan menyala: `hcis-db` (Postgres), `hcis-backend` (Laravel pada port 8000), `hcis-frontend` (Vue pada port 5173).

2. **Jalankan Database Migrations**
   Setelah container menyala, jalankan perintah ini untuk melakukan migrasi database (termasuk tabel users):
   ```bash
   docker compose exec backend php artisan migrate
   ```

3. **Akses Aplikasi**
   - Frontend Vue: Buka browser ke `http://localhost:5173/`
   - Backend API: `http://localhost:8000/api`
   
   Halaman Frontend secara default diarahkan dengan Vue Router. Anda dapat masuk ke `/login` untuk mencoba fitur otentikasi.

## Catatan Autentikasi
Untuk Proof of Concept (PoC) ini, sistem login telah dilengkapi mekanisme auto-create user:
- Email: `admin@admin.com`
- Password: `password`
Jika user belum ada di database, ia akan otomatis dibuat saat Anda melakukan login dengan email tersebut.
