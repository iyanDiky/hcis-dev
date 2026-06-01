# Planning: Integrasi Fitur Login (Backend & Frontend)

Dokumen ini berisi panduan teknis untuk mengimplementasikan fitur login secara komprehensif, mulai dari penyesuaian API di sisi Backend (Laravel) hingga integrasi UI di sisi Frontend (Vue 3).

Dokumen ini ditulis agar dapat dieksekusi langsung oleh *programmer* atau AI agent.

---

## 1. Spesifikasi API Login (Backend)

API Login sebenarnya sudah memiliki logika dasar dari iterasi sebelumnya, namun perlu dikonfirmasi dan dirapikan kembali agar benar-benar terhubung dengan data *seed* (`admin` / `password`).

**Endpoint:** `POST /api/login`

### Request Body
Berupa JSON dengan format:
```json
{
  "username": "admin",
  "password": "password"
}
```

### Response Body: Success (200 OK)
Mengembalikan data user lengkap (termasuk relasi SDM dan SDM Data) dan sebuah token (bisa dummy token atau Laravel Sanctum token jika sudah di-setup).
```json
{
  "message": "Login sukses",
  "token": "dummy-token-1234567890",
  "user": {
    "id": "uuid-user-123",
    "username": "admin",
    "status": 1,
    "error_login": 0,
    "sdm_relation": {
      "id": "uuid-sdm-123",
      "data": {
        "id": "uuid-sdm-data-123",
        "email": "hcis@bankkalsel.co.id",
        "nama": "Super Admin HCIS"
      }
    }
  }
}
```

### Aturan Logika Tambahan (Backend)
1. **Pengecekan Status Aktif:** Sebelum mengecek password, pastikan `status == 1`. Jika `status == 2` (terblokir) atau `0` (non-aktif), tolak login.
2. **Hitung Kegagalan Login:** Jika password salah, tambahkan nilai `error_login` + 1 di tabel `users`.
3. **Blokir Otomatis:** Jika `error_login` mencapai 3 (atau lebih), otomatis ubah `status` menjadi `2` (blokir) dan tolak akses login.
4. **Reset Kegagalan:** Jika login berhasil (password benar dan `status == 1`), kembalikan nilai `error_login` menjadi `0`.
5. **Manajemen Multi-Device (Single Login Limit):** 
   - Tambahkan sebuah *toggle* konfigurasi di `.env` (misal: `SINGLE_DEVICE_LOGIN=true`).
   - Jika konfigurasi ini aktif (`true`), sistem harus me-*revoke* (menghapus) semua token/sesi aktif sebelumnya milik user tersebut sebelum menerbitkan token yang baru. Dengan ini, *device* yang login lebih awal akan otomatis ter-logout (menerima respons 401 di panggilan API berikutnya).
   - Jika konfigurasi non-aktif (`false`), user diperbolehkan login bersamaan di banyak device.

### Response Body: Error (401 Unauthorized / 403 Forbidden)
- **Username tidak ada:**
  ```json
  { "message": "Username tidak ditemukan" }
  ```
  *(HTTP Status: 401)*
- **Akun tidak aktif / terblokir:**
  ```json
  { "message": "Akun tidak aktif atau diblokir" }
  ```
  *(HTTP Status: 403)*
- **Password salah:**
  ```json
  { "message": "Password salah" }
  ```
  *(HTTP Status: 401)*

---

## 2. Integrasi Frontend (Vue 3)

Frontend berada di direktori `frontend/`. Framework yang digunakan adalah Vue 3 dengan Vite.

### Kebutuhan Implementasi Frontend:
1. **Instalasi HTTP Client (opsional tapi disarankan):**
   Pastikan menggunakan `axios` atau native `fetch` untuk melakukan HTTP POST request ke `http://localhost:8000/api/login`.
   *(Catatan CORS: Jika terjadi error CORS, pastikan backend Laravel sudah dikonfigurasi `config/cors.php` untuk mengizinkan origin `http://localhost:5173`)*.

2. **State Management (Menyimpan Token):**
   - Saat response success diterima, simpan `token` dan data `user` (nama, relasi) ke dalam **LocalStorage** atau **SessionStorage** (misal: `localStorage.setItem('auth_token', response.data.token)`).
   - Opsi lain: Gunakan *Pinia* jika aplikasi sudah setup state management yang kompleks.

3. **Halaman Login (`/login`):**
   - Buat atau modifikasi komponen form login yang memuat input `username` dan `password`.
   - Hubungkan form dengan fungsi submit (menggunakan `@submit.prevent`).
   - Tampilkan indikator *Loading* saat request sedang berjalan.
   - Tampilkan *Error Message* berwarna merah jika HTTP Response mengembalikan status 401 atau 403. Tampilkan isi `response.data.message` ke layar agar user tahu penyebab gagal login.

4. **Redireksi (Router):**
   - Setelah login sukses dan token disimpan, gunakan `Vue Router` untuk mengarahkan pengguna ke halaman *Dashboard* (`router.push('/')` atau `/dashboard`).
   - Berikan *Route Guard* (opsional untuk pengembangan lebih lanjut) agar halaman dashboard tidak bisa diakses jika belum ada token di LocalStorage.

---

## 3. Instruksi untuk Programmer / AI Agent

**Task List untuk Eksekusi:**
- [ ] Verifikasi ulang fungsi `login()` di `backend/app/Http/Controllers/AuthController.php` apakah strukturnya sudah sesuai dengan spesifikasi di atas. Jika belum, lakukan penyesuaian.
- [ ] Pastikan konfigurasi CORS di backend Laravel mengizinkan request dari frontend (jika frontend dan backend berjalan di port berbeda).
- [ ] Buka/buat file komponen halaman login di frontend Vue (misalnya `frontend/src/views/Login.vue`).
- [ ] Tambahkan logika integrasi API (fetch/axios) di dalam fungsi *handle submit* form login.
- [ ] Implementasikan penanganan *Success* (simpan token, redirect) dan *Error* (tampilkan pesan).
- [ ] Lakukan verifikasi manual: Jalankan server frontend `npm run dev` dan server backend, lalu uji coba login menggunakan kredensial `admin` dan `password` hingga berhasil ter-redirect.
