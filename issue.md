# Planning Document: Pembuatan CRUD ms_provinsi

Dokumen ini adalah panduan dan spesifikasi teknis untuk *programmer* atau AI Model dalam mengimplementasikan fitur CRUD untuk tabel `ms_provinsi`. Mohon ikuti seluruh panduan ini secara presisi dan terstruktur.

---

## 1. Spesifikasi Database & Audit Trail
- **Tabel:** `ms_provinsi`
- **Primary Key:** HARUS menggunakan **UUID v7** (atau versi terbaru yang optimal untuk *time-based sorting*).
- **Tipe Data:** Pastikan tipe data pada skema backend/database sesuai dengan kebutuhan (contoh: string untuk nama/kode, boolean/integer untuk status aktif).
- **Audit Trail:** Sistem WAJIB mengimplementasikan pencatatan *audit trail* dengan benar untuk setiap aktivitas. Kolom standar yang harus ada dan otomatis terisi saat beroperasi:
  - `created_at`, `created_by`
  - `updated_at`, `updated_by`
  - `deleted_at`, `deleted_by` (Gunakan *Soft Delete* agar jejak data tidak hilang permanen).

## 2. Spesifikasi API (Backend)
Berdasarkan ketentuan rancangan arsitektur, seluruh aksi CRUD untuk modul ini **HARUS menggunakan metode POST** (pendekatan menyerupai RPC). Jangan gunakan GET, PUT, PATCH, atau DELETE.

- **Endpoints yang diperlukan:**
  - `POST /api/provinsi/list`  
    **Fungsi:** Mengambil daftar data provinsi.  
    **Syarat:** Menerima parameter di *body* untuk `page`, `limit`, dan `search` (wajib mendukung *Server-Side Pagination*).
  - `POST /api/provinsi/detail`  
    **Fungsi:** Mengambil data tunggal berdasarkan UUID.
  - `POST /api/provinsi/create`  
    **Fungsi:** Menyimpan data baru provinsi ke database.
  - `POST /api/provinsi/update`  
    **Fungsi:** Mengubah data provinsi yang sudah ada.
  - `POST /api/provinsi/delete`  
    **Fungsi:** Menghapus data secara logis (*Soft Delete*).

## 3. Spesifikasi Frontend (Vue 3)
- **DataTable Server-Side:** 
  Daftar data provinsi di frontend harus di-render menggunakan *DataTable* yang mendukung **Server-Side Pagination** atau **Lazy Load**. Data tidak boleh di-load sekaligus semua (*client-side*), melainkan dipanggil per halaman (page) dengan hit API `POST /api/provinsi/list` setiap berpindah halaman atau melakukan pencarian.
- **Lokasi File:** 
  Buat komponen di `frontend/src/views/master/Provinsi/` (contoh: `ProvinsiList.vue`).
- **Sidebar Menu:**
  Pastikan sidebar navigasi (di file `frontend/src/components/Sidebar.vue`) memiliki entri dinamis berikut:
  - **Header Menu:** "Data"
  - **Menu Anak:** "Provinsi" (link ke halaman tabel Provinsi).
  - Skema menu ini harus siap untuk dihubungkan dengan logic **Hak Akses** masing-masing user nantinya.

## 4. Checklist Instruksi Implementasi
Instruksi untuk AI / Programmer:
- [ ] Buat file *Migration* (atau *Schema Definition*) untuk `ms_provinsi` dengan `id` ber-tipe UUID v7 dan kolom audit trail lengkap.
- [ ] Bangun logic/Controller Backend untuk kelima API `ms_provinsi` menggunakan metode `POST`.
- [ ] Pastikan seluruh aksi menyertakan validasi *Audit Trail* yang otomatis melacak user yang bersangkutan.
- [ ] Di Frontend, konfigurasi routing untuk path `/provinsi`.
- [ ] Buat komponen Vue (`ProvinsiList.vue` dan kelengkapannya) untuk tabel *server-side pagination* dan form Create/Update.
- [ ] Pastikan menu "Provinsi" di bawah header "Data" ter-render di `Sidebar.vue` dan dapat diakses.
- [ ] Lakukan testing *End-to-End* dari sisi *client* hingga ke data yang masuk di tabel.
