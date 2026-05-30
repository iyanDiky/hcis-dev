# Planning: Penyesuaian Struktur Database untuk Autentikasi HCIS

Dokumen ini berisi panduan teknis langkah demi langkah untuk melakukan penyesuaian skema database pada proyek HCIS. Penyesuaian ini bertujuan untuk mengakomodasi kebutuhan autentikasi dan relasi data SDM yang baru, dengan tetap mempertahankan tabel inti bawaan Laravel (`sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`).

**Harap dibaca dengan teliti sebelum mulai melakukan implementasi koding (coding).**

---

## 1. Urutan Pembuatan/Penyesuaian Migrasi
Karena ada relasi *Foreign Key* antar tabel, migrasi harus dibuat dan dijalankan dengan urutan yang benar (tabel master/referensi dibuat lebih dulu). Semua *Primary Key* menggunakan tipe `UUID` (`varchar(36)`).

**Urutan Migrasi:**
1. `ms_provinsi`
2. `ms_kota_kab`
3. `sdm_jenis`
4. `sdm_data`
5. `sdm`
6. `users` (Update tabel bawaan Laravel)
7. `password_reset` (Update/Rename tabel bawaan Laravel `password_reset_tokens`)

---

## 2. Struktur Tabel & Spesifikasi Skema

Semua tabel di bawah ini (kecuali tabel inti sistem) wajib memiliki kolom **Audit Trail**:
```php
$table->timestamp('created_at')->useCurrent();
$table->string('created_by', 64)->nullable(); // format: id_sdm-bagian_seksi-unit_kerja
$table->timestamp('updated_at')->useCurrent();
$table->string('updated_by', 64)->nullable();
$table->dateTime('delete_at')->nullable();    // Custom soft delete
$table->string('delete_by', 64)->nullable();
```
*(Tips implementasi: Buat trait khusus, misalnya `AuditTrailTrait`, yang secara otomatis mengisi kolom `created_by`, `updated_by`, dan `delete_by` pada model events `creating`, `updating`, dan `deleting`.)*

### 2.1. Tabel `ms_provinsi`
Tabel master provinsi.
- `id` : `uuid` (Primary Key)
- `provinsi` : `string` (varchar 255)
- + Kolom Audit Trail

### 2.2. Tabel `ms_kota_kab`
Tabel master kota/kabupaten.
- `id` : `uuid` (Primary Key)
- `kota_kabupaten` : `string` (varchar 255)
- `provinsi` : `uuid` (Foreign Key ke `ms_provinsi.id`)
- + Kolom Audit Trail

### 2.3. Tabel `sdm_jenis`
Tabel master jenis SDM (contoh: pegawai, bakat, tenaga alih daya).
- `id` : `uuid` (Primary Key)
- `jenis` : `string` (varchar 255)
- + Kolom Audit Trail

### 2.4. Tabel `sdm_data`
Tabel yang menyimpan data personal / demografi SDM.
- `id` : `uuid` (Primary Key)
- `email` : `string` (varchar 255, unik)
- `nik` : `string` (varchar 16, unik)
- `nama` : `string` (varchar 255)
- `jk` : `string` (varchar 1)
- `tempat_lahir` : `string` (varchar 255)
- `tanggal_lahir` : `date`
- `agama` : `string` (varchar 255)
- `gol_darah` : `string` (varchar 2)
- `status_pernikahan` : `string` (varchar 1)
- `foto` : `string` (varchar 255, nullable)
- `spesimen_tanda_tangan` : `string` (varchar 255, nullable)
- `spesimen_paraf` : `string` (varchar 255, nullable)
- `npwp` : `string` (varchar 255, nullable)
- `nomor_telp` : `string` (varchar 13, unik)
- `alamat_ktp` : `text`
- `kota_kab_ktp` : `uuid` (Foreign Key ke `ms_kota_kab.id`)
- `alamat_domisili` : `text`
- `kota_kab_domisili` : `uuid` (Foreign Key ke `ms_kota_kab.id`)
- + Kolom Audit Trail

### 2.5. Tabel `sdm`
Tabel utama entitas SDM yang terhubung dengan `sdm_data` dan tipe SDM.
- `id` : `uuid` (Primary Key)
- `sdm_data` : `uuid` (Foreign Key ke `sdm_data.id`)
- `jenis` : `uuid` (Foreign Key ke `sdm_jenis.id`)
- `nomor_rekening` : `string` (varchar 255)
- + Kolom Audit Trail

### 2.6. Tabel `users`
Penyesuaian tabel bawaan Laravel. Ubah dari skema bawaan menjadi berikut:
- `id` : `uuid` (Primary Key)
- `sdm` : `uuid` (Foreign Key ke `sdm.id`)
- `username` : `string` (varchar 10, unik)
- `password` : `string` (varchar 255)
- `password_expired_at` : `datetime` (nullable)
- `status` : `integer` (panjang 2, default 1, value: 0=non-aktif, 1=aktif)
- `error_login` : `integer` (panjang 1, default 0)
- + Kolom Audit Trail

### 2.7. Tabel `password_reset`
Modifikasi file migrasi `password_reset_tokens` milik Laravel. Ubah nama tabel menjadi `password_reset` dan ubah skemanya:
- `id` : `uuid` (Primary Key)
- `user` : `uuid` (Foreign Key ke `users.id`)
- `token` : `string` (varchar 255)
- + Kolom Audit Trail
*(Hapus atau sesuaikan tabel bawaan Laravel `password_reset_tokens` di dalam file `database/migrations/0001_01_01_000000_create_users_table.php`)*

---

## 3. Tugas Implementasi (Model & Autentikasi)

### 3.1. Penyesuaian Model
1. Buat model untuk masing-masing tabel: `Provinsi`, `KotaKab`, `SdmJenis`, `SdmData`, `Sdm`, dan sesuaikan `User`.
2. Gunakan trait `Illuminate\Database\Eloquent\Concerns\HasUuids` pada semua model di atas karena PK menggunakan `UUID`.
3. Matikan timestamps default (`public $timestamps = false;`) jika Anda mengontrol `created_at` dan `updated_at` melalui sistem custom audit trail, ATAU buat trait custom *SoftDeletes* untuk menangani kolom `delete_at`. Laravel secara default menggunakan `deleted_at`. Untuk mengakomodir kolom bernama `delete_at`, bisa menambahkan `const DELETED_AT = 'delete_at';` di dalam Model.
4. Tentukan relasi Eloquent:
   - `User` belongsTo `Sdm` (`'sdm'`)
   - `Sdm` belongsTo `SdmData` (`'sdm_data'`) dan `SdmJenis` (`'jenis'`)
   - `SdmData` belongsTo `KotaKab` (`'kota_kab_ktp'`) dan (`'kota_kab_domisili'`)
   - `KotaKab` belongsTo `Provinsi` (`'provinsi'`)

### 3.2. Penyesuaian Fitur Auth Controller
1. Update `AuthController` atau service login yang ada.
2. Login yang sebelumnya menggunakan `email`, ubah konfigurasinya agar validasi dan query menggunakan kolom `username`.
3. Tambahkan pengecekan `status == 1` saat login (Hanya akun aktif yang bisa login).
4. Buat mekanisme perhitungan `error_login` ketika gagal login.
5. Saat authentikasi berhasil, reset `error_login` kembali ke `0`.
6. Load relasi `sdm.sdm_data` saat mengembalikan respons API login untuk menampilkan profil user di Frontend.

---

## 4. Instruksi untuk Programmer / AI Agent

**Task List untuk di eksekusi:**
- [ ] Ubah struktur migrasi utama Laravel (pada `database/migrations/0001_01_01_000000_create_users_table.php`). Ganti tabel `users` dan hapus/ubah `password_reset_tokens`.
- [ ] Buat file migrasi baru untuk `ms_provinsi`, `ms_kota_kab`, `sdm_jenis`, `sdm_data`, `sdm`, dan `password_reset` (jika dipisah).
- [ ] Buat *Trait* untuk `AuditTrail` untuk mengisi `created_by`, `updated_by`, dan `delete_by`.
- [ ] Implementasikan Models beserta definisi *relationships* (`belongsTo`, `hasMany`).
- [ ] Sesuaikan config/auth.php jika diperlukan dan buat ulang logika `login` pada Controller.
- [ ] Jalankan `php artisan migrate:fresh` (pastikan DB reset dengan struktur baru berhasil tanpa conflict foreign key).
