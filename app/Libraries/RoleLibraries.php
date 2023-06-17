<?php

namespace App\Libraries;

use App\Models\Role;

/**
 * 
 */

use Validator;
use Illuminate\Validation\Rule;
use Auth;

class RoleLibraries
{
    public function get_all_list_role()
    {
        $data = Role::where('isdelete', 0)->get();

        return $data;
    }

    public function filter($request)
    {
        $id       = $request->id;
        $role     = $request->role;
        $username = $request->username;
        $data = Role::where('isdelete', 0)
        ->when($id, function ($q, $id) {
            return $q->where('id', $id);
        })
        ->when($role, function ($q, $role) {
            return $q->where('role', $role);
        })
        ->get();

        return $data;
    }


    public function check_role($request)
    {
        // dd($request);
        $id       = $request->id;
        $message  = [
            'unique.username'   => $request->username . ' ' . __('Already Exist') . '!',
            'unique.email'      => $request->email . ' ' . __('Already Exist') . '!',
        ];

        $validation = Validator::make($request->all(), [
            'role'  => ['required', 'max:255'],
            'role' => [
                'required',
                'max:255',
                Rule::unique('App\Models\Role')->where(function ($q) use ($id) {
                    $q->where('id', '!=', $id)->where('isdelete', 0);
                }),
            ]
        ], $message)->validate();

        return $validation;
    }

    public function add_role($request)
    {
        $find = Role::create([
            'role'          => $request->role,
            'description'   => $request->des,
            'note'          => $request->note,
            'user_created'  => Auth::user()->id,
            'user_updated'  => Auth::user()->id,
        ]);

        $status = __('Create') . ' ' . __('Role') . ' ' . __('Success');
        return (object)[
            'status' => $status
        ];
    }

    public function destroy($request)
    {
        // dd($request->ID);
        Role::where('id', $request->ID)->update([
            'isdelete'         => 1,
            'User_Updated'    => Auth::user()->id
        ]);

        return __('Delete') . ' ' . __('Success');
    }
}
