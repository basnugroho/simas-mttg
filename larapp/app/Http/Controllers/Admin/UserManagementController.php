<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index()
    {
        $me = Auth::user();
        if (! $me || ! $me->isWebmaster()) {
            abort(403);
        }

        $users = User::orderBy('id', 'desc')->paginate(30);
        return view('admin.users', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $me = Auth::user();
        if (! $me || ! $me->isWebmaster()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        $data = $request->validate([
            'role' => ['required', 'in:user,admin,webmaster'],
            'approved' => ['nullable', 'in:0,1'],
        ]);

        $user->role = $data['role'];
        $user->approved = isset($data['approved']) && $data['approved'] == '1';
        $user->save();

        return redirect()->route('admin.users')->with('status', 'Perubahan disimpan.');
    }
}
