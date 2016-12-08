<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user-admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.user.index')
            ->with('users', User::with('providers')->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.user.edit')
            ->with('user', $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // This is here to be updated later; don't delete a user that has results?
        // If we care about keeping results?
        if (false) {
            \Notification::add('error', 'User "'.$user->name.'" cannot be deleted - [reason]');
            return \Redirect::route('admin.user.index');
        } else {
            $user->delete();
            \Notification::add('success', 'User "'.$user->name.'" deleted');
            return \Redirect::route('admin.user.index');
        }
    }

    /**
     * Add a user to a role
     *
     * @param Request $request
     * @param Role $role
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addUser(Request $request, Role $role)
    {
        $user = User::findOrFail($request->get('user'));
        $user->assignRole($role);
        \Notification::add('success', 'User "'.$user->name.'" added');
        return \Redirect::route('role.show', $role);
    }

    /**
     * Remove a user from a role
     *
     * @param Role $role
     * @param User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeUser(Role $role, User $user)
    {
        $user->removeRole($role);
        \Notification::add('success', 'User "'.$user->name.'" removed');
        return \Redirect::route('role.show', $role);
    }

    /**
     * Add a permission to a role
     *
     * @param Request $request
     * @param Role $role
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addPermission(Request $request, Role $role)
    {
        $permission = Permission::findOrFail($request->get('permission'));
        $role->givePermissionTo($permission);
        \Notification::add('success', 'Permission "'.$permission->name.'" added');
        return \Redirect::route('role.show', $role);
    }

    /**
     * Remove a permission from a role
     *
     * @param Role $role
     * @param Permission $permission
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removePermission(Role $role, Permission $permission)
    {
        $role->revokePermissionTo($permission);
        \Notification::add('success', 'Permission "'.$permission->name.'" removed');
        return \Redirect::route('role.show', $role);
    }
}