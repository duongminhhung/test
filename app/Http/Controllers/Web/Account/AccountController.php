<?php

namespace App\Http\Controllers\Web\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\UserLibraries;
use App\Libraries\RoleLibraries;
use App\Models\MasterData\MasterWarehouse;
use Auth;

class AccountController extends Controller
{
    protected $user;
    protected $role;

    public function __construct(
        UserLibraries $userLibraries,
        RoleLibraries $roleLibraries
    ) {
        $this->middleware('auth');
        $this->user = $userLibraries;
        $this->role = $roleLibraries;
    }

    public function index(Request $request)
    {
        $data  = $this->user->get_all_list_user();
        $roles = $this->role->get_all_list_role();

        return view('account.user.index', [
            'roles'      => $roles,
            'data'        => $data
        ]);
    }

    public function index_role()
    {
        $data = $this->role->get_all_list_role();

        return view('account.role.index', [
            'data' => $data,
            // 'del'  => $this->authDelete(),
            // 'upd'  => $this->authUpdate(),
            // 'cre'  => $this->authCreate(),
        ]);
    }

    public function show(Request $request)
    {
        // dd($request);
        if ($request->id) {
            if (!Auth::user()->id == $request->id && Auth::user()->level != 9999) {
                abort(401);
            }
        }
        $user     = $this->user->filter($request);
        $roles      = $this->role->get_all_list_role();

        if (!$request->id) {
            $user = collect([]);
        }
        // dd($user);

        return view(
            'account.user.add_or_update',
            [
                'show'      => true,
                'roles'      => $roles,
                'data'       => $user->first()
            ]
        );
    }

    public function reset_my_password()
    {
        return view('auth.reset_pass');
    }

    public function check_password(Request $request)
    {
        $data = $this->user->check_pass($request);

        return response()->json([
            'status' => $data,
        ]);
    }

    public function show_role(Request $request)
    {
        // dd($request);
        $roles = $this->role->filter($request);
        if (!$request->id) {
            $roles = collect([]);
        }

        return view('account.role.add_or_update', ['data' => $roles->first()]);
    }

    public function reset_password(Request $request)
    {
        $data = $this->user->reset_password((object)[
            'id'    => $request->idUser,
            'password' => $request->Password
        ]);

        return redirect()->back()->with('success', __('Reset') . ' ' . __('Success'));
    }

    public function reset_new_my_password(Request $request)
    {
        $data = $this->user->reset_my_password((object)[
            'password' => $request->Password
        ]);

        return redirect()->route('home')->with('success',  __('Reset') . ' ' . __('Success'));
    }

    public function add_or_update(Request $request)
    {
        $check = $this->user->check_user($request);
        // dd($check);
        $data  = $this->user->add_or_update($request);
        // dd('OK');
        return redirect()->route('account')->with('success', $data->status);
    }

    public function destroy(Request $request)
    {
        // dd($request);
        $data = $this->user->destroy($request);

        return redirect()->back()->with('danger', $data);
    }

    public function add_or_update_role(Request $request)
    {
        // dd($request);
        $check = $this->role->check_role($request);
        // dd($check);
        $data  = $this->role->add_role($request);

        return redirect()->route('account.role')->with('success', $data->status);
    }

    public function destroy_role(Request $request)
    {
        // dd($request);
        $data = $this->role->destroy($request);

        return redirect()->back()->with('danger', $data);
    }
}
