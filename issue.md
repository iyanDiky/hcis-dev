# Perencanaan (Planning) Pembuatan CRUD Tabel `sdm_data`

Dokumen ini berisi panduan teknis langkah demi langkah untuk melakukan implementasi fitur **CRUD (Create, Read, Update, Delete)** untuk tabel `sdm_data`. Panduan ini dirancang agar mudah diikuti oleh programmer maupun AI/model bahasa (LLM) untuk eksekusi kode.

---

## 1. Persiapan Database & Model

Tabel `sdm_data` sudah ada di database beserta migrasinya. Berikut adalah struktur utama yang perlu diperhatikan:
- **Field Unik (Unique)**: `email`, `nik`, dan `nomor_telp`.
- **Foreign Keys**: `kota_kab_ktp` dan `kota_kab_domisili` berelasi ke tabel `ms_kota_kab`.

### Langkah Pembuatan Model `SdmData`
1. Buat file model di `backend/app/Models/SdmData.php`.
2. Gunakan *trait* yang sesuai dengan standar aplikasi: `HasUuids`, `SoftDeletes`, dan `AuditTrail`.
3. Definisikan tabel: `protected $table = 'sdm_data';`
4. Sesuaikan konstanta soft delete: `public const DELETED_AT = 'delete_at';`
5. Atur `$fillable` agar menampung seluruh kolom isian: `email`, `nik`, `nama`, `jk`, `tempat_lahir`, `tanggal_lahir`, `agama`, `gol_darah`, `status_pernikahan`, `foto`, `spesimen_tanda_tangan`, `spesimen_paraf`, `npwp`, `nomor_telp`, `alamat_ktp`, `kota_kab_ktp`, `alamat_domisili`, `kota_kab_domisili`.
6. Buat relasi (*belongsTo*) ke `MsKotaKab` untuk kedua field *foreign key* tersebut. (misal function `kotaKtpRel()` dan `kotaDomisiliRel()`).

---

## 2. Pembuatan Backend API (Controller & Routes)

### Langkah Pembuatan Controller
1. Buat `backend/app/Http/Controllers/SdmDataController.php`.
2. Implementasikan 5 endpoint utama dengan metode `POST` layaknya standar RPC:
   - `list`: Mengambil data list lengkap dengan pagination, searching (`ilike`), dan pengurutan (sorting) `ORDER BY LOWER(nama)`. Sertakan relasi (`with`) ke tabel kota/kabupaten.
   - `detail`: Mengambil 1 baris spesifik menggunakan `id`.
   - `create`: Menerima JSON *payload* dan melakukan validasi:
     - `email`: `required|email|unique:sdm_data,email`
     - `nik`: `required|string|max:16|unique:sdm_data,nik`
     - `nomor_telp`: `required|string|max:15|unique:sdm_data,nomor_telp`
     - Kolom wajib lainnya sesuai struktur database.
   - `update`: Menerima `id` dan melakukan *update* data. Pastikan validasi *unique* mengabaikan ID saat ini (contoh: `unique:sdm_data,email,` . $request->id).
   - `delete`: Melakukan pencarian berdasarkan `id` lalu melakukan *soft delete* (dan isi field `delete_by` menggunakan user yang sedang *login* atau `'system'`).

### Langkah Registrasi Route
1. Buka file `backend/routes/api.php`.
2. Di dalam *group* middleware `auth:sanctum`, tambahkan definisi routes:
   - `Route::post('/sdm-data/list', [\App\Http\Controllers\SdmDataController::class, 'list']);`
   - `Route::post('/sdm-data/detail', [\App\Http\Controllers\SdmDataController::class, 'detail']);`
   - `Route::post('/sdm-data/create', [\App\Http\Controllers\SdmDataController::class, 'create']);`
   - `Route::post('/sdm-data/update', [\App\Http\Controllers\SdmDataController::class, 'update']);`
   - `Route::post('/sdm-data/delete', [\App\Http\Controllers\SdmDataController::class, 'delete']);`

---

## 3. Pembuatan Frontend (Vue)

### Pembuatan Component Vue
1. Buat folder `frontend/src/views/sdm/SdmData`.
2. Buat file `SdmDataList.vue` di dalam folder tersebut.
3. Konstruksi komponen ini sangat mirip dengan `MsProvinsi` atau `MsKotaKab`. Komponen ini harus memiliki:
   - **Tabel Data**: Datatables interaktif dengan fitur *search debounce*, *pagination*, dan *sorting*. 
   - **Tampilkan kolom utama di tabel**: Nama, NIK, Email, No. Telp, Jenis Kelamin (sebagai perwakilan saja agar tabel tidak terlalu lebar). Tombol Aksi (Edit, Hapus) di kolom terakhir.
   - **Modal Form Add/Edit**: Modal statis dari Bootstrap yang memuat *form input*. Karena fieldnya cukup banyak, gunakan layout **Grid System (col-md-6)** agar form tidak memanjang sangat panjang ke bawah.
   - Gunakan `SweetAlert2` untuk konfirmasi hapus data dan notifikasi sukses.

### Ketentuan Field Form di Vue
- **Text Input biasa**: `email`, `nik`, `nama`, `tempat_lahir`, `npwp`, `nomor_telp`, dll.
- **Date Picker**: `tanggal_lahir` (gunakan tipe input `date`).
- **Select Dropdown (Statis)**: 
  - `jk`: L (Laki-laki), P (Perempuan).
  - `agama`: Islam, Kristen, Katolik, dll.
  - `gol_darah`: A, B, AB, O.
  - `status_pernikahan`: B (Belum Menikah), M (Menikah), P (Pernah Menikah).
- **Select Dropdown (Dinamis dari API API kota)**: `kota_kab_ktp` dan `kota_kab_domisili`.
  - Pastikan untuk mengambil *list* seluruh kota menggunakan endpoint `/kota/all` dari `MsKotaKabController` (buat endpoint `/all` jika belum tersedia, atau manfaatkan `/kota/list` dengan limit besar).
  - Integrasikan library Select2 untuk mempercantik pencarian dropdown kota.
- **Textarea**: `alamat_ktp` dan `alamat_domisili`.
- **Upload File**: Untuk `foto`, `spesimen_tanda_tangan`, `spesimen_paraf`, jika belum ada instruksi untuk *storage handling* file, gunakan input `text` biasa untuk saat ini, atau siapkan tempat yang bisa diganti input file nantinya. (Direkomendasikan menggunakan input string URL teks sementara atau konfirmasi lebih lanjut untuk penggunaan `Storage`).

### Integrasi Router dan Sidebar
1. Buka file `frontend/src/router/index.js`.
2. Daftarkan route baru di bawah *children route* utama (`/`):
   ```javascript
   {
       path: '/sdm-data',
       name: 'SdmData',
       component: () => import('../views/sdm/SdmData/SdmDataList.vue')
   }
   ```
3. Buka file `frontend/src/components/Sidebar.vue`.
4. Tambahkan *sub-menu* `Data Pribadi` (atau Data SDM) di bawah menu induk **SDM** (yang ada di dalam grup **DATA**). Pastikan hirarkinya sejajar dengan menu **Jenis SDM** (sdm_jenis) yang sudah dibuat sebelumnya.

---

## 4. Langkah Pengujian (Testing)

Setelah diimplementasikan, segera jalankan pengetesan:
1. Akses halaman melalui navigasi sidebar.
2. Buat data baru dan pastikan form modal berfungsi penuh.
3. Cobalah memasukkan `email`, `nik`, atau `nomor_telp` yang sudah ada untuk menguji bahwa respon gagal 422 *(Unprocessable Entity)* berjalan dengan benar, dan tangkap pesan error dari API untuk memunculkan di frontend via SweetAlert/Toaster.
4. Pastikan pencarian kota di dalam *Select2* berfungsi.
5. Coba perbarui data (*Edit*) dan hapus data (*Delete*) untuk memverifikasi SoftDelete dan AuditTrail.
