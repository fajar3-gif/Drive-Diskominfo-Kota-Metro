<?php

namespace App\Services;

use App\Models\FileItem;
use App\Models\Folder;
use ZipArchive;

class ZipService
{
    /**
     * Secara rekursif menambahkan isi folder (file & subfolder) ke dalam ZIP archive.
     * Fungsi ini dipisahkan dari controller karena merupakan business logic murni
     * yang berinteraksi dengan filesystem, bukan tanggung jawab controller.
     *
     * @param ZipArchive $zip           Instance ZipArchive yang sedang diisi
     * @param Folder     $folder        Folder yang akan ditambahkan
     * @param string     $zipPathPrefix Prefix path di dalam ZIP (misal: "nama_folder/")
     * @param int        $userId        ID user pemilik file (untuk keamanan tenant isolation)
     */
    public function addFolderToZip(ZipArchive $zip, Folder $folder, string $zipPathPrefix, int $userId): void
    {
        // Tambahkan semua file di dalam folder ini
        $files = FileItem::where('user_id', $userId)
                         ->where('folder_id', $folder->id)
                         ->get();

        foreach ($files as $file) {
            $path = storage_path('app/private/' . $file->file_path);
            if (!file_exists($path)) {
                $path = storage_path('app/' . $file->file_path);
            }
            if (file_exists($path)) {
                $zip->addFile($path, $zipPathPrefix . $file->name);
            }
        }

        // Rekursif: tambahkan semua subfolder
        $subfolders = Folder::where('user_id', $userId)
                            ->where('parent_id', $folder->id)
                            ->get();

        foreach ($subfolders as $sub) {
            $zip->addEmptyDir($zipPathPrefix . $sub->name);
            $this->addFolderToZip($zip, $sub, $zipPathPrefix . $sub->name . '/', $userId);
        }
    }
}
