<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sdm')->nullable()->constrained('sdm')->onDelete('cascade');
            $table->string('username', 10)->unique();
            $table->string('password');
            $table->dateTime('password_expired_at')->nullable();
            $table->integer('status')->default(1);
            $table->integer('error_login')->default(0);
            
            // Audit Trail
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 64)->nullable();
            $table->timestamp('updated_at')->useCurrent();
            $table->string('updated_by', 64)->nullable();
            $table->dateTime('delete_at')->nullable();
            $table->string('delete_by', 64)->nullable();
        });

        Schema::create('password_reset', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user')->constrained('users')->onDelete('cascade');
            $table->string('token');
            
            // Audit Trail
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 64)->nullable();
            $table->timestamp('updated_at')->useCurrent();
            $table->string('updated_by', 64)->nullable();
            $table->dateTime('delete_at')->nullable();
            $table->string('delete_by', 64)->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset');
        Schema::dropIfExists('users');
    }
};
