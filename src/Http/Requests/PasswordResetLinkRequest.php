<?php

namespace Cratespace\Citadel\Http\Requests;

use Cratespace\Citadel\Citadel\Config;
use Illuminate\Foundation\Http\FormRequest;
use Cratespace\Citadel\Http\Requests\Concerns\AuthorizesRequests;

class PasswordResetLinkRequest extends FormRequest
{
    use AuthorizesRequests;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->isGuest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [Config::email() => ['required', 'string', 'email']];
    }
}
