# Planning Pembuatan Project Aplikasi Terpisah (Frontend & Backend)

Dokumen ini berisi spesifikasi teknis dan panduan implementasi untuk pembuatan project baru. Sistem ini akan menggunakan arsitektur *Headless/Decoupled* yang memisahkan antara frontend dan backend, dan seluruh project akan dijalankan menggunakan Docker.

## 1. Arsitektur Utama

- **Backend**: Laravel (versi terbaru). Berfungsi murni sebagai RESTful API provider.
- **Frontend**: Vue.js (versi terbaru, direkomendasikan Vue 3 dengan Vite). Berfungsi sebagai Single Page Application (SPA) yang akan mengkonsumsi API dari backend.
- **Database**: PostgreSQL.
- **Infrastruktur/Deployment**: Docker & Docker Compose. Semua service (Backend, Frontend, Database) dikontainerisasi untuk kemudahan proses development dan operasional.
- **Tampilan UI**: Menggunakan referensi template HTML dari folder `metrica_v3.1`.

## 2. Struktur Direktori Utama (Usulan Monorepo)

Proyek akan menggunakan struktur folder sebagai berikut:

```text
/
├── backend/            # Source code Laravel
├── frontend/           # Source code Vue.js
├── database/           # (Opsional) Docker volume atau init scripts untuk PostgreSQL
├── docker-compose.yml  # File orkestrasi Docker untuk semua service
├── README.md           # Dokumentasi cara menjalankan project
└── issue.md            # File planning ini
```

## 3. Spesifikasi Teknis & Lingkup Pekerjaan

### A. Konfigurasi Infrastruktur (Docker)
1. Buat file `docker-compose.yml` di *root directory*.
2. Konfigurasikan minimal 3 *services* utama:
   - `db`: Menggunakan image PostgreSQL (misalnya `postgres:15-alpine`). Atur environment variables untuk koneksi database (DB_USER, DB_PASSWORD, DB_NAME).
   - `backend`: Image PHP-FPM / Nginx (atau Laravel Sail) yang dikonfigurasi untuk menjalankan Laravel. Service ini harus memiliki akses jaringan ke service `db`.
   - `frontend`: Image Node (untuk development menggunakan *dev server*) atau Nginx (untuk production) yang menjalankan Vue.js.

### B. Setup Backend (Laravel)
1. Lakukan instalasi Laravel terbaru di dalam direktori `backend/`.
2. Sesuaikan file `backend/.env` agar terkoneksi ke database PostgreSQL yang berjalan di kontainer Docker.
3. Konfigurasikan CORS (Cross-Origin Resource Sharing) agar frontend (Vue.js) dapat melakukan *request* API tanpa kendala keamanan dari browser.
4. Buat endpoint API dasar (misalnya `/api/health` atau GET `/api/user`) sebagai Proof of Concept (PoC) bahwa koneksi backend dan database telah berhasil.
5. Setup *Authentication* menggunakan Laravel Sanctum atau JWT untuk otentikasi API yang akan digunakan frontend.

### C. Setup Frontend (Vue.js)
1. Instalasi project Vue 3 (menggunakan *Vite*) di dalam direktori `frontend/`.
2. Integrasikan pustaka pendukung standar seperti Vue Router (untuk navigasi halaman) dan Pinia (untuk state management).
3. **Integrasi Template Tampilan `metrica_v3.1`**: 
   - Lakukan *slicing* (pemotongan) aset HTML, CSS, dan JS statis dari folder `metrica_v3.1`.
   - Buat komponen layout utama (Navbar, Sidebar, Footer) menjadi komponen re-usable berekstensi `.vue` (misal: `Sidebar.vue`, `Header.vue`).
   - Pindahkan *assets* statis (seperti gambar, CSS vendor, JS template) dari folder `metrica_v3.1/dist` atau `metrica_v3.1/src` ke folder `frontend/public/` atau `frontend/src/assets/`, lalu referensikan file-file tersebut di Vue.
4. Atur HTTP Client (seperti Axios) dengan base URL yang mengarah ke endpoint backend Laravel (misal: `http://localhost:8000/api`).

## 4. Langkah-Langkah Implementasi (Task Checklist)

Untuk Programmer / Agent AI yang akan mengimplementasikan instruksi ini, lakukan secara berurutan:

- [ ] **Task 1**: Buat folder kosong untuk `backend/` dan `frontend/`.
- [ ] **Task 2**: Buat file `docker-compose.yml` dengan 3 container: `db` (Postgres), `backend` (Laravel/PHP), dan `frontend` (Node/Vue). Pastikan masing-masing dapat berjalan (misal dengan dummy index file terlebih dahulu).
- [ ] **Task 3**: Generate framework Laravel di dalam folder `backend/`.
- [ ] **Task 4**: Generate Vue.js project (menggunakan Vite) di dalam folder `frontend/`.
- [ ] **Task 5**: Konfigurasikan koneksi database di Laravel (`.env`) ke service Docker PostgreSQL. Buat dan jalankan migrasi database pertama (tabel `users`).
- [ ] **Task 6**: Sesuaikan konfigurasi CORS di Laravel.
- [ ] **Task 7**: Lakukan *slicing* dari template `metrica_v3.1`. Ubah layout HTML menjadi sistem komponen Vue (komponen Navbar, Sidebar, Footer).
- [ ] **Task 8**: Buat form login dari template `metrica_v3.1` dan integrasikan fungsionalitasnya dengan endpoint API Login dari Laravel.
- [ ] **Task 9**: Tulis panduan lengkap di `README.md` tentang tata cara meng-up service (`docker-compose up -d`), menjalankan *migration*, dan perintah instalasi awal.

## 5. Catatan Ekstra
- Berhati-hatilah dengan pengaturan *port* di Docker Compose untuk menghindari *port clash*. Secara umum gunakan port yang aman: `8000` untuk API backend, `5173` untuk frontend, dan `5432` untuk database PostgreSQL.
- Tulis *commit message* secara berkala setelah tiap *task checklist* berhasil diselesaikan untuk *version control* yang rapi.
