<?php

namespace App\Http\Middleware;

use App\Services\Socialite\Providers;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ValidateProvider
{
    public function handle($request, $next)
    {
        $providers = app(Providers::class);
        if ($providers->verify($request->route('provider'))) {
            return $next($request);
        } else {
            throw new NotFoundHttpException();
        }
    }
}
