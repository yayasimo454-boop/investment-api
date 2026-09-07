<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('wallets.currency')->orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['sometimes', 'in:user,admin'],
            'badge' => ['sometimes', 'in:none,vip,certified'],
            'kyc_status' => ['sometimes', 'in:pending,verified,rejected'],
            'is_blocked' => ['sometimes', 'boolean'],
        ]);

        if ($user->id === $request->user()->id) {
            if (isset($validated['role']) && $validated['role'] !== 'admin') {
                return response()->json(['message' => 'Vous ne pouvez pas retirer vos propres droits administrateur.'], 422);
            }
            if (isset($validated['is_blocked']) && $validated['is_blocked']) {
                return response()->json(['message' => 'Vous ne pouvez pas bloquer votre propre compte.'], 422);
            }
        }

        $oldValues = $user->only(array_keys($validated));

        $user->update($validated);

        AuditLog::create([
            'admin_id' => $request->user()->id,
            'action' => 'user.updated',
            'target_type' => 'User',
            'target_id' => $user->id,
            'old_value' => $oldValues,
            'new_value' => $validated,
        ]);

        return response()->json($user->load('wallets.currency'));
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 422);
        }

        AuditLog::create([
            'admin_id' => $request->user()->id,
            'action' => 'user.deleted',
            'target_type' => 'User',
            'target_id' => $user->id,
            'old_value' => $user->toArray(),
            'new_value' => null,
        ]);

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }
}