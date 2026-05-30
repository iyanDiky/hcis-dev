<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sdm_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('nik', 16)->unique();
            $table->string('nama');
            $table->string('jk', 1);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama');
            $table->string('gol_darah', 2)->nullable();
            $table->string('status_pernikahan', 1);
            $table->string('foto')->nullable();
            $table->string('spesimen_tanda_tangan')->nullable();
            $table->string('spesimen_paraf')->nullable();
            $table->string('npwp')->nullable();
            $table->string('nomor_telp', 15)->unique(); // made 15 just in case
            $table->text('alamat_ktp');
            $table->foreignUuid('kota_kab_ktp')->nullable()->constrained('ms_kota_kab')->onDelete('set null');
            $table->text('alamat_domisili');
            $table->foreignUuid('kota_kab_domisili')->nullable()->constrained('ms_kota_kab')->onDelete('set null');
            
            // Audit Trail
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 64)->nullable();
            $table->timestamp('updated_at')->useCurrent();
            $table->string('updated_by', 64)->nullable();
            $table->dateTime('delete_at')->nullable();
            $table->string('delete_by', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sdm_data');
    }
};
