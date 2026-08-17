<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BahanMentah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        return view('inventory.index', [
            'materials' => BahanMentah::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'stok'   => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
        ]);

        BahanMentah::create(array_merge(
            $request->only('nama', 'stok', 'satuan'),
            ['created_by' => $request->user()->id]
        ));

        return back()->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    public function update(Request $request, BahanMentah $inventory): RedirectResponse
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'stok'   => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
        ]);

        $inventory->update(array_merge(
            $request->only('nama', 'stok', 'satuan'),
            ['updated_by' => $request->user()->id]
        ));

        return back()->with('success', 'Data bahan baku diperbarui!');
    }

    public function destroy(BahanMentah $inventory): RedirectResponse
    {
        $inventory->delete();

        return back()->with('success', 'Bahan baku dihapus!');
    }
}
