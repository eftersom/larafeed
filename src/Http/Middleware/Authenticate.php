<?php

namespace Eftersom\Larafeed\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * The authentication factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  Auth  $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->auth->guard('web')->check()) {
            $this->auth->shouldUse('web');
        } else {
            throw new AuthenticationException(
                'Unauthenticated.', ['web'], route('login')
            );
        }

        return $next($request);
    }


    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}

