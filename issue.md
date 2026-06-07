# Planning: Slicing Template Frontend (Modernize) ke Vue 3

Dokumen ini berisi panduan teknis dan langkah-langkah untuk melakukan proses *slicing* template HTML **Modernize** yang berada di direktori `template_frontend/` ke dalam project Vue 3 (`frontend/`).

Dokumen ini ditulis agar dapat dieksekusi langsung oleh *programmer* atau AI agent.

---

## 1. Analisis Struktur Template
Sistem akan menggunakan template **Modernize**. Berdasarkan struktur di `template_frontend/`:
- **Assets Template:** Berada di `template_frontend/package/dist/` (terdiri dari folder `css`, `js`, `images`, dan `libs`).
- **File HTML Utama (Dashboard):** Berada di `template_frontend/package/html/main/` (misalnya `index.html` untuk dashboard, `authentication-login.html` untuk halaman auth).

## 2. Manajemen Assets (CSS, JS, Images, Libs)
- **Penghapusan Assets Lama:**
  - Hapus seluruh isi default atau bawaan dari folder `frontend/src/assets/` (misal: logo vue, base.css) dan `frontend/public/` (jika ada) agar tidak memberatkan disk space sebelum memasukkan assets baru.
- **Tujuan Pindahan:**
  - Salin seluruh folder `libs`, `images`, dan file `js` pendukung dari `template_frontend/package/dist/` ke dalam folder `frontend/public/` (misal: `frontend/public/assets/`).
  - Salin file CSS utama (seperti `style.min.css`) ke dalam `frontend/src/assets/css/` atau tetap letakkan di `public/assets/css/` jika tidak perlu diproses ulang oleh Vite.
- **Konfigurasi Global (`index.html`):** Update file `frontend/index.html` untuk memuat CSS dan JS global dari template (seperti bootstrap, app.min.js, dll). Pastikan path *resource* sudah sesuai (menggunakan `/assets/...`).

## 3. Pembuatan Layouts (Vue 3)
Pisahkan struktur dasar HTML menjadi layout Vue yang *reusable* di dalam folder `frontend/src/layouts/`:

### 3.1. AuthLayout.vue
- **Tujuan:** Digunakan untuk halaman Login, Register, Forgot Password.
- **Referensi File:** `template_frontend/package/html/main/authentication-login.html`.
- **Implementasi:** Hanya memuat wrapper dasar (seperti card form di tengah layar) tanpa *sidebar* atau *navbar* kompleks. Menempatkan `<router-view />` untuk merender komponen auth.

### 3.2. MainLayout.vue
- **Tujuan:** Digunakan untuk halaman Dashboard dan halaman dalam sistem lainnya yang membutuhkan navigasi.
- **Referensi File:** `template_frontend/package/html/main/index.html`.
- **Komponen Slicing di MainLayout:**
  - **`Sidebar.vue`**: Menu navigasi sebelah kiri. Pastikan script menu (seperti metismenu/simplebar) berjalan atau disesuaikan dengan Vue.
  - **`Header.vue`**: Topbar yang memuat profil user, tombol hamburger untuk toggle sidebar, dan notifikasi.
  - **`<router-view />`**: Tempat me-render konten halaman dinamis di area utama.

## 4. Slicing Halaman (Views)
- **Halaman Login (`frontend/src/views/Login.vue`):**
  - Gunakan *class* dan struktur form dari `authentication-login.html`.
  - Hubungkan *input* form dengan state Vue (`v-model`).
- **Halaman Dashboard (`frontend/src/views/Dashboard.vue`):**
  - Ambil bagian struktur konten utama (widget, grafik, tabel) dari `index.html`.
  - Pastikan fungsionalitas UI bawaan template (seperti *dropdown*, *offcanvas*) tetap berfungsi dengan baik.

## 5. Checklist Eksekusi Slicing
- [ ] Hapus seluruh assets lama di folder `frontend/src/assets/` dan `frontend/public/` untuk menghemat kapasitas disk.
- [ ] Copy assets (CSS, JS, Images, Libs) dari `template_frontend/package/dist/` ke `frontend/public/assets/`.
- [ ] Sesuaikan path *import/link* (CSS & JS) pada `frontend/index.html`.
- [ ] Buat layout `AuthLayout.vue` dari struktur `authentication-login.html`.
- [ ] Buat layout `MainLayout.vue` lengkap dengan komponen `Sidebar.vue` dan `Header.vue` berdasarkan `index.html`.
- [ ] Update konfigurasi Vue Router (`frontend/src/router/index.js` atau sejenisnya) agar menggunakan `MainLayout` dan `AuthLayout` sesuai halamannya.
- [ ] Buat dan sesuaikan struktur UI di `Login.vue`.
- [ ] Buat dan sesuaikan struktur UI di `Dashboard.vue`.
- [ ] Lakukan verifikasi manual dengan menjalankan server frontend (`npm run dev`) dan pastikan tampilan responsif serta styling berjalan sesuai template aslinya.
