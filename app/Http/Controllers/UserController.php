<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
   public function index()
   {
      $users = User::with('roles')->get();
      $roles = Role::all();
      return view('users.index', compact('users', 'roles'));
   }

   public function edit(User $user)
   {
      return view('users.edit', compact('user'));
   }

   public function update(Request $request, User $user)
   {
      $user->update($request->only('name', 'email'));
      return redirect()->route('user')->with('success', 'User updated.');
   }

   public function destroy(User $user)
   {
      $user->update(['email_verified_at' => null]); // Bisa dianggap sebagai nonaktif
      return redirect()->route('user')->with('success', 'User deactivated.');
   }

   public function assignRole(Request $request, User $user)
   {
      $user->syncRoles($request->role);
      return redirect()->route('user')->with('success', 'Role updated.');
   }

   public function getData()
   {
      $users = User::with('roles')->get();
      return response()->json(['data' => $users]);
   }

   public function show(User $user)
   {
      return response()->json($user);
   }
}
