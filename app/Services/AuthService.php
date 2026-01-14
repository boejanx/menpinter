<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthService
{
    public function authenticate(Request $request): array
    {
        $nip = $request->nip;
        $password = $request->password;

        if ($this->loginLocal($nip, $password)) {
            return [
                'success' => true,
                'user' => Auth::user()
            ];
        }

        $externalUser = $this->loginExternal($nip, $password);

        if (!$externalUser) {
            throw new \Exception('NIP atau password salah.');
        }

        // 3️⃣ Sinkron user ke DB lokal
        $user = $this->syncUser($externalUser, $password);

        Auth::login($user);

        return [
            'success' => true,
            'user' => $user
        ];
    }

    protected function loginLocal(string $nip, string $password): bool
    {
        return Auth::attempt([
            'nip' => $nip,
            'password' => $password
        ]);
    }

    protected function loginExternal(string $nip, string $password): ?array
    {
        try {
            $response = Http::asForm()
                ->timeout(5)
                ->connectTimeout(2)
                ->post(
                    config('services.polakesatu.login_url'),
                    compact('nip', 'password')
                );

            $data = $response->json();

            return (!empty($data['nip']) && !empty($data['nama']))
                ? $data
                : null;

        } catch (\Throwable $e) {
            throw new \Exception('Gagal terhubung ke server polakesatu.');
        }
    }

    protected function syncUser(array $data, string $password): User
    {
        $user = User::where('nip', $data['nip'])->first();

        if ($user) {
            $user->update([
                'name' => $data['nama'],
                'password' => Hash::make($password),
            ]);
        } else {
            $user = User::create([
                'nip' => $data['nip'],
                'email' => $data['nip'],
                'name' => $data['nama'],
                'password' => Hash::make($password),
            ]);

            $user->assignRole('user');
        }

        return $user;
    }
}
