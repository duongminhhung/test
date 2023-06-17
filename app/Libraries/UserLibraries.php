<?php

namespace App\Libraries;

use Illuminate\Validation\Rule;
use Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Auth;
use Hash;
use Session;
use DB;
use Carbon\Carbon;

/**
 * 
 */
class UserLibraries
{

    public function get_all_list_user()
    {
        if (Auth::user()->level == 9999) {
            $data = User::where('IsDelete', 0)->where('level', '!=', 9999)
                ->with([
                    'role'
                ])
                ->get();
        } else {
            $data = User::where('IsDelete', 0)->where('level', '!=', 9999)->where('id', Auth::user()->id)
                ->with([
                    'role'
                ])
                ->get();
        }

        return $data;
    }

    public function filter($request)
    {
        $id       = $request->id;
        $name     = $request->name;
        $username = $request->username;
        $data = User::where('IsDelete', 0)
            ->where('level', '!=', 9999)
            ->when($id, function ($q, $id) {
                return $q->where('id', $id);
            })
            ->when($name, function ($q, $name) {
                return $q->where('name', $name);
            })
            ->when($username, function ($q, $username) {
                return $q->where('username', $username);
            })
            ->get();

        return $data;
    }

    public function check_pass($request)
    {
        return Hash::check($request->Password, Auth::user()->password);
    }

    public function check_user($request)
    {
        // dd($request);
        $id       = $request->id;
        $message  = [
            'unique.username'   => $request->username . ' ' . __('Already Exist') . '!',
            'unique.email'      => $request->email . ' ' . __('Already Exist') . '!',
        ];

        $validation = Validator::make(
            $request->all(),
            [
                'name'  => ['required', 'max:255'],
                'username' => [
                    'required',
                    'max:20',
                    Rule::unique('App\Models\User')->where(function ($q) use ($id) {
                        $q->where('id', '!=', $id)->where('IsDelete', 0);
                    }),
                ],
                'email' => [
                    'required',
                    'max:255',
                    'email',
                    Rule::unique('App\Models\User')->where(function ($q) use ($id) {
                        $q->where('id', '!=', $id)->where('IsDelete', 0);
                    }),
                ],
            ],
            $message
        )->validate();

        return $validation;
    }

    public function show($request)
    {
        $id       = $request->id;
        $name     = $request->name;
        $username = $request->username;

        $find = User::where('IsDelete', 0)
            ->where('level', '!=', 9999)
            ->when($id, function ($q, $id) {
                return $q->where('id', $id);
            })
            ->when($name, function ($q, $name) {
                return $q->where('name', $name);
            })
            ->when($username, function ($q, $username) {
                return $q->where('username', $username);
            })
            ->first();

        return $find;
    }

    public function reset_password($request)
    {
        if (Auth::user()->level == 9999 && Auth::user()->username == 'admin') {
            $find = User::where('IsDelete', 0)->where('id', $request->id)->first();

            if ($find) {
                $find->password = bcrypt($request->password);
                $find->save();
            }
        }

        return __('Success');
    }

    public function reset_my_password($request)
    {
        $find = User::where('IsDelete', 0)->where('id', Auth::user()->id)->first();
        if ($find) {
            $find->password = bcrypt($request->password);
            $find->save();
        }
        return __('Success');
    }

    public function add_or_update($request)
    {
        $arr = [];
        $find   = User::where('IsDelete', 0)->where('id', $request->id)->first();
        $status = __('No Action');
        $userrole = UserRole::where('user_id', $request->id)->get();
        foreach ($userrole as $value) {
            array_push($arr, $value->role_id);
        }
        // dd($request->role);
        if ($find) {
            $status = __('Update') . ' ' . __('Account') . ' ' . __('Success');
            if (Auth::user()->level != 9999) {
                // dd('1');
                if ($request->name == $find->name && $request->email == $find->email && $request->username == $find->username) {
                    return (object)[
                        'status'    => $status
                    ];
                }
            }

            $find->name     = $request->name;
            $find->username = $request->username;
            $find->email    = $request->email;
            $find->password = $request->password ? Hash::make($request->password) : $find->password;
            // $find->cache    = 0;
            $find->save();

            if (Auth::user()->level == 9999) {
                if ($request->role) {
                    // dd('run');
                    if (count($arr) < count($request->role)) {
                        if (count(array_diff($arr, $request->role)) == 0) {
                            $find->refresh()->role()->detach();
                            $find->refresh()->role()->attach($request->role);
                        }
                    } else if (count($arr) > count($request->role)) {
                        if (count(array_diff($arr, $request->role)) > 0) {
                            $find->refresh()->role()->detach();
                            $find->refresh()->role()->attach($request->role);
                        }
                    } else {
                        if (count(array_diff($request->role, $arr)) == 0) {
                            // dd(1);
                            return (object)[
                                'status'    => $status,
                            ];
                        } else {
                            // dd(2);
                            $find->refresh()->role()->detach();
                            $find->refresh()->role()->attach($request->role);
                        }
                    }
                } else {
                    $find->refresh()->role()->detach();
                }
            }
            return (object)[
                'status'    => $status
            ];
        } else {
            // dd($request);
            $find = User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'password' => bcrypt('123')
            ]);
            $find = User::where('IsDelete', 0)->where('username', $find->username)->first();
            // dd($find);
            if ($request->role) {
                foreach ($request->role as $value) {
                    $user_role = DB::table('user_role')->insert([
                        'user_id' => $find->id,
                        'role_id' => (int)$value
                    ]);
                    // dd($user_role);
                }
            }
            $status = __('Create') . ' ' . __('Account') . ' ' . __('Success');
            return (object)[
                'status'    => $status,
                'data'        => $find
            ];
        }
    }

    public function destroy($request)
    {
        // dd($request);
        if (Auth::user()->level != 9999) {
            abort(401);
        }
        $find  = User::where('IsDelete', 0)
            ->where('level', '!=', 9999)
            ->where('id', $request->ID)
            ->first();

        $status = __('Account') . ' ' . __('Does Not Exist');

        if ($find) {
            $find->IsDelete = 1;
            $find->save();

            $status = __('Delete') . ' ' . __('Account') . ' ' . __('Success');
        }

        return $status;
    }
}
