<?php

namespace App\Http\Requests;


class UserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => [
                'required',
                'string',
                'unique:users,number,'.$this->route('user')->id,
            ],
        ];
    }
}
