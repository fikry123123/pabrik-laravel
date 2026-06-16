<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BahanMentah;
use App\Models\MasterProduk;
use App\Models\Resep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function index(): View
    {
        return view('recipe.index', [
            'products' => MasterProduk::with('reseps.bahanMentah')->get(),
            'materials' => BahanMentah::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_produk'  => 'required|string|max:100',
            'id_bahan'     => 'required|array|min:1',
            'id_bahan.*'   => 'required|exists:bahan_mentah,id',
            'qty_butuh'    => 'required|array|min:1',
            'qty_butuh.*'  => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($data) {
            $produk = MasterProduk::create(['nama_produk' => $data['nama_produk']]);
            foreach ($data['id_bahan'] as $i => $idBahan) {
                Resep::create([
                    'id_produk' => $produk->id,
                    'id_bahan'  => $idBahan,
                    'qty_butuh' => $data['qty_butuh'][$i],
                ]);
            }
        });

        return back()->with('success', 'Produk dan resep baru berhasil disimpan!');
    }

    public function update(Request $request, MasterProduk $recipe): RedirectResponse
    {
        $data = $request->validate([
            'nama_produk'  => 'required|string|max:100',
            'id_bahan'     => 'required|array|min:1',
            'id_bahan.*'   => 'required|exists:bahan_mentah,id',
            'qty_butuh'    => 'required|array|min:1',
            'qty_butuh.*'  => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($data, $recipe) {
            $recipe->update(['nama_produk' => $data['nama_produk']]);
            $recipe->reseps()->delete();
            foreach ($data['id_bahan'] as $i => $idBahan) {
                Resep::create([
                    'id_produk' => $recipe->id,
                    'id_bahan'  => $idBahan,
                    'qty_butuh' => $data['qty_butuh'][$i],
                ]);
            }
        });

        return back()->with('success', 'Master resep berhasil diperbarui!');
    }

    public function destroy(MasterProduk $recipe): RedirectResponse
    {
        $recipe->delete(); // cascade ke resep

        return back()->with('success', 'Master produk dan resep telah dihapus!');
    }
}
