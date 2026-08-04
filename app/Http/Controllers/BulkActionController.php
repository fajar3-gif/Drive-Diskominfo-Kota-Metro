<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ZipService;

class BulkActionController extends Controller
{
    public function __construct(private ZipService $zipService) {}

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
                $path    = storage_path('app/private/' . $file->file_path);
                $altPath = storage_path('app/' . $file->file_path);
                if (file_exists($path)) unlink($path);
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
        $fileIds   = $request->input('file_ids', []);

        // Jika hanya 1 file tanpa folder, langsung download
        if (empty($folderIds) && count($fileIds) === 1) {
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

            // Tambahkan folder beserta isinya secara rekursif via ZipService
            foreach ($folderIds as $fId) {
                $folder = \App\Models\Folder::where('user_id', Auth::id())->find($fId);
                if ($folder) {
                    $this->zipService->addFolderToZip($zip, $folder, $folder->name . '/', Auth::id());
                }
            }

            // Tambahkan file individual
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
}
