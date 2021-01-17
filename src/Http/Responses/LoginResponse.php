<?php

namespace Citadel\Http\Responses;

use Citadel\Http\Responses\Response;
use Citadel\Limiters\LoginRateLimiter;
use Illuminate\Contracts\Support\Responsable;

class LoginResponse extends Response implements Responsable
{
    /**
     * The login rate limiter instance.
     *
     * @var \Citadel\Limiters\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new class instance.
     *
     * @param  \Citadel\Limiters\LoginRateLimiter  $limiter
     * @return void
     */
    public function __construct(LoginRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $request->session()->regenerate();

        $this->limiter->clear($request);

        return $request->expectsJson()
            ? $this->json(['two_factor' => false])
            : $this->redirectToIntended($this->home(), 302);
    }
}
