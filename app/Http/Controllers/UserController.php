<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(15);
        $title = 'Data Pengguna';
        return view('users.index', compact('users', 'title'));
    }

    public function create()
    {
        $title = 'Tambah User';
        return view('users.create', compact('title'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','max:255', Rule::unique((new User)->getTable())],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:user,admin,petugas',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $title = 'Detail User';
        return view('users.show', compact('user', 'title'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $title = 'Edit User';
        return view('users.edit', compact('user', 'title'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tb_user,email,' . $user->id . ',id',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:user,admin,petugas',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}

