<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    public function sampah(Request $request)
    {
        $type     = $request->get('type', '');
        $modified = $request->get('modified', '');

        $folderQuery = \App\Models\Folder::onlyTrashed()->where('user_id', Auth::id());
        $fileQuery   = \App\Models\FileItem::onlyTrashed()->where('user_id', Auth::id());

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
            $files   = collect([]);
        } elseif ($type == 'file') {
            $folders = collect([]);
            $files   = $fileQuery->get();
        } else {
            $folders = $folderQuery->get();
            $files   = $fileQuery->get();
        }

        return view('sampah', compact('folders', 'files'));
    }
}
