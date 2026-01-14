<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuManagementController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $menus = Menu::orderBy('order')->get();
        $roleMenuAccess = [];

        // Query role_menu dan simpan dalam array
        $roleMenuRaw = DB::table('menu_role')->get();
        foreach ($roleMenuRaw as $rm) {
            $roleMenuAccess[$rm->menu_id][$rm->role_id] = true;
        }

        return view('menu-management.index', compact('menus', 'roles', 'roleMenuAccess'));
    }

    public function store(Request $request)
    {
        DB::table('menu_role')->truncate(); // hapus semua, lalu simpan ulang

        $data = $request->input('access', []);
        foreach ($data as $menuId => $roles) {
            foreach ($roles as $roleId => $value) {
                DB::table('menu_role')->insert([
                    'menu_id' => $menuId,
                    'role_id' => $roleId,
                ]);
            }
        }

        return redirect()->route('menu-management.index')->with('success', 'Akses berhasil disimpan.');
    }
}