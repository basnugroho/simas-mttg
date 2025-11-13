<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Only allow webmaster or admin who is approved
        if (! $user->canAccessDashboard()) {
            return view('auth.awaiting_approval');
        }

        // show dashboard view
        return view('dashboard');
    }
}
