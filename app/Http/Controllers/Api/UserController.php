<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * GET /api/users
     */
    public function index(): JsonResponse
    {
        return response()->json(User::select('id', 'username', 'role', 'created_at')->get());
    }

    /**
     * POST /api/users
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,editor,reviewer',
        ]);

        $user = User::create($data);

        return response()->json(['status' => 'success', 'user' => $user->only('id', 'username', 'role')], 201);
    }

    /**
     * PUT /api/users/{id}
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:admin,editor,reviewer',
        ]);

        // Hapus password dari data jika kosong (tidak diubah)
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['status' => 'success', 'user' => $user->only('id', 'username', 'role')]);
    }

    /**
     * DELETE /api/users/{id}
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        // Tidak boleh hapus diri sendiri
        if ($user->id === $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Tidak bisa menghapus akun sendiri.'], 422);
        }

        $user->delete();

        return response()->json(['status' => 'success']);
    }
}
