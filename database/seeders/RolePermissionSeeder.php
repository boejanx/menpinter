<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()["cache"]->forget('spatie.permission.cache');

        // Buat Permissions
        Permission::create(['name' => 'Tambah Bangkom']);
        Permission::create(['name' => 'Ubah Bangkom']);
        Permission::create(['name' => 'Hapus Bangkom']);
        Permission::create(['name' => 'Verifikasi Bangkom']);
        Permission::create(['name' => 'Lihat Bangkom']);

        // Role Admin - bisa melakukan semuanya
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        // Role Writer - bisa melihat, mengedit, mempublish artikel
        $roleWriter = Role::create(['name' => 'verifikator']);
        $roleWriter->givePermissionTo(['Verifikasi Bangkom']);

        // Role Editor - bisa melihat, mengedit, mempublish, dan unpublish artikel
        $roleEditor = Role::create(['name' => 'user']);
        $roleEditor->givePermissionTo(['Lihat Bangkom']);
    }
}
