<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'name', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function fileItems()
    {
        return $this->hasMany(FileItem::class);
    }

    public function getSizeAttribute()
    {
        $size = 0;
        
        // Jika folder ini ada di tempat sampah, kita hitung juga file/subfolder yang mungkin ter-soft-delete
        // atau tersembunyi. Jika folder ini aktif, kita hanya hitung yang aktif.
        $files = $this->trashed() ? $this->fileItems()->withTrashed()->get() : $this->fileItems;
        foreach ($files as $file) {
            $size += (int) $file->size;
        }
        
        $children = $this->trashed() ? $this->children()->withTrashed()->get() : $this->children;
        foreach ($children as $child) {
            $size += (int) $child->size;
        }
        
        return $size;
    }
}