<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => ['required', 'digits:18'],
            'password' => ['required', 'string'],
        ]);

        try {
            app(AuthService::class)->authenticate($request);
            $request->session()->regenerate();

            return $this->successResponse($request);
        } catch (\Throwable $e) {
            return $this->errorResponse($request, $e->getMessage());
        }
    }

    private function successResponse(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect_to' => route('dashboard'),
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    private function errorResponse(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => ['nip' => [$message]],
            ], 422);
        }

        return back()->withErrors(['nip' => $message])->withInput();
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
