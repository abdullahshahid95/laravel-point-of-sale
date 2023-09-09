<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Role;
use App\Permission;
use App\PermissionRole;

class UsersController extends Controller
{
    public function index()
    {
        if(!auth()->check() || (auth()->check() && !allowed(18, 'view')))
        {
            return redirect('/');
        }

        $users = User::where('id', '!=', 1)->get();

        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        if(!auth()->check() || (auth()->check() && !allowed(18, 'edit')))
        {
            return redirect('/');
        }

        $roles = Role::where('id', '!=', 1)->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(User $user)
    {
        if(!auth()->check() || (auth()->check() && !allowed(18, 'edit')))
        {
            return redirect('/');
        }
        $data = request()->validate([
            'role_id' => 'required',
            'name' => 'required',
            'username' => 'required',
            'password' => ''
        ]);

        if(!User::where([['username', '=', $data['username']], ['id', '!=', $user->id]])->first())
        {
            if(array_key_exists('password', $data) && $data['password'])
            {
                $data['password'] = Hash::make($data['password']);
            }
            else
            {
                unset($data['password']);
            }

            $user->update($data);
        }

        return redirect('/users')->with(['message' => 'User updated']);
    }

    public function login()
    {
        if(Auth::check())
        {
            return view('home.index');
        }

        $message = "";

        return view('home.login', compact('message'));
    }

    public function checkLogin()
    {
        $data = request()->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if(auth()->attempt($data))
        {
            return redirect('/');
        }

        return redirect('/login')->with(['message' => 'Incorrect Username or Password']);
    }

    public function register()
    {
        if(!auth()->check() || (auth()->check() && !allowed(18, 'make')))
        {
            return redirect('/');
        }

        $roles = Role::where('id', '!=', 1)->get();

        return view('home.register', compact('roles'));
    }

    public function storeUser()
    {
        if(!auth()->check() || (auth()->check() && !allowed(18, 'make')))
        {
            return redirect('/');
        }

        $data = request()->validate([
            'role_id' => 'required',
            'name' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);

        if(!User::where('username', '=', $data['username'])->first())
        {
            $data['password'] = Hash::make($data['password']);

            User::create($data);
        }

        return redirect('/users')->with(['message' => 'User registered']);
    }

    public function logout()
    {
        auth()->logout();
        request()->session()->flush();

        return redirect('/login');
    }

    public function destroy(User $user)
    {
        if(!auth()->check() || (auth()->check() && !allowed(18, 'remove')))
        {
            return 0;
        }

        $user->delete();

        return 1;
    }
}
