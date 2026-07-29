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
        $order = $request->get('order', 'asc');
        $type = $request->get('type', '');
        $modified = $request->get('modified', '');
        
        $folderQuery = \App\Models\Folder::where('user_id', Auth::id())->whereNull('parent_id');
        $fileQuery = \App\Models\FileItem::where('user_id', Auth::id())->whereNull('folder_id');

        if ($keyword) {
            $folderQuery->where('name', 'like', '%' . $keyword . '%');
            $fileQuery->where('name', 'like', '%' . $keyword . '%');
        }

        if ($modified) {
            if ($modified == 'today') {
                $folderQuery->whereDate('updated_at', \Carbon\Carbon::today());
                $fileQuery->whereDate('updated_at', \Carbon\Carbon::today());
            } elseif ($modified == '7days') {
                $folderQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(7));
                $fileQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(7));
            } elseif ($modified == '30days') {
                $folderQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(30));
                $fileQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(30));
            }
        }

        $folderQuery->orderBy('name', $order);
        $fileQuery->orderBy('name', $order);

        if ($type == 'folder') {
            $folders = $folderQuery->get();
            $files = collect([]);
        } elseif ($type == 'file') {
            $folders = collect([]);
            $files = $fileQuery->get();
        } else {
            $folders = $folderQuery->get();
            $files = $fileQuery->get();
        }

        return view('dashboard', compact('folders', 'files', 'order'));
    }

    public function terbaru(Request $request)
    {
        $keyword = $request->telusuri;
        $type = $request->get('type', '');
        $modified = $request->get('modified', '');
        
        $fileQuery = \App\Models\FileItem::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($keyword) {
            $fileQuery->where('name', 'like', '%' . $keyword . '%');
        }
        
        if ($modified) {
            if ($modified == 'today') {
                $fileQuery->whereDate('updated_at', \Carbon\Carbon::today());
            } elseif ($modified == '7days') {
                $fileQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(7));
            } elseif ($modified == '30days') {
                $fileQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(30));
            }
        }

        if ($type == 'folder') {
            $files = collect([]); // Terbaru tidak punya folder, jadi jika filter folder, kosongkan
        } else {
            $files = $fileQuery->get();
        }

        return view('terbaru', compact('files'));
    }
    
    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $folder = Folder::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id ?? null
        ]);

        $url = $folder->parent_id ? url('/folder/show/' . $folder->parent_id) : route('dashboard');
        
        return redirect($url)->with([
            'success' => 'Folder berhasil dibuat!',
            'new_item_id' => $folder->id,
            'new_item_type' => 'folder'
        ]);
    }

    public function storeFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file', 
        ]);

        $file = $request->file('file');

        // --- KEAMANAN: SANITASI NAMA FILE (Mencegah Path Traversal) ---
        // Hapus karakter berbahaya seperti ../, \, null byte, dll.
        $rawName = $file->getClientOriginalName();
        $fileName = basename($rawName);                          // Hapus path traversal (../)
        $fileName = preg_replace('/[^\w\s\-.]/', '', $fileName); // Hanya izinkan karakter aman
        $fileName = preg_replace('/\s+/', '_', $fileName);       // Ganti spasi dengan underscore
        if (empty($fileName)) {
            $fileName = 'file_' . time();
        }

        $clientExt = strtolower($file->getClientOriginalExtension());
        
        // --- 1. CEK KUOTA PENYIMPANAN (10 GB) ---
        $quotaBytes = 1 * 1024 * 1024 * 1024; // 1 GB
        $usedStorage = \App\Models\FileItem::where('user_id', Auth::id())->sum('size');
        
        if (($usedStorage + $file->getSize()) > $quotaBytes) {
            return back()->with('error', 'Kapasitas Penyimpanan Penuh! Batas maksimal Anda adalah 1 GB.');
        }

        // --- 2. CEK FILE KOSONG ---
        // Sesuai permintaan, file kosong tetap diizinkan diupload (bypass pengecekan anti-spoofing).
        // File kosong (0 bytes) akan otomatis ditangani (pesan error) saat mencoba dibuka.
        if ($file->getSize() > 0) {
            // --- 2. CEK DOUBLE EXTENSION (Misal: laporan.pdf.exe) ---
            // Jika nama file mengandung lebih dari satu titik dan diakhiri ekstensi berbahaya
            $segments = explode('.', $fileName);
            if (count($segments) > 2) {
                $lastExt = strtolower(end($segments));
                $secondLastExt = strtolower($segments[count($segments)-2]);
                if (in_array($lastExt, ['exe', 'php', 'sh', 'bat', 'cmd', 'ps1']) && in_array($secondLastExt, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png', 'mp4'])) {
                    return back()->with('error', 'Keamanan: Terdeteksi Double Extension (Spoofing). File ditolak.');
                }
            }

            // --- 3. PEMINDAIAN KONTEN & MAGIC NUMBER (FILE SIGNATURE) ---
            // getMimeType() di Laravel menggunakan fungsi finfo PHP yang membaca
            // struktur "Magic Number" di dalam bita pertama isi file (bukan cuma nama).
            $realMime = $file->getMimeType();

            // Catatan: Sesuai konsep Universal Web Drive, file script/program seperti .php atau .exe 
            // diizinkan selama mereka "jujur" (tidak disamarkan sebagai file lain).

            // Cek spoofing PDF (Misal ngaku PDF tapi Magic Number-nya bukan PDF)
            if ($clientExt === 'pdf' && $realMime !== 'application/pdf') {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. Magic Number file ini bukan PDF asli.');
            }

            // Cek spoofing gambar raster (JPG/PNG/GIF/WEBP)
            if (in_array($clientExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']) && !str_starts_with($realMime, 'image/')) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. Magic Number file ini bukan gambar asli.');
            }

            // Cek spoofing SVG (harus bertipe image/svg+xml)
            if ($clientExt === 'svg' && $realMime !== 'image/svg+xml') {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. File ini bukan SVG asli.');
            }

            // Cek spoofing Office BARU (.docx/.xlsx/.pptx) — Magic Number-nya ZIP
            if (in_array($clientExt, ['docx', 'xlsx', 'pptx']) && !in_array($realMime, [
                'application/zip',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ])) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. Isi file ini bukan dokumen Office (.docx/.xlsx/.pptx) asli.');
            }

            // Cek spoofing Office LAMA (.doc/.xls/.ppt) — Magic Number Compound Document (OLE)
            if ($clientExt === 'doc' && !in_array($realMime, ['application/msword', 'application/vnd.ms-word'])) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. File ini bukan dokumen Word (.doc) asli.');
            }
            if ($clientExt === 'xls' && !in_array($realMime, ['application/vnd.ms-excel', 'application/excel'])) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. File ini bukan spreadsheet Excel (.xls) asli.');
            }
            if (in_array($clientExt, ['ppt', 'pptx']) && $clientExt === 'ppt' && !in_array($realMime, ['application/vnd.ms-powerpoint', 'application/powerpoint'])) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. File ini bukan presentasi PowerPoint (.ppt) asli.');
            }

            // Cek spoofing ZIP / RAR
            if ($clientExt === 'zip' && !in_array($realMime, ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'])) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. Magic Number file ini bukan ZIP asli.');
            }
            if ($clientExt === 'rar' && !in_array($realMime, ['application/x-rar-compressed', 'application/vnd.rar', 'application/octet-stream'])) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. Magic Number file ini bukan RAR asli.');
            }

            // Cek spoofing Video (.mp4, .avi, .mkv, .mov, .webm)
            if (in_array($clientExt, ['mp4', 'avi', 'mkv', 'mov', 'webm']) && !str_starts_with($realMime, 'video/')) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. Magic Number file ini bukan video asli.');
            }

            // Cek spoofing Audio (.mp3, .wav, .ogg, .flac, .aac)
            if (in_array($clientExt, ['mp3', 'wav', 'ogg', 'flac', 'aac']) && !str_starts_with($realMime, 'audio/')) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. Magic Number file ini bukan audio asli.');
            }
        }

        // --- 4. KEAMANAN: TENANT ISOLATION (Private Storage) ---
        // Nama file sudah disanitasi di atas, aman dari path traversal
        $path = $file->storeAs('private/files', $fileName);
        
        \App\Models\FileItem::create([
            'name'      => $fileName,
            'file_path' => $path,
            'user_id'   => Auth::id(),
            'folder_id' => $request->folder_id ?? null,
            'mime_type' => $file->getClientMimeType(), 
            'size'      => $file->getSize()                 
        ]);

        return back()->with('success', 'File berhasil diupload.');
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
        // withTrashed() agar file yang ada di sampah tetap bisa dipreview
        $file = \App\Models\FileItem::withTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $path = storage_path('app/private/' . $file->file_path);
        if (!file_exists($path)) {
            // Coba path lain
            $altPath = storage_path('app/' . $file->file_path);
            if (file_exists($altPath)) {
                $path = $altPath;
            } else {
                return response(view('file-error', [
                    'title' => 'File Tidak Ditemukan',
                    'file' => $file, 
                    'message' => 'File secara fisik tidak ditemukan di server (kemungkinan telah dihapus secara manual).'
                ]), 404);
            }
        }

        // --- CEK FILE KOSONG SAAT DIBUKA ---
        // Menampilkan halaman khusus sesuai instruksi jika isi file kosong (0 bytes)
        if (filesize($path) == 0) {
            return response(view('file-error', [
                'title' => 'File Kosong',
                'file' => $file,
                'message' => 'Tidak dapat melihat pratinjau file'
            ]), 200);
        }

        $ext = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
        if ($ext === 'docx') {
            return response(view('document-preview', compact('file')), 200);
        }
        
        if ($ext === 'zip') {
            $zip = new \ZipArchive;
            $zipContents = [];
            $rootItems = [];
            $currentPath = request('path', '');
            
            if ($zip->open($path) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    $name = $stat['name'];
                    
                    // Abaikan file sistem bawaan Mac
                    if (str_starts_with($name, '__MACOSX')) continue;
                    
                    // Jika sedang berada di dalam folder, abaikan file di luar folder tsb
                    if ($currentPath !== '' && !str_starts_with($name, $currentPath)) continue;
                    
                    $relativePath = substr($name, strlen($currentPath));
                    if ($relativePath === '' || $relativePath === '/') continue; // abaikan folder itu sendiri
                    
                    $parts = explode('/', trim($relativePath, '/'));
                    $rootName = $parts[0];
                    
                    if (!empty($rootName) && !isset($rootItems[$rootName])) {
                        $isFolder = (count($parts) > 1 || str_ends_with($relativePath, '/'));
                        $fullPath = $currentPath . $rootName . ($isFolder ? '/' : '');
                        $rootItems[$rootName] = [
                            'name' => $rootName,
                            'is_folder' => $isFolder,
                            'full_path' => $fullPath,
                            'size' => $isFolder ? '-' : $stat['size'],
                            'mtime' => $stat['mtime']
                        ];
                    }
                }
                $zip->close();
                
                // Urutkan: folder di atas, file di bawah
                usort($rootItems, function($a, $b) {
                    if ($a['is_folder'] == $b['is_folder']) return strcasecmp($a['name'], $b['name']);
                    return $a['is_folder'] ? -1 : 1;
                });
                
                $zipContents = $rootItems;
            }
            
            return response(view('zip-preview', compact('file', 'zipContents', 'currentPath')), 200);
        }

        $mime = mime_content_type($path);
        
        // --- CEK FORMAT YANG TIDAK DIDUKUNG OLEH BROWSER ---
        $previewableMimes = [
            'application/pdf',
            'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp',
            'text/plain', 'text/html', 'text/css', 'text/javascript',
            'video/mp4', 'audio/mpeg'
        ];

        if (!in_array($mime, $previewableMimes) && !str_starts_with($mime, 'image/') && !str_starts_with($mime, 'text/') && !str_starts_with($mime, 'video/')) {
            return response(view('file-error', [
                'title' => 'Pratinjau Tidak Tersedia',
                'file' => $file,
                'message' => 'Tidak ada pratinjau yang tersedia'
            ]), 200);
        }
        
        return response()->file($path, [
            'Content-Type' => $mime,
        ]);
    }

    public function showFolder(Request $request, $id)
    {
        // withTrashed() agar folder yang ada di sampah tetap bisa dibuka
        $folder = \App\Models\Folder::withTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $order = $request->get('order', 'asc');

        $folders = \App\Models\Folder::withTrashed()->where('user_id', Auth::id())
            ->where('parent_id', $folder->id)
            ->orderBy('name', $order)
            ->get();

        $files = \App\Models\FileItem::withTrashed()->where('user_id', Auth::id())
            ->where('folder_id', $folder->id)
            ->orderBy('name', $order)
            ->get();

        $breadcrumbs = [];
        $current = $folder;
        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent_id ? \App\Models\Folder::withTrashed()->find($current->parent_id) : null;
        }

        // Deteksi apakah folder ini (atau root-nya) berasal dari sampah
        $isTrashed = $breadcrumbs[0]->trashed();

        return view('folder', compact('folder', 'folders', 'files', 'breadcrumbs', 'order', 'isTrashed'));
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

    public function sampah(Request $request)
    {
        $type = $request->get('type', '');
        $modified = $request->get('modified', '');
        
        $folderQuery = \App\Models\Folder::onlyTrashed()->where('user_id', Auth::id());
        $fileQuery = \App\Models\FileItem::onlyTrashed()->where('user_id', Auth::id());
        
        if ($modified) {
            if ($modified == 'today') {
                $folderQuery->whereDate('deleted_at', \Carbon\Carbon::today());
                $fileQuery->whereDate('deleted_at', \Carbon\Carbon::today());
            } elseif ($modified == '7days') {
                $folderQuery->where('deleted_at', '>=', \Carbon\Carbon::now()->subDays(7));
                $fileQuery->where('deleted_at', '>=', \Carbon\Carbon::now()->subDays(7));
            } elseif ($modified == '30days') {
                $folderQuery->where('deleted_at', '>=', \Carbon\Carbon::now()->subDays(30));
                $fileQuery->where('deleted_at', '>=', \Carbon\Carbon::now()->subDays(30));
            }
        }
        
        if ($type == 'folder') {
            $folders = $folderQuery->get();
            $files = collect([]);
        } elseif ($type == 'file') {
            $folders = collect([]);
            $files = $fileQuery->get();
        } else {
            $folders = $folderQuery->get();
            $files = $fileQuery->get();
        }

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

    public function favorit(Request $request)
    {
        $order = $request->get('order', 'asc');
        $type = $request->get('type', '');
        $modified = $request->get('modified', '');

        $folderQuery = \App\Models\Folder::where('user_id', Auth::id())
            ->where('is_favorite', true)
            ->orderBy('name', $order);

        $fileQuery = \App\Models\FileItem::where('user_id', Auth::id())
            ->where('is_favorite', true)
            ->orderBy('name', $order);
            
        if ($modified) {
            if ($modified == 'today') {
                $folderQuery->whereDate('updated_at', \Carbon\Carbon::today());
                $fileQuery->whereDate('updated_at', \Carbon\Carbon::today());
            } elseif ($modified == '7days') {
                $folderQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(7));
                $fileQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(7));
            } elseif ($modified == '30days') {
                $folderQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(30));
                $fileQuery->where('updated_at', '>=', \Carbon\Carbon::now()->subDays(30));
            }
        }
        
        if ($type == 'folder') {
            $folders = $folderQuery->get();
            $files = collect([]);
        } elseif ($type == 'file') {
            $folders = collect([]);
            $files = $fileQuery->get();
        } else {
            $folders = $folderQuery->get();
            $files = $fileQuery->get();
        }

        return view('favorit', compact('folders', 'files', 'order'));
    }

    public function toggleFavoriteFolder($id)
    {
        $folder = \App\Models\Folder::where('user_id', Auth::id())->findOrFail($id);
        $folder->is_favorite = !$folder->is_favorite;
        $folder->save();
        return back()->with('success', 'Status favorit folder diperbarui.');
    }

    public function toggleFavoriteFile($id)
    {
        $file = \App\Models\FileItem::where('user_id', Auth::id())->findOrFail($id);
        $file->is_favorite = !$file->is_favorite;
        $file->save();
        return back()->with('success', 'Status favorit file diperbarui.');
    }

    // ===== BULK ACTIONS =====

    public function bulkTrash(Request $request)
    {
        $folderIds = $request->input('folder_ids', []);
        $fileIds   = $request->input('file_ids', []);

        if (!empty($folderIds)) {
            \App\Models\Folder::where('user_id', Auth::id())->whereIn('id', $folderIds)->delete();
        }
        if (!empty($fileIds)) {
            \App\Models\FileItem::where('user_id', Auth::id())->whereIn('id', $fileIds)->delete();
        }

        return back()->with('success', (count($folderIds) + count($fileIds)) . ' item dipindahkan ke sampah.');
    }

    public function bulkRestore(Request $request)
    {
        $folderIds = $request->input('folder_ids', []);
        $fileIds   = $request->input('file_ids', []);

        if (!empty($folderIds)) {
            \App\Models\Folder::onlyTrashed()->where('user_id', Auth::id())->whereIn('id', $folderIds)->restore();
        }
        if (!empty($fileIds)) {
            \App\Models\FileItem::onlyTrashed()->where('user_id', Auth::id())->whereIn('id', $fileIds)->restore();
        }

        return back()->with('success', (count($folderIds) + count($fileIds)) . ' item berhasil dipulihkan.');
    }

    public function bulkForceDelete(Request $request)
    {
        $folderIds = $request->input('folder_ids', []);
        $fileIds   = $request->input('file_ids', []);

        if (!empty($folderIds)) {
            \App\Models\Folder::onlyTrashed()->where('user_id', Auth::id())->whereIn('id', $folderIds)->forceDelete();
        }
        if (!empty($fileIds)) {
            $files = \App\Models\FileItem::onlyTrashed()->where('user_id', Auth::id())->whereIn('id', $fileIds)->get();
            foreach ($files as $file) {
                $path = storage_path('app/private/' . $file->file_path);
                if (file_exists($path)) unlink($path);
                $altPath = storage_path('app/' . $file->file_path);
                if (file_exists($altPath)) unlink($altPath);
            }
            \App\Models\FileItem::onlyTrashed()->where('user_id', Auth::id())->whereIn('id', $fileIds)->forceDelete();
        }

        return back()->with('success', (count($folderIds) + count($fileIds)) . ' item dihapus permanen.');
    }

    public function bulkFavorite(Request $request)
    {
        $folderIds = $request->input('folder_ids', []);
        $fileIds   = $request->input('file_ids', []);

        if (!empty($folderIds)) {
            $folders = \App\Models\Folder::where('user_id', Auth::id())->whereIn('id', $folderIds)->get();
            foreach ($folders as $f) {
                $f->is_favorite = !$f->is_favorite;
                $f->save();
            }
        }
        if (!empty($fileIds)) {
            $files = \App\Models\FileItem::where('user_id', Auth::id())->whereIn('id', $fileIds)->get();
            foreach ($files as $f) {
                $f->is_favorite = !$f->is_favorite;
                $f->save();
            }
        }

        return back()->with('success', 'Status favorit ' . (count($folderIds) + count($fileIds)) . ' item diperbarui.');
    }

    public function bulkDownload(Request $request)
    {
        $folderIds = $request->input('folder_ids', []);
        $fileIds = $request->input('file_ids', []);

        if (empty($folderIds) && count($fileIds) === 1) {
            // Jika hanya 1 file, langsung download
            $file = \App\Models\FileItem::where('user_id', Auth::id())->find($fileIds[0]);
            if ($file) {
                $path = storage_path('app/private/' . $file->file_path);
                if (!file_exists($path)) {
                    $path = storage_path('app/' . $file->file_path);
                }
                if (file_exists($path)) {
                    return response()->download($path, $file->name);
                }
            }
        }

        if (empty($folderIds) && empty($fileIds)) {
            return back()->with('error', 'Pilih item untuk didownload.');
        }

        $zipName = 'download_' . time() . '.zip';
        $zipPath = storage_path('app/private/' . $zipName);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            
            // Loop folders and add files recursively (simplified: currently we just add direct files inside selected folder for now to avoid deep recursion complexity in this basic implementation, or we can use a recursive function)
            // To make it simple, let's just get files in the selected folder.
            foreach ($folderIds as $fId) {
                $folder = \App\Models\Folder::where('user_id', Auth::id())->find($fId);
                if ($folder) {
                    $this->addFolderToZip($zip, $folder, $folder->name . '/');
                }
            }

            foreach ($fileIds as $id) {
                $file = \App\Models\FileItem::where('user_id', Auth::id())->find($id);
                if ($file) {
                    $path = storage_path('app/private/' . $file->file_path);
                    if (!file_exists($path)) {
                        $path = storage_path('app/' . $file->file_path);
                    }
                    if (file_exists($path)) {
                        $zip->addFile($path, $file->name);
                    }
                }
            }
            $zip->close();
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Gagal membuat file ZIP.');
    }

    private function addFolderToZip($zip, $folder, $zipPathPrefix = '')
    {
        // Add files in this folder
        $files = \App\Models\FileItem::where('user_id', Auth::id())->where('folder_id', $folder->id)->get();
        foreach ($files as $file) {
            $path = storage_path('app/private/' . $file->file_path);
            if (!file_exists($path)) {
                $path = storage_path('app/' . $file->file_path);
            }
            if (file_exists($path)) {
                $zip->addFile($path, $zipPathPrefix . $file->name);
            }
        }

        // Recursively add subfolders
        $subfolders = \App\Models\Folder::where('user_id', Auth::id())->where('parent_id', $folder->id)->get();
        foreach ($subfolders as $sub) {
            $zip->addEmptyDir($zipPathPrefix . $sub->name);
            $this->addFolderToZip($zip, $sub, $zipPathPrefix . $sub->name . '/');
        }
    }
}
