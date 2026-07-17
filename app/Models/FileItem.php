<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileItem extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'folder_id', 'name', 'file_path', 'mime_type', 'size'];

    // Relasi ke pemilik file
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke folder tempat file ini berada
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}