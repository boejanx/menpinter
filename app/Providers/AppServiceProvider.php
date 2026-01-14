<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Carbon::setLocale('id');
        Blade::directive('tanggal', function ($expression) {
            return "<?php echo \\Carbon\\Carbon::parse($expression)->translatedFormat('d F Y'); ?>";
        });
        
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $roles = Auth::user()->roles->pluck('name');

                $menus = Menu::whereNull('parent_id')
                    ->whereHas('roles', fn ($q) => $q->whereIn('name', $roles))
                    ->with(['children' => fn ($q) =>
                        $q->whereHas('roles', fn ($q) => $q->whereIn('name', $roles))
                    ])
                    ->orderBy('order')
                    ->get();

                $view->with('menus', $menus);
            }
        });
        Paginator::useBootstrapFive();
    }
}
