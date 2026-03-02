<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class BannedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_banned) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'malheuresement votre compte est banni.']);
        }

        return $next($request);
    }
}