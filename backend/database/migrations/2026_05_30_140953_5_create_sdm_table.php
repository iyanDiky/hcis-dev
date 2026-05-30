<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sdm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sdm_data')->nullable()->constrained('sdm_data')->onDelete('cascade');
            $table->foreignUuid('jenis')->nullable()->constrained('sdm_jenis')->onDelete('set null');
            $table->string('nomor_rekening')->nullable();
            
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
        Schema::dropIfExists('sdm');
    }
};
