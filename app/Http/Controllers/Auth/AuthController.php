<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Socialite\Auth\Auth;

class AuthController extends Controller
{
    /** @var Auth */
    protected $socialiteAuth;

    public function __construct(Auth $socialiteAuth)
    {
        $this->socialiteAuth = $socialiteAuth;
        $this->middleware('validateProvider')->except('logout');
        $this->middleware('auth')->only('destroy', 'logout');
    }

    /**
     * Send a request to try to login
     *
     * @param $provider
     *
     * @return mixed
     */
    public function redirectToProvider($provider)
    {
        // Save the page we came from
        \Session::put('url.intended', \Redirect::getUrlGenerator()->previous());
        return \Socialite::driver($provider)->redirect();
    }

    /**
     * Deal with the return from a provider
     *
     * @param $provider
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        $this->socialiteAuth->handleProviderCallback($provider);
        if (\Auth::user()->new) {
            return \Redirect::route('user.logins');
        } else {
            return \Redirect::intended();
        }
    }

    /**
     * Remove a provider from the current user
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($provider)
    {
        $this->socialiteAuth->destroy($provider);
        return \Redirect::route('user.logins');
    }

    /**
     * Log out of the system
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        \Auth::logout();
        return \Redirect::route('home');
    }

}
