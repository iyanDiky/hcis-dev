# Planning: Seeding Database HCIS

Dokumen ini berisi panduan teknis langkah demi langkah untuk melakukan proses *seeding* awal pada database aplikasi HCIS. *Seeding* ini bertujuan untuk mengisi tabel master dan menyiapkan akun Super Admin agar aplikasi dapat langsung digunakan.

**Harap dibaca dengan teliti sebelum mulai melakukan koding (coding).**

---

## 1. Daftar Seeder yang Perlu Dibuat

Buat file seeder terpisah agar rapi, lalu panggil semua di dalam `DatabaseSeeder.php` menggunakan `$this->call([...])`.
Urutan eksekusi seeder harus diatur dengan benar agar tidak terjadi *Foreign Key Constraint Violation*:
1. `SdmJenisSeeder`
2. `WilayahSeeder` (untuk Provinsi & Kota/Kab)
3. `AdminUserSeeder` (untuk SDM Data, SDM, dan Users)

---

## 2. Detail Data Seeding

### 2.1. `SdmJenisSeeder`
Tabel: `sdm_jenis`
Isi tabel ini dengan jenis SDM berikut. Pastikan untuk meng-generate UUID untuk masing-masing baris.
- `Admin`
- `Pegawai`
- `Pengurus`
- `Bakat`

### 2.2. `WilayahSeeder` (Provinsi dan Kota/Kab)
Tabel: `ms_provinsi` & `ms_kota_kab`
Sumber Data: File Excel `database_provinsi_kabkota_indonesia.xlsx` di *root* proyek.

**Langkah Implementasi:**
1. Karena file Excel tersebut tampaknya berformat teks *comma-separated* yang disatukan di kolom pertama (berdasarkan pengecekan struktur: `kode_provinsi,nama_provinsi,kode_kabupaten_kota,nama_kabupaten_kota`), kamu mungkin perlu membacanya baris per baris lalu memecahnya (split/explode) menggunakan koma `,`.
2. Gunakan `kode_provinsi` untuk mengecek apakah `ms_provinsi` dengan kode/nama tersebut sudah dibuat (jangan buat duplikat). Simpan id `uuid` provinsi yang baru dibuat ke dalam variabel / array memori.
3. Gunakan relasi `uuid` provinsi tersebut untuk membuat data di `ms_kota_kab` (berdasarkan `kode_kabupaten_kota` dan `nama_kabupaten_kota`).

### 2.3. `AdminUserSeeder`
Tabel: `sdm_data`, `sdm`, dan `users`
Tujuan: Membuat Super User Admin aplikasi.

**Langkah Implementasi:**
1. Ambil `id` dari `sdm_jenis` dengan `jenis = 'Admin'`.
2. Buat data dummy di `sdm_data`:
   - `email`: `hcis@bankkalsel.co.id`
   - `nik`: (isikan angka unik 16 digit, misal `1234567890123456`)
   - `nama`: `Super Admin HCIS`
   - `jk`: `L`
   - `tempat_lahir`: `Banjarmasin`
   - `tanggal_lahir`: `1990-01-01`
   - `agama`: `Islam`
   - `status_pernikahan`: `B`
   - `nomor_telp`: `081234567890`
   - `alamat_ktp`: `Jl. Lambung Mangkurat`
   - `alamat_domisili`: `Jl. Lambung Mangkurat`
3. Buat data di `sdm`:
   - `sdm_data`: (UUID dari langkah 2)
   - `jenis`: (UUID dari langkah 1)
4. Buat data di `users`:
   - `sdm`: (UUID dari langkah 3)
   - `username`: `admin`
   - `password`: `Hash::make('password')`
   - `status`: `1`
   - `error_login`: `0`

---

## 3. Penyesuaian Kolom Audit Trail

Pada struktur database yang ada, setiap tabel memiliki kolom *Audit Trail* (`created_by`, `updated_by`). Kolom ini dikendalikan secara otomatis oleh trait `AuditTrail.php`. 

Saat menjalankan seeder (CLI), session `Auth::user()` otomatis kosong, sehingga nilai `created_by` akan diisi secara default dengan string `"System"`.
Namun, untuk **data Admin**, nilai `created_by`-nya harus mencerminkan Admin itu sendiri (berhubung dia adalah Super User pengelolaan data).

**Instruksi:**
1. Di dalam `AdminUserSeeder`, setelah entitas `sdm_data`, `sdm`, dan `users` dibuat dan mendapatkan UUID, lakukan eksekusi ulang secara manual (force update).
2. *Bypass* event Model atau lakukan perintah `DB::table(...)` *query builder* secara langsung pada baris data Super Admin untuk meng-*update* kolom `created_by` dan `updated_by` menggunakan format standar: `{uuid_sdm_admin}-unknown-unknown`.
3. Biarkan sisa entitas lainnya (provinsi, kota, jenis sdm) tetap memiliki `created_by` `"System"` karena direkam saat inisialisasi awal database.

---

## 4. Instruksi untuk Programmer / AI Agent

**Task List untuk eksekusi:**
- [ ] Buat file seeder menggunakan perintah `php artisan make:seeder`.
- [ ] Tambahkan *package* pembaca Excel / CSV (seperti `maatwebsite/excel` atau `fgetcsv` murni PHP) jika diperlukan, untuk parsing file `database_provinsi_kabkota_indonesia.xlsx`.
- [ ] Tulis logika parsing provinsi & kota/kabupaten ke dalam `WilayahSeeder`. Pastikan data *unique*.
- [ ] Tulis pembuatan data statis di `SdmJenisSeeder`.
- [ ] Tulis pembuatan data *super admin* di `AdminUserSeeder`.
- [ ] Registrasikan ketiga seeder tersebut ke dalam fungsi `run()` di `DatabaseSeeder.php`.
- [ ] Uji coba dengan menjalankan perintah `php artisan db:seed`. Pastikan tidak ada *error constraint*.
