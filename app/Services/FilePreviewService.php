<?php

namespace App\Services;

use App\Models\FileItem;
use ZipArchive;

class FilePreviewService
{
    /**
     * Menangani logika berat untuk membaca dan mempratinjau file.
     * Mulai dari ekstrak isi ZIP secara live, baca MIME types, hingga
     * cek file eksis atau kosong.
     *
     * @param FileItem $file Data model file dari database
     * @param string   $path Path absolut dari file fisik
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function preview(FileItem $file, string $path)
    {
        // --- CEK FILE KOSONG SAAT DIBUKA ---
        if (filesize($path) == 0) {
            return response(view('file-error', [
                'title'   => 'File Kosong',
                'file'    => $file,
                'message' => 'Tidak dapat melihat pratinjau file'
            ]), 200);
        }

        $ext = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));

        if ($ext === 'docx') {
            return response(view('document-preview', compact('file')), 200);
        }

        if ($ext === 'zip') {
            $zip         = new ZipArchive;
            $zipContents = [];
            $rootItems   = [];
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
                    if ($relativePath === '' || $relativePath === '/') continue;

                    $parts    = explode('/', trim($relativePath, '/'));
                    $rootName = $parts[0];

                    if (!empty($rootName) && !isset($rootItems[$rootName])) {
                        $isFolder            = (count($parts) > 1 || str_ends_with($relativePath, '/'));
                        $fullPath            = $currentPath . $rootName . ($isFolder ? '/' : '');
                        $rootItems[$rootName] = [
                            'name'      => $rootName,
                            'is_folder' => $isFolder,
                            'full_path' => $fullPath,
                            'size'      => $isFolder ? '-' : $stat['size'],
                            'mtime'     => $stat['mtime']
                        ];
                    }
                }
                $zip->close();

                // Urutkan: folder di atas, file di bawah
                usort($rootItems, function ($a, $b) {
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

        if (
            !in_array($mime, $previewableMimes) &&
            !str_starts_with($mime, 'image/') &&
            !str_starts_with($mime, 'text/') &&
            !str_starts_with($mime, 'video/')
        ) {
            return response(view('file-error', [
                'title'   => 'Pratinjau Tidak Tersedia',
                'file'    => $file,
                'message' => 'Tidak ada pratinjau yang tersedia'
            ]), 200);
        }

        return response()->file($path, ['Content-Type' => $mime]);
    }
}
