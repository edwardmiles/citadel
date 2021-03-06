<?php

namespace Cratespace\Citadel\Http\Controllers;

use Cratespace\Citadel\Citadel\Config;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Cratespace\Citadel\Http\Requests\VerifyEmailRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param \Citadel\Http\Requests\VerifyEmailRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(VerifyEmailRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(Config::home() . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(Config::home() . '?verified=1');
    }
}
