<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUser;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        Gate::authorize('users-index');

        $users = User::whereEmail($request->query('email') ? "%{$request->query('email')}%" : null)
            ->whereRoleIdIn($request->query('roles'))
            ->latest()
            ->paginate(15)
            ->onEachSide(1);

        $roles = Role::all();

        $allCount = User::all()->count();
        $activeCount = User::whereActive(1)->count();
        $inactiveCount = User::whereActive(0)->count();
        $userRoleCount = User::whereHas('roles', function($query) {
            $query->where('slug', 'user');
        })->count();

        return view('users.index',
            compact('users', 'roles', 'allCount', 'activeCount', 'inactiveCount', 'userRoleCount')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('users-create');

        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreUser $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUser $request)
    {
        Gate::authorize('users-create');

        $role = Role::findOrFail($request->roles);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->mobile = $request->mobile;
        $user->balance  = 50000;
        $user->save();

        $user->syncAllRolesAndPermission($role->slug);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        Gate::authorize('users-update');

        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreUser $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(StoreUser $request, $id)
    {
        Gate::authorize('users-update');

        $user = User::findOrFail($id);
        $role = Role::findOrFail($request->roles);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->balance = $request->balance;
        $user->active = $request->has('active') ? 1 : 0;
        if ($request->has('password') && $request->password != '')
            $user->password = Hash::make($request->password);

        $user->save();
        $user->syncAllRolesAndPermission($role->slug);

        $roles = Role::all();

        toast()->success('User has been updated');

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
