<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\FileItem;
use Illuminate\Support\Facades\Auth;

class DriveController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->telusuri;
        $folderQuery = \App\Models\Folder::where('user_id', Auth::id())->whereNull('parent_id');
        $fileQuery = \App\Models\FileItem::where('user_id', Auth::id())->whereNull('folder_id');

        if ($keyword) {
            $folderQuery->where('name', 'like', '%' . $keyword . '%');
            $fileQuery->where('name', 'like', '%' . $keyword . '%');
        }

        $folders = $folderQuery->get();
        $files = $fileQuery->get();

        return view('dashboard', compact('folders', 'files'));
    }
    
    public function storeFolder(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255'
    ]);

    Folder::create([
        'name' => $request->name,
        'user_id' => Auth::id(),
        'parent_id' => $request->parent_id ?? null
    ]);

    return back()->with('success', 'Folder berhasil dibuat!');
}
    public function storeFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', 
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $path = $file->storeAs('public/files', $fileName);
        \App\Models\FileItem::create([
            'name' => $fileName,
            'file_path' => $path,
            'user_id' => Auth::id(),
            'folder_id' => $request->folder_id ?? null
        ]);

        return back();
    }
    public function updateFolder(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $folder = \App\Models\Folder::where('user_id', Auth::id())->findOrFail($id);
        $folder->update([
            'name' => $request->name
        ]);
        return back();
    }
    public function showFile($id)
    {
        $file = \App\Models\FileItem::where('user_id', Auth::id())->findOrFail($id);
        $path = storage_path('app/private/' . $file->file_path);
        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan di server.');
        }
        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
        ]);
    }

    public function showFolder($id)
    {
        $folder = \App\Models\Folder::where('user_id', Auth::id())->findOrFail($id);

        $folders = \App\Models\Folder::where('user_id', Auth::id())
            ->where('parent_id', $folder->id)
            ->get();

        $files = \App\Models\FileItem::where('user_id', Auth::id())
            ->where('folder_id', $folder->id)
            ->get();
        return view('folder', compact('folder', 'folders', 'files'));
    }

    public function deleteFolder($id)
    {
        $folder = \App\Models\Folder::where('user_id', Auth::id())->findOrFail($id);
        $folder->delete();
        return back()->with('success', 'Folder berhasil dipindahkan ke sampah.');
    }

    public function deleteFile($id)
    {
        $file = \App\Models\FileItem::where('user_id', Auth::id())->findOrFail($id);
        $file->delete(); 
        return back()->with('success', 'File berhasil dipindahkan ke sampah.');
    }

    public function downloadFile($id)
    {
        $file = \App\Models\FileItem::where('user_id', Auth::id())->findOrFail($id);
        $path = storage_path('app/private/' . $file->file_path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        return response()->download($path, $file->name);
    }

    public function sampah()
    {
        $folders = \App\Models\Folder::onlyTrashed()->where('user_id', Auth::id())->get();
        $files = \App\Models\FileItem::onlyTrashed()->where('user_id', Auth::id())->get();

        return view('sampah', compact('folders', 'files'));
    }

    public function restoreFolder($id)
    {
        $folder = \App\Models\Folder::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $folder->restore();
        return back()->with('success', 'Folder berhasil dipulihkan.');
    }

    public function restoreFile($id)
    {
        $file = \App\Models\FileItem::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $file->restore();
        return back()->with('success', 'File berhasil dipulihkan.');
    }

    public function forceDeleteFolder($id)
    {
        $folder = \App\Models\Folder::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $folder->forceDelete();
        return back()->with('success', 'Folder dihapus permanen.');
    }

    public function forceDeleteFile($id)
    {
        $file = \App\Models\FileItem::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $path = storage_path('app/private/' . $file->file_path);

        if (file_exists($path)) {
            unlink($path);
        }

        $file->forceDelete();
        return back()->with('success', 'File dihapus permanen.');
    }
}