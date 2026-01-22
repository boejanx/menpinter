<?php 

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public static function log(
        string $action,
        ?string $module = null,
        ?string $description = null,
        ?int $userId = null
    ): void {
        Log::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'module'     => $module,
            'description'=> $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
