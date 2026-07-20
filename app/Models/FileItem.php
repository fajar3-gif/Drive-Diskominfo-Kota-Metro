<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'folder_id', 'name', 'file_path', 'mime_type', 'size'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}