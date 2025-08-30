<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Détermine où rediriger si l’utilisateur n’est pas authentifié.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return response()->json([
                'message' => 'Non authentifié'
            ], 401);
        }
    }
}
