<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'parent_id'];

    // Relasi ke pemilik folder
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke sub-folder
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    // Relasi ke file-file di dalam folder ini
    public function fileItems()
    {
        return $this->hasMany(FileItem::class);
    }
}