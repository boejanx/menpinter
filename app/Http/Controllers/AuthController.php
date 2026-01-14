<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); 
    }

    public function login(Request $request)
    {

        $turnstile = $request->input('cf-turnstile-response');

        if (!$turnstile) {
            return response()->json(['success' => false, 'message' => 'Verifikasi keamanan gagal.'], 400);
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('TURNSTILE_SECRET_KEY'),
            'response' => $turnstile,
            'remoteip' => $request->ip(),
        ]);

        if (!$response->json('success')) {
            return response()->json(['success' => false, 'message' => 'Verifikasi keamanan gagal.'], 400);
        }

        $request->validate([
            'nip' => 'required',
            'password' => 'required'
        ]);

        $response = Http::asForm()->post('https://polakesatu.pekalongankab.go.id/api/login', [
            'nip' => $request->nip,
            'password' => $request->password
        ]);

        if ($response->successful()) {
            $data = $response->json();

            return response()->json([
                'nama' => $data['nama'] ?? null,
                'nip' => $data['nip'] ?? null,
                'jabatan' => $data['jabatan'] ?? null
            ]);
        }

        return response()->json([
            'error' => 'NIP atau password salah.'
        ], 401);
    }
}

