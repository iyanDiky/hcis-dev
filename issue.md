# Pembuatan CRUD untuk Jenis SDM (sdm_jenis)

## Deskripsi Singkat
Modul ini bertujuan untuk menyediakan fitur CRUD lengkap (API Backend + Frontend Vue) untuk master data Jenis SDM (`sdm_jenis`). Berbeda dengan `ms_kota_kab`, tabel `sdm_jenis` berdiri sendiri dan tidak memiliki relasi dropdown *(foreign key)* ke tabel lain pada saat *Create* atau *Edit*. 
Struktur fungsionalitas UI (seperti pagination, search, sorting, dan sweetalert) akan 100% mengikuti standar modul `ms_provinsi`. Perbedaan utamanya ada pada tata letak *Sidebar* Menu, di mana menu ini berada di bawah *dropdown* khusus bernama **SDM**.

## Skema Database
Berdasarkan migration yang sudah ada:
- **Tabel:** `sdm_jenis`
- **Kolom Utama:**
  - `id` (UUIDv7, Primary Key)
  - `jenis` (String)
- **Kolom Audit Trail:** `created_at`, `created_by`, `updated_at`, `updated_by`, `delete_at`, `delete_by` (Menggunakan SoftDeletes kustom pada Laravel).

## Spesifikasi Backend (Laravel API)
1. **Model (`SdmJenis.php`):**
   - Gunakan trait `HasUuids` (disesuaikan dengan UUIDv7 via `Str::orderedUuid()`).
   - Implementasikan SoftDeletes kustom dengan override `const DELETED_AT = 'delete_at';`.
   - Setup field `fillable` untuk `jenis` dan audit log.
2. **Controller (`SdmJenisController.php`):**
   - Harus menggunakan metode **POST** untuk semua *endpoint* (*RPC-style*).
   - Buat endpoint berikut:
     - `POST /api/sdm-jenis/list`: Untuk Datatable dengan *pagination*, *search* (`ilike`), dan *sorting* secara *case-insensitive* (`ORDER BY LOWER(jenis)`).
     - `POST /api/sdm-jenis/detail`: Mengambil data tunggal.
     - `POST /api/sdm-jenis/create`: Menyimpan data baru dengan audit trail dari `$request->user()`.
     - `POST /api/sdm-jenis/update`: Mengubah data *existing*.
     - `POST /api/sdm-jenis/delete`: Melakukan *SoftDelete*, wajib simpan `delete_by` sebelum delete.
3. **Routing (`api.php`):** 
   - Daftarkan semua *route* `sdm-jenis` baru di dalam *middleware* `auth:sanctum`.
4. **Database Seeding (`SdmJenisSeeder.php`):**
   - Buat seeder untuk memasukkan data awal secara berurutan.
   - Data jenis SDM yang harus di-seed: "organik", "bakat", "tenaga alih daya", "pengurus", "dewan komite", "dewan pengawas syariah", "staf khusus".
   - Pastikan setiap id di-generate menggunakan `Str::orderedUuid()`.

## Spesifikasi Frontend (Vue 3)
1. **Komponen Daftar Jenis SDM (`SdmJenisList.vue`):**
   - **Tabel Server-side:** Menggunakan UI HTML Datatable bawaan template.
   - **Fitur Tabel:** *Pagination*, fitur urutkan (*Sort*), dan *Search Real-time* (debounce 500ms).
   - **Kolom Tabel:** Tampilkan kolom "Jenis SDM" dan "Aksi" (Tombol Edit dan Hapus).
2. **Fitur Tambah/Edit (Bootstrap Modal):**
   - Form menggunakan Bootstrap Modal berukuran *medium* (*static backdrop*).
   - **Form Fields:** Hanya berisi 1 (satu) input teks untuk "Jenis SDM".
3. **Konfirmasi & Notifikasi (SweetAlert2):**
   - Tombol Hapus memicu dialog konfirmasi (`warning` icon).
   - Munculkan *popup* centang hijau (otomatis tertutup dalam 1.5 detik) jika Tambah/Edit/Hapus berhasil.
4. **Sidebar Navigation (`Sidebar.vue`):**
   - Buat objek `dropdown` baru dengan judul **"SDM"** yang sejajar letaknya di bawah/setelah *dropdown* **"Master"** (tetapi tetap di bawah *cap* header **"Data"**).
   - Tambahkan menu **"Jenis SDM"** sebagai *child* dari grup **"SDM"** tersebut dengan *route path* `/sdm-jenis`.
   - Rute ini harus dikonfigurasikan juga di `router/index.js`.

## Catatan Tambahan Bagi Developer
- Selalu tiru persis pola balikan JSON dan penamaan standardisasi dari `MsProvinsiController.php`.
- Perhatikan bahwa menu navigasinya berbeda dari tugas sebelumnya. Jenis SDM tidak diletakkan di dalam *Master*, melainkan di *dropdown* baru bernama *SDM*.
