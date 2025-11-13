<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserApprovalController extends Controller
{
    public function index()
    {
        $me = Auth::user();
        if (! $me || ! $me->isWebmaster()) {
            abort(403, 'Unauthorized');
        }

        $pending = User::where('role', 'user')->where('approved', false)->get();
        return view('admin.pending_users', ['pending' => $pending]);
    }

    public function approve(Request $request, $id)
    {
        $me = Auth::user();
        if (! $me || ! $me->isWebmaster()) {
            abort(403, 'Unauthorized');
        }

        $user = User::findOrFail($id);
        // Promote to admin and mark approved
        $user->role = 'admin';
        $user->approved = true;
        $user->save();

        return redirect()->route('admin.pending')->with('status', 'User approved and promoted to admin.');
    }
}
