<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        //verif d'admin global
        if ($role === 'admin' && !$user->is_admin) {
            abort(403, 'Accès réservé à l\'administrateur global.');
        }
        // verification de owner
        if ($role === 'owner') {
            $membership = $user->activeMembership;
            if (!$membership || $membership->role !== 'owner') {
                abort(403, 'Accès réservé au propriétaire de la colocation.');
            }
        }
        // verif membre
        if ($role === 'member') {
            $membership = $user->activeMembership;
            if (!$membership) {
                abort(403, 'Vous n\'appartenez à aucune colocation active.');
            }
        }

        return $next($request);
    }
}
