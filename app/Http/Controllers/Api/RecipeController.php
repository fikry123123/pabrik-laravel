<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterProduk;
use App\Models\Resep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    /**
     * GET /api/recipes
     * Kembalikan semua produk beserta BOM-nya.
     */
    public function index(): JsonResponse
    {
        $products = MasterProduk::with([
            'reseps.bahanMentah:id,nama,stok,satuan',
        ])->get();

        // Format supaya Flutter mudah parse
        $result = $products->map(fn($p) => [
            'id'          => $p->id,
            'nama_produk' => $p->nama_produk,
            'kapasitas'   => $p->kapasitasMaksimal(),
            'bom'         => $p->reseps->map(fn($r) => [
                'id'          => $r->id,
                'id_bahan'    => $r->id_bahan,
                'nama_bahan'  => $r->bahanMentah->nama,
                'satuan'      => $r->bahanMentah->satuan,
                'stok'        => $r->bahanMentah->stok,
                'qty_butuh'   => $r->qty_butuh,
            ]),
        ]);

        return response()->json($result);
    }

    /**
     * POST /api/recipes
     * Body: { nama_produk, bahan_list: [id_bahan], qty_list: [qty] }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_produk'   => 'required|string|max:100',
            'bahan_list'    => 'required|array|min:1',
            'bahan_list.*'  => 'required|exists:bahan_mentah,id',
            'qty_list'      => 'required|array|min:1',
            'qty_list.*'    => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($data, &$produk) {
            $produk = MasterProduk::create(['nama_produk' => $data['nama_produk']]);

            foreach ($data['bahan_list'] as $i => $idBahan) {
                Resep::create([
                    'id_produk'  => $produk->id,
                    'id_bahan'   => $idBahan,
                    'qty_butuh'  => $data['qty_list'][$i],
                ]);
            }
        });

        return response()->json(['status' => 'success', 'id' => $produk->id], 201);
    }

    /**
     * PUT /api/recipes/{id}
     */
    public function update(Request $request, MasterProduk $recipe): JsonResponse
    {
        $data = $request->validate([
            'nama_produk'   => 'required|string|max:100',
            'bahan_list'    => 'required|array|min:1',
            'bahan_list.*'  => 'required|exists:bahan_mentah,id',
            'qty_list'      => 'required|array|min:1',
            'qty_list.*'    => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($data, $recipe) {
            $recipe->update(['nama_produk' => $data['nama_produk']]);

            // Delete-then-reinsert (sama seperti versi PHP lama)
            $recipe->reseps()->delete();

            foreach ($data['bahan_list'] as $i => $idBahan) {
                Resep::create([
                    'id_produk' => $recipe->id,
                    'id_bahan'  => $idBahan,
                    'qty_butuh' => $data['qty_list'][$i],
                ]);
            }
        });

        return response()->json(['status' => 'success']);
    }

    /**
     * DELETE /api/recipes/{id}
     */
    public function destroy(MasterProduk $recipe): JsonResponse
    {
        // reseps dihapus otomatis karena onDelete('cascade') di migration
        $recipe->delete();

        return response()->json(['status' => 'success']);
    }
}
