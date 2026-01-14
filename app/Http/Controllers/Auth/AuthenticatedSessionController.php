<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\User;
use App\Services\AuthService;

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
            $result = app(AuthService::class)->authenticate($request);

            $request->session()->regenerate();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_to' => route('dashboard'),
                ]);
            }

            return redirect()->intended(route('dashboard'));
        } catch (\Throwable $e) {

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => ['nip' => [$e->getMessage()]],
                ], 422);
            }

            return back()
                ->withErrors(['nip' => $e->getMessage()])
                ->withInput();
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

    /**
     * Helper untuk respon error (agar tidak duplikat)
     */
    private function errorResponse(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => 'gagal',
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
