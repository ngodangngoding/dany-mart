<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthPreviewController extends Controller
{
    public function showLogin(): View
    {
        return view('welcome');
    }

    public function login(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'role' => ['required', 'in:kasir,admin'],
        ]);

        $request->session()->put('preview_role', $payload['role']);

        return redirect()->route($payload['role'] === 'admin' ? 'admin.dashboard' : 'kasir.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('preview_role');

        return redirect()->route('login.preview');
    }
}
