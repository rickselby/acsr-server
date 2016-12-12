<?php

namespace App\Http\Controllers;

use Httpful\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function logins()
    {
        return view('user.logins')
            ->with('user', \Auth::user());
    }

    public function settings()
    {
        return view('user.settings')
            ->with('user', \Auth::user());
    }

    public function updateSettings(\Illuminate\Http\Request $request)
    {
        \Auth::user()->timezone = $request->get('timezone');
        \Auth::user()->save();
        \Notification::add('success', 'Timezone updated.');
        return \Redirect::route('user.settings');
    }
}
