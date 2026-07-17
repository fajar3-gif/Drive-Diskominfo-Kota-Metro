<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Wajib: agar tahu siapa pemilik foldernya
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('folders')->cascadeOnDelete(); // Untuk fitur sub-folder
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
