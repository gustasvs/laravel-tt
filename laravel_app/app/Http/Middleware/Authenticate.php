<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
            if (Auth::guest()) {
                return route('guest.home'); // Replace 'guest.home' with the route name for your reduced home page for guests
            }
            return route('login');
        }
    }

}
