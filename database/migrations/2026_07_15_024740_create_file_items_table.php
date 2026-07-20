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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            $table->foreignId('folder_id')->nullable()->constrained('folders')->cascadeOnDelete(); 
            $table->string('name'); 
            $table->string('file_path'); 
            $table->string('mime_type')->nullable(); 
            $table->unsignedBigInteger('size')->default(0); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_items');
    }
};
