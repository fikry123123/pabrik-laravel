<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BahanMentah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * GET /api/inventory
     */
    public function index(): JsonResponse
    {
        return response()->json(BahanMentah::all());
    }

    /**
     * POST /api/inventory
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama'   => 'required|string|max:100',
            'stok'   => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
        ]);

        $bahan = BahanMentah::create($data);

        return response()->json(['status' => 'success', 'data' => $bahan], 201);
    }

    /**
     * PUT /api/inventory/{id}
     */
    public function update(Request $request, BahanMentah $inventory): JsonResponse
    {
        $data = $request->validate([
            'nama'   => 'required|string|max:100',
            'stok'   => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
        ]);

        $inventory->update($data);

        return response()->json(['status' => 'success', 'data' => $inventory]);
    }

    /**
     * DELETE /api/inventory/{id}
     */
    public function destroy(BahanMentah $inventory): JsonResponse
    {
        $inventory->delete();

        return response()->json(['status' => 'success']);
    }
}
