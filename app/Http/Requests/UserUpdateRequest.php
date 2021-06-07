<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UserUpdateRequest extends Request
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
        if ($this->method() == 'PUT') {
            return [
                'email' => 'email|required|unique:mysql.users,email,' . $this->user,
            ];
        }
        return [
            'email' => 'email|required|unique:mysql.users',
            'password' => 'required|confirmed'
        ];
    }
}
