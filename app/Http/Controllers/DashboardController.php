<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $keyword  = $request->telusuri;
        $order    = $request->get('order', 'asc');
        $type     = $request->get('type', '');
        $modified = $request->get('modified', '');

        $folderQuery = \App\Models\Folder::where('user_id', Auth::id())->whereNull('parent_id');
        $fileQuery   = \App\Models\FileItem::where('user_id', Auth::id())->whereNull('folder_id');

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
            $files   = collect([]);
        } elseif ($type == 'file') {
            $folders = collect([]);
            $files   = $fileQuery->get();
        } else {
            $folders = $folderQuery->get();
            $files   = $fileQuery->get();
        }

        return view('dashboard', compact('folders', 'files', 'order'));
    }

    public function terbaru(Request $request)
    {
        $keyword  = $request->telusuri;
        $type     = $request->get('type', '');
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

        // Terbaru tidak punya folder, jika filter folder maka kosongkan
        if ($type == 'folder') {
            $files = collect([]);
        } else {
            $files = $fileQuery->get();
        }

        return view('terbaru', compact('files'));
    }

    public function favorit(Request $request)
    {
        $order    = $request->get('order', 'asc');
        $type     = $request->get('type', '');
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
            $files   = collect([]);
        } elseif ($type == 'file') {
            $folders = collect([]);
            $files   = $fileQuery->get();
        } else {
            $folders = $folderQuery->get();
            $files   = $fileQuery->get();
        }

        return view('favorit', compact('folders', 'files', 'order'));
    }
}
