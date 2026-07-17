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
        // Menangkap kata kunci dari input pencarian yang bernama "telusuri"
        $keyword = $request->telusuri;

        // Query dasar: Ambil data milik user yang sedang login
        $folderQuery = \App\Models\Folder::where('user_id', Auth::id())->whereNull('parent_id');
        $fileQuery = \App\Models\FileItem::where('user_id', Auth::id())->whereNull('folder_id');

        // Jika form pencarian diisi, tambahkan filter LIKE ke query
        if ($keyword) {
            $folderQuery->where('name', 'like', '%' . $keyword . '%');
            $fileQuery->where('name', 'like', '%' . $keyword . '%');
        }

        // Eksekusi pengambilan data
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
        'parent_id' => null
    ]);

    return back()->with('success', 'Folder berhasil dibuat!');
}
    public function storeFile(Request $request)
    {
        // 1. Validasi file (wajib diisi, maksimal 10MB)
        $request->validate([
            'file' => 'required|file|max:10240', 
        ]);

        // 2. Ambil data file-nya
        $file = $request->file('file');
        
        // 3. Ambil nama asli file tersebut (misal: laporan.pdf)
        $fileName = $file->getClientOriginalName();
        
        // 4. Simpan file fisik ke dalam server (folder storage/app/public/files)
        $path = $file->storeAs('public/files', $fileName);

        // 5. Simpan catatan ke database menggunakan Model FileItem milikmu
        \App\Models\FileItem::create([
            'name' => $fileName,
            'file_path' => $path,
            'user_id' => Auth::id(),
            'folder_id' => null // Null karena masih diletakkan di halaman utama (root)
        ]);

        return back();
    }
    // Fungsi Manual untuk Mengubah Nama Folder
    public function updateFolder(Request $request, $id)
    {
        // 1. Validasi input nama baru
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // 2. Cari folder berdasarkan ID dan pastikan itu milik user yang sedang login
        $folder = \App\Models\Folder::where('user_id', Auth::id())->findOrFail($id);
        
        // 3. Update namanya di database
        $folder->update([
            'name' => $request->name
        ]);

        // 4. Kembalikan ke halaman dashboard
        return back();
    }
}