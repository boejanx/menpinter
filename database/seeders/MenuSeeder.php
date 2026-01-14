<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Role;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $nip   = Role::firstOrCreate(['name' => 'user']);

        $dashboard = Menu::create(['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'bi-house', 'order' => 1]);
        $dashboard->roles()->attach([$admin->id, $nip->id]);

        $users = Menu::create(['name' => 'Kelola User', 'route' => 'users.index', 'icon' => 'bi-people', 'order' => 2]);
        $users->roles()->attach($admin->id);
    }
}
