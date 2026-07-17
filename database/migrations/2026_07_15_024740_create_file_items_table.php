<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Pemilik file
            $table->foreignId('folder_id')->nullable()->constrained('folders')->cascadeOnDelete(); // File ditaruh di folder mana
            $table->string('name'); // Nama asli file (contoh: Laporan_KKN.pdf)
            $table->string('file_path'); // Alamat penyimpanan fisik di server
            $table->string('mime_type')->nullable(); // Tipe ekstensi file
            $table->unsignedBigInteger('size')->default(0); // Ukuran file (bytes)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_items');
    }
};
