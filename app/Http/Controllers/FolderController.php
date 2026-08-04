<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Folder;

class FolderController extends Controller
{
    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $folder = Folder::create([
            'name'      => $request->name,
            'user_id'   => Auth::id(),
            'parent_id' => $request->parent_id ?? null
        ]);

        $url = $folder->parent_id ? url('/folder/show/' . $folder->parent_id) : route('dashboard');

        return redirect($url)->with([
            'success'       => 'Folder berhasil dibuat!',
            'new_item_id'   => $folder->id,
            'new_item_type' => 'folder'
        ]);
    }

    public function updateFolder(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $folder = Folder::where('user_id', Auth::id())->findOrFail($id);
        $folder->update(['name' => $request->name]);

        return back();
    }

    public function showFolder(Request $request, $id)
    {
        // withTrashed() agar folder yang ada di sampah tetap bisa dibuka
        $folder = Folder::withTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $order  = $request->get('order', 'asc');

        $folders = Folder::withTrashed()
            ->where('user_id', Auth::id())
            ->where('parent_id', $folder->id)
            ->orderBy('name', $order)
            ->get();

        $files = \App\Models\FileItem::withTrashed()
            ->where('user_id', Auth::id())
            ->where('folder_id', $folder->id)
            ->orderBy('name', $order)
            ->get();

        $breadcrumbs = [];
        $current     = $folder;
        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent_id
                ? Folder::withTrashed()->find($current->parent_id)
                : null;
        }

        // Deteksi apakah folder ini (atau root-nya) berasal dari sampah
        $isTrashed = $breadcrumbs[0]->trashed();

        return view('folder', compact('folder', 'folders', 'files', 'breadcrumbs', 'order', 'isTrashed'));
    }

    public function deleteFolder($id)
    {
        $folder = Folder::where('user_id', Auth::id())->findOrFail($id);
        $folder->delete();
        return back()->with('success', 'Folder berhasil dipindahkan ke sampah.');
    }

    public function restoreFolder($id)
    {
        $folder = Folder::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $folder->restore();
        return back()->with('success', 'Folder berhasil dipulihkan.');
    }

    public function forceDeleteFolder($id)
    {
        $folder = Folder::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $folder->forceDelete();
        return back()->with('success', 'Folder dihapus permanen.');
    }

    public function toggleFavoriteFolder($id)
    {
        $folder              = Folder::where('user_id', Auth::id())->findOrFail($id);
        $folder->is_favorite = !$folder->is_favorite;
        $folder->save();
        return back()->with('success', 'Status favorit folder diperbarui.');
    }
}
