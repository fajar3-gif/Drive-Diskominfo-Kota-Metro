<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FileItem;
use App\Services\FilePreviewService;

class FileController extends Controller
{
    public function __construct(private FilePreviewService $filePreviewService) {}

    public function storeFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');

        // --- KEAMANAN: SANITASI NAMA FILE (Mencegah Path Traversal) ---
        $rawName  = $file->getClientOriginalName();
        $fileName = basename($rawName);                          // Hapus path traversal (../)
        $fileName = preg_replace('/[^\w\s\-.]/', '', $fileName); // Hanya izinkan karakter aman
        $fileName = preg_replace('/\s+/', '_', $fileName);       // Ganti spasi dengan underscore
        if (empty($fileName)) {
            $fileName = 'file_' . time();
        }

        $clientExt = strtolower($file->getClientOriginalExtension());

        // --- 1. CEK KUOTA PENYIMPANAN (1 GB) ---
        $quotaBytes  = 1 * 1024 * 1024 * 1024;
        $usedStorage = FileItem::where('user_id', Auth::id())->sum('size');

        if (($usedStorage + $file->getSize()) > $quotaBytes) {
            return back()->with('error', 'Kapasitas Penyimpanan Penuh! Batas maksimal Anda adalah 1 GB.');
        }

        // --- 2. CEK FILE KOSONG ---
        // File kosong (0 bytes) tetap diizinkan diupload; akan ditangani saat preview.
        if ($file->getSize() > 0) {
            // --- 2. CEK DOUBLE EXTENSION (Misal: laporan.pdf.exe) ---
            $segments = explode('.', $fileName);
            if (count($segments) > 2) {
                $lastExt       = strtolower(end($segments));
                $secondLastExt = strtolower($segments[count($segments) - 2]);
                if (
                    in_array($lastExt, ['exe', 'php', 'sh', 'bat', 'cmd', 'ps1']) &&
                    in_array($secondLastExt, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png', 'mp4'])
                ) {
                    return back()->with('error', 'Keamanan: Terdeteksi Double Extension (Spoofing). File ditolak.');
                }
            }

            // --- 3. PEMINDAIAN KONTEN & MAGIC NUMBER (FILE SIGNATURE) ---
            $realMime = $file->getMimeType();

            // Cek spoofing PDF
            if ($clientExt === 'pdf' && $realMime !== 'application/pdf') {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. Magic Number file ini bukan PDF asli.');
            }

            // Cek spoofing gambar raster (JPG/PNG/GIF/WEBP)
            if (in_array($clientExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']) && !str_starts_with($realMime, 'image/')) {
                return back()->with('error', 'Keamanan: Spoofing terdeteksi. Magic Number file ini bukan gambar asli.');
            }

            // Cek spoofing SVG
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
        $path = $file->storeAs('private/files', $fileName);

        FileItem::create([
            'name'      => $fileName,
            'file_path' => $path,
            'user_id'   => Auth::id(),
            'folder_id' => $request->folder_id ?? null,
            'mime_type' => $file->getClientMimeType(),
            'size'      => $file->getSize()
        ]);

        return back()->with('success', 'File berhasil diupload.');
    }

    public function showFile($id)
    {
        // withTrashed() agar file yang ada di sampah tetap bisa dipreview
        $file = FileItem::withTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $path = storage_path('app/private/' . $file->file_path);

        if (!file_exists($path)) {
            $altPath = storage_path('app/' . $file->file_path);
            if (file_exists($altPath)) {
                $path = $altPath;
            } else {
                return response(view('file-error', [
                    'title'   => 'File Tidak Ditemukan',
                    'file'    => $file,
                    'message' => 'File secara fisik tidak ditemukan di server (kemungkinan telah dihapus secara manual).'
                ]), 404);
            }
        }

        return $this->filePreviewService->preview($file, $path);
    }

    public function downloadFile($id)
    {
        $file = FileItem::where('user_id', Auth::id())->findOrFail($id);
        $path = storage_path('app/private/' . $file->file_path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        return response()->download($path, $file->name);
    }

    public function toggleFavoriteFile($id)
    {
        $file              = FileItem::where('user_id', Auth::id())->findOrFail($id);
        $file->is_favorite = !$file->is_favorite;
        $file->save();
        return back()->with('success', 'Status favorit file diperbarui.');
    }
}
