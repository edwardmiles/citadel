<?php

namespace Cratespace\Citadel\Http\Controllers;

use Cratespace\Citadel\Jobs\DeleteUserJob;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Cratespace\Citadel\Http\Requests\DeleteUserRequest;
use Cratespace\Citadel\Http\Responses\DeleteUserResponse;
use Cratespace\Citadel\Contracts\Actions\UpdatesUserProfiles;
use Cratespace\Citadel\Http\Requests\UpdateUserProfileRequest;
use Cratespace\Citadel\Http\Responses\UpdateUserProfileResponse;
use Cratespace\Citadel\Contracts\Responses\UserProfileViewResponse;

class UserProfileController extends Controller
{
    /**
     * Show user profile view.
     *
     * @param \Illuminate\Http\Request                             $request
     * @param \Citadel\Contracts\Responses\UserProfileViewResponse $response
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function show(): Responsable
    {
        return $this->app(UserProfileViewResponse::class);
    }

    /**
     * Update the user's profile information.
     *
     * @param \Citadel\Http\Requests\UpdateUserProfileRequest $request
     * @param \Citadel\Contracts\Actions\UpdatesUserProfiles  $updater
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserProfileRequest $request, UpdatesUserProfiles $updater)
    {
        $updater->update($request->user(), $request->validated());

        return $this->app(UpdateUserProfileResponse::class);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\DeleteUserRequest     $request
     * @param \App\Auth\Contracts\DeletesUsers         $deletor
     * @param \Illuminate\Contracts\Auth\StatefulGuard $auth
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function destroy(DeleteUserRequest $request, StatefulGuard $auth): Responsable
    {
        DeleteUserJob::dispatch($request->user()->fresh());

        $auth->logout();

        return $this->app(DeleteUserResponse::class);
    }
}
