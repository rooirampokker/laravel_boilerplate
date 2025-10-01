<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use \App\Traits\ResponseTrait;

class UserStoreRequest extends FormRequest
{
    use ResponseTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'      => 'required|email|unique:users',
            'password'   => 'required',
            'c_password' => 'required|same:password',
            'roles'       => 'required',
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(
            $this->error(__('users.store.failed'), $validator->errors()->toArray(), 422),
            422
        ));
    }
}
