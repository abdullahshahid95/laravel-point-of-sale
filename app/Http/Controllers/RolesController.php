<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Role;
use App\Permission;
use App\PermissionRole;

class RolesController extends Controller
{
    public function index()
    {
        if(!auth()->check() || (auth()->check() && !allowed(19, 'view')))
        {
            return redirect('/');
        }

        $roles = Role::where('id', '!=', 1)->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        if(!auth()->check() || (auth()->check() && !allowed(19, 'make')))
        {
            return redirect('/');
        }
        $permissions = Permission::all();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        if(!auth()->check() || (auth()->check() && !allowed(19, 'make')))
        {
            return 0;
        }

        $data = json_decode($request->getContent(), true);

        $role = Role::create(['name' => $data['name']]);

        for ($i = 0; $i < sizeof($data['permissions']); $i++) 
        { 
            PermissionRole::create([
                'permission_id' => $data['permissions'][$i]['permission_id'],
                'role_id' => $role->id,
                'view' => $data['permissions'][$i]['view'],
                'make' => $data['permissions'][$i]['make'],
                'edit' => $data['permissions'][$i]['edit'],
                'remove' => $data['permissions'][$i]['remove']
            ]);
        }

        return 1;
        return redirect('/roles')->with(['message' => 'Role added.']);
    }

    public function edit(Role $role)
    {
        if(!auth()->check() || (auth()->check() && !allowed(19, 'edit')))
        {
            return redirect('/');
        }
        $permissions = Permission::all();
        $permissionsRoles = PermissionRole::all();
        $roles = Role::all();

        return view('roles.edit', compact('permissions', 'permissionsRoles', 'roles', 'role'));
    }

    public function update(Request $request, Role $role)
    {
        if(!auth()->check() || (auth()->check() && !allowed(19, 'edit')))
        {
            return 0;
        }

        $data = json_decode($request->getContent(), true);

        $role->update(['name' => $data['name']]);

        for ($i = 0; $i < sizeof($data['permissions']); $i++) 
        {
            if(PermissionRole::where([['permission_id', '=', $data['permissions'][$i]['permission_id']], 
            ['role_id', '=', $role->id]])->first())
            {
                PermissionRole::where([['permission_id', '=', $data['permissions'][$i]['permission_id']], 
                ['role_id', '=', $role->id]])
                                    ->update([
                                        'view' => $data['permissions'][$i]['view'],
                                        'make' => $data['permissions'][$i]['make'],
                                        'edit' => $data['permissions'][$i]['edit'],
                                        'remove' => $data['permissions'][$i]['remove']
                                    ]);
            }
            else
            {
                PermissionRole::create(['permission_id' => $data['permissions'][$i]['permission_id'],
                                        'role_id' => $role->id,
                                        'view' => $data['permissions'][$i]['view'],
                                        'make' => $data['permissions'][$i]['make'],
                                        'edit' => $data['permissions'][$i]['edit'],
                                        'remove' => $data['permissions'][$i]['remove']]);
            }
        }

        return 1;
    }

    public function destroy(Role $role)
    {
        if(!auth()->check() || (auth()->check() && !allowed(19, 'remove')))
        {
            return 0;
        }

        DB::delete("DELETE FROM users WHERE role_id = $role->id");
        DB::delete("DELETE FROM permission_role WHERE role_id = $role->id");

        $role->delete();

        return 1;
    }
}
