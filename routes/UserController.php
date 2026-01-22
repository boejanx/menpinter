<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Eager load roles agar query efisien
            $data = User::with('roles')->select('users.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role', function($row){
                    // Ambil nama role pertama (asumsi 1 user 1 role utama)
                    return $row->getRoleNames()->first() ?? '-';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="d-flex gap-2">';
                    $btn .= '<button onclick="editUser('.$row->id.')" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i></button>';
                    
                    // Cegah user menghapus akunnya sendiri
                    if(auth()->id() !== $row->id) {
                        $btn .= '<button onclick="deleteUser('.$row->id.')" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('users.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role menggunakan Spatie
        $user->assignRole($request->role);

        return response()->json(['message' => 'User berhasil ditambahkan']);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        // Tambahkan atribut role manual ke response JSON untuk dibaca modal edit
        $user->role = $user->roles->first()->name ?? '';
        
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|min:6'
        ]);

        $user = User::findOrFail($id);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Hanya update password jika field diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        
        // Sync role (hapus role lama, pasang role baru)
        $user->syncRoles([$request->role]);

        return response()->json(['message' => 'User berhasil diperbarui']);
    }

    public function destroy($id)
    {
        if(auth()->id() == $id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri'], 403);
        }

        User::destroy($id);
        return response()->json(['message' => 'User berhasil dihapus']);
    }
}