<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'phone'   => 'nullable|numeric|regex:/(^1[3456789][0-9]{9}$)/',
            'username'  => 'required|min:2|max:14',
            'password'  => 'required|confirmed|min:2|max:14'
        ];
    }
}
