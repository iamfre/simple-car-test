<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateApi extends Middleware
{
    public function authenticate($request, array $guards): void
    {
        $token = $request->query('api_token');

        if (empty($token)) {
            $token = $request->input('api_token');
        }

        if (empty($token)) {
            $token = $request->bearerToken();
        }

        if ($token === env('API_TOKEN')) {
            return;
        }

        $this->unauthenticated($request, $guards);
    }
}
