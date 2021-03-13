<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUser extends FormRequest
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
        $emailUnique = Rule::unique((new User)->getTable());
        $passwordRequired = 'required';
        $passwordSometimes = '';
        $balance = [];

        if (request()->isMethod('patch')) {
            // we update user, let's ignore its own email
            $emailUnique->ignore($this->route('user'));
            $passwordRequired = 'nullable';
            $passwordSometimes = 'sometimes';
            $balance = ['required', 'numeric'];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $emailUnique],
            'password' => [$passwordSometimes, $passwordRequired, 'string', 'min:8'],
            'roles' => ['required', 'exists:roles,id'],
            'balance' => $balance,
            'mobile' => ['min:', 'max:32'],
        ];
    }
}
