<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::with('permissions')->orderBy('username')->get(),
            'features' => UserPermission::FEATURES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,editor,reviewer',
            'features' => 'nullable|array',
            'features.*' => 'in:' . implode(',', UserPermission::FEATURES),
        ]);

        try {
            $user = User::create($request->only('username', 'password', 'role'));
            $this->syncPermissions($user, $request->input('features', []));
            return back()->with('success', 'User baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Log exception for debugging and show friendly error
            logger()->error('User creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:admin,editor,reviewer',
            'features' => 'nullable|array',
            'features.*' => 'in:' . implode(',', UserPermission::FEATURES),
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $this->syncPermissions($user, $request->input('features', []));

        return back()->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus!');
    }

    // ─── Permission Management ──────────────────────────────────────────────────

    /**
     * Update permissions untuk specific role
     */
    public function updateUserPermissions(Request $request, User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Akses akun admin tidak dapat diubah.');
        }

        $request->validate([
            'features' => 'nullable|array',
            'features.*' => 'in:' . implode(',', UserPermission::FEATURES),
        ]);

        $selectedFeatures = $request->input('features', []);

        foreach (UserPermission::FEATURES as $feature) {
            UserPermission::updateOrCreate(
                ['user_id' => $user->id, 'feature' => $feature],
                ['can_manage' => in_array($feature, $selectedFeatures, true)]
            );
        }

        return back()->with('success', "Hak akses {$user->username} berhasil diperbarui.");
    }

    private function syncPermissions(User $user, array $selectedFeatures): void
    {
        foreach (UserPermission::FEATURES as $feature) {
            UserPermission::updateOrCreate(
                ['user_id' => $user->id, 'feature' => $feature],
                ['can_manage' => !$user->isAdmin() && $user->role !== 'reviewer' && in_array($feature, $selectedFeatures, true)]
            );
        }
    }
}
