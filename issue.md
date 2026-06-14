# Pembuatan CRUD untuk Master Kota/Kabupaten (ms_kota_kab)

## Deskripsi Singkat
Akan dibuat modul CRUD lengkap (API Backend + Frontend Vue) untuk master data Kota/Kabupaten (`ms_kota_kab`). Sistem ini harus mengikuti standar dan fungsionalitas yang persis sama dengan modul `ms_provinsi` yang sudah ada, dengan tambahan penyesuaian fungsionalitas Dropdown relasi ke provinsi.

## Skema Database
Berdasarkan migration yang sudah ada:
- **Tabel:** `ms_kota_kab`
- **Kolom Utama:**
  - `id` (UUIDv7, Primary Key)
  - `kota_kabupaten` (String)
  - `provinsi` (UUID, Foreign Key ke tabel `ms_provinsi`)
- **Kolom Audit Trail:** `created_at`, `created_by`, `updated_at`, `updated_by`, `delete_at`, `delete_by` (Gunakan implementasi SoftDeletes kustom pada Laravel).

## Spesifikasi Backend (Laravel API)
1. **Model (`MsKotaKab.php`):**
   - Gunakan trait `HasUuids` (disesuaikan dengan UUIDv7 via `Str::orderedUuid()`).
   - Implementasikan SoftDeletes kustom yang meng-override konstanta `DELETED_AT` menjadi `delete_at`.
   - Buat relasi `belongsTo` ke model `MsProvinsi` melalui foreign key `provinsi`.
2. **Controller (`MsKotaKabController.php`):**
   - Harus menggunakan metode **POST** untuk semua *endpoint* (metode *RPC-style*).
   - Buat endpoint berikut:
     - `POST /api/kota/list`: Untuk Datatable, dukung *pagination* JSON Body, fitur *search* (`ilike` PostgreSQL), dan fitur *sorting* secara *case-insensitive* (`ORDER BY LOWER(kolom)`). Pastikan relasi provinsi di-*eager load* (`with('provinsiRel')`).
     - `POST /api/kota/detail`: Mengambil data spesifik (beserta data relasi provinsinya).
     - `POST /api/kota/create`: Menyimpan data baru. Pastikan audit trail tersimpan otomatis dari state autentikasi (`$request->user()->name`).
     - `POST /api/kota/update`: Mengubah data *existing*. Pastikan audit trail berjalan otomatis.
     - `POST /api/kota/delete`: Melakukan *SoftDelete*, sebelumnya set field `delete_by` secara eksplisit.
     - `POST /api/provinsi/all`: Tambahkan endpoint kecil di `MsProvinsiController` (jika belum ada) untuk *dropdown* yang me-*return* seluruh provinsi tanpa paginasi (untuk Select Option).
3. **Routing (`api.php`):** 
   - Daftarkan semua *route* baru di dalam *middleware* `auth:sanctum`.

## Spesifikasi Frontend (Vue 3)
1. **Komponen Daftar Kota/Kabupaten (`KotaList.vue`):**
   - **Tabel Server-side:** Menggunakan UI HTML Datatable bawaan template (`table border table-striped table-bordered text-nowrap`).
   - **Fitur Tabel:** *Pagination*, fitur urutkan (*Sort*), dan fitur *Search Real-time* (menggunakan `@input` dengan *debounce* 500ms).
   - **Kolom Tabel:** Tampilkan kolom "Kota/Kabupaten", "Provinsi", dan "Aksi" (Tombol Edit dan Hapus).
2. **Fitur Tambah/Edit (Menggunakan Bootstrap Modal):**
   - Form *Create* dan *Update* diletakkan di dalam Bootstrap Modal berukuran *medium* dengan efek *static backdrop*.
   - **Form Fields:** 
     - Input teks untuk "Nama Kota/Kabupaten".
     - `<select>` (Dropdown) untuk "Provinsi", ambil datanya dari *endpoint* API provinsi *all* secara dinamis saat komponen di-*mount*.
3. **Konfirmasi Penghapusan (SweetAlert2):**
   - Tombol Hapus harus *trigger* dialog konfirmasi dari `SweetAlert2` (*Confirm Dialog* dengan tombol merah dan peringatan tekstual jelas).
4. **Notifikasi Sukses (SweetAlert2):**
   - Munculkan *popup* centang hijau otomatis selama 1.5 detik jika Tambah, Edit, atau Hapus berhasil.
5. **Sidebar Navigation (`Sidebar.vue`):**
   - Pastikan rute baru (contoh: `/kota`) ditambahkan di daftar `menuItems` di bawah sub-menu **"Master"**.
   - Logic *active-class* dan *auto-open accordion* sudah siap, hanya perlu tambahkan objek *route*-nya.

## Catatan Tambahan Bagi Developer
- Selalu tiru persis struktur, pola balikan JSON, dan nama fungsi yang ada di `MsProvinsiController.php` dan `ProvinsiList.vue`.
- Konsisten menggunakan Axios dengan autentikasi *Bearer token*.
- Perhatikan relasi Foreign Key saat merender data nama Provinsi di Frontend. Pastikan Backend merespon nama provinsinya, bukan sekadar ID provinsinya.
