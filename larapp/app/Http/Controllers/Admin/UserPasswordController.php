<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserPasswordController extends Controller
{
    public function edit($id)
    {
        $me = Auth::user();
        if (! $me) {
            abort(403);
        }

        $user = User::findOrFail((int)$id);

        // Only webmaster or the user themselves may access this page
        if (! ($me->isWebmaster() || $me->id === $user->id)) {
            return redirect()->route('dashboard')->with('status', 'Anda tidak punya izin mengakses halaman ubah password ini.');
        }

        return view('admin.user.index', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $me = Auth::user();
        if (! $me) {
            abort(403);
        }

        $user = User::findOrFail((int)$id);

        // Only webmaster or the user themselves may update the password
        if (! ($me->isWebmaster() || $me->id === $user->id)) {
            return redirect()->route('dashboard')->with('status', 'Anda tidak punya izin mengubah password ini.');
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // The User model casts password as 'hashed' so assignment will hash it.
        $user->password = $data['password'];
        $user->save();

        Log::info('Password changed', ['by' => $me->id, 'target' => $user->id]);

        // Redirect: if webmaster changed another user's password, go back to admin users list.
        if ($me->isWebmaster() && $me->id !== $user->id) {
            return redirect()->route('admin.users')->with('status', 'Password user berhasil diubah.');
        }

        // Otherwise (self-change) redirect to dashboard
        return redirect()->route('dashboard')->with('status', 'Password berhasil diubah.');
    }
}
