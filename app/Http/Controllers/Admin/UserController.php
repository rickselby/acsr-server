<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Socialite\UserProviderStore;
use App\Services\VoiceServer\DiscordVoiceServer;

class UserController extends Controller
{
    /** @var UserProviderStore */
    protected $userProviderStore;

    public function __construct(UserProviderStore $userProviderStore)
    {
        $this->userProviderStore = $userProviderStore;
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
     * Remove a provider from a user
     *
     * @param User $user
     * @param string $provider
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeProvider(User $user, $provider)
    {
        $this->userProviderStore->flush($user, $provider);

        \Notification::add('success', 'Provider "'.ucfirst($provider).'" removed');
        return \Redirect::route('admin.user.edit', $user);
    }

    public function refreshNames()
    {
        \Artisan::call('users:names');
        return \Redirect::route('admin.user.index');
    }
}
