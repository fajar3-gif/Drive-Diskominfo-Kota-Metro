<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FileItem;

class FileTrashController extends Controller
{
    public function deleteFile($id)
    {
        $file = FileItem::where('user_id', Auth::id())->findOrFail($id);
        $file->delete();
        return back()->with('success', 'File berhasil dipindahkan ke sampah.');
    }

    public function restoreFile($id)
    {
        $file = FileItem::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $file->restore();
        return back()->with('success', 'File berhasil dipulihkan.');
    }

    public function forceDeleteFile($id)
    {
        $file = FileItem::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $path = storage_path('app/private/' . $file->file_path);

        if (file_exists($path)) {
            unlink($path);
        }

        $file->forceDelete();
        return back()->with('success', 'File dihapus permanen.');
    }
}
