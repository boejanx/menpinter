<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:scaffold')]
class ScaffoldMakeCommand extends Command
{
    protected $signature = 'make:scaffold {name}';
    protected $description = 'Generate model, controller, migration, route, and empty views for a resource';

    public function handle()
    {
        $name = $this->argument('name');
        $model = ucfirst($name);
        $controller = $model . 'Controller';
        $table = \Illuminate\Support\Str::plural(\Illuminate\Support\Str::snake($name));
        $viewDir = resource_path('views/' . strtolower($table));

        // 1. Generate model, migration, and controller
        $this->info("🛠 Membuat model, migration, dan controller...");
        Artisan::call("make:model", [
            'name' => $model,
            '--migration' => true,
            '--controller' => true,
            '--resource' => true
        ]);
        $this->line(Artisan::output());

        // 2. Tambahkan route ke web.php
        $routePath = base_path('routes/web.php');
        $routeLine = "Route::resource('" . strtolower($table) . "', \\App\\Http\\Controllers\\$controller::class);";

        if (!str_contains(File::get($routePath), $routeLine)) {
            File::append($routePath, "\n" . $routeLine);
            $this->info("✅ Route resource ditambahkan ke routes/web.php");
        } else {
            $this->warn("⚠️  Route sudah ada di routes/web.php");
        }

        // 3. Buat view kosong
        if (!File::exists($viewDir)) {
            File::makeDirectory($viewDir, 0755, true);
        }

        foreach (['index', 'create', 'edit'] as $view) {
            File::put($viewDir . "/{$view}.blade.php", ""); // Kosongkan isi file
        }

        $this->info("📁 Views kosong dibuat di: resources/views/$table");
        $this->info("🎉 Scaffold untuk '$model' selesai dibuat.");
    }
}
