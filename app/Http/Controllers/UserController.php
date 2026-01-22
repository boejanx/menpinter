<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Services\LogService;

class UserController extends Controller
{
   public function index()
   {
      $users = User::with('roles')->get();
      $roles = Role::select('name')->get();
      return view('users.index', compact('users', 'roles'));
   }

   public function edit(User $user)
   {
      return view('users.edit', compact('user'));
   }

   public function update(Request $request, User $user)
   {
      $data = $request->validate([
         'name' => 'required|string|max:255',
         'email' => 'required|email|max:255|unique:users,email,' . $user->id,
      ]);
      $user->update($data);
      return response()->json([
         'message' => 'User updated.',
         'user' => $user->load('roles'),
      ]);
   }

   public function destroy(User $user)
   {
      LogService::log('Status User', 'User', 'Menonaktifkan user dengan nama ' . $user->name);

      $user->update(['is_active' => 0]);
      return response()->json(['message' => 'User deactivated.']);
   }

   public function assignRole(Request $request, User $user)
   {
      $validated = $request->validate([
         'role' => 'required|string|exists:roles,name',
      ]);
      $user->syncRoles([$validated['role']]);
      return response()->json([
         'message' => 'Role updated.',
         'roles' => $user->roles()->get(['name']),
      ]);
   }

   public function activate(User $user)
   {
      $user->update(['is_active' => 1]);
      return response()->json(['message' => 'User activated.']);
   }

   public function getData()
   {
      // Ambil semua user (aktif dan tidak aktif)
      $users = User::with('roles')->get();
      return response()->json(['data' => $users]);
   }

   public function show(User $user)
   {
      return response()->json($user->load('roles'));
   }
}
