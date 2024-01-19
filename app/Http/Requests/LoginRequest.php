<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        $new = $this->boolean('new');

        if ($new) {
            return [
                'new' => 'required|bool',
                'name' => 'required',
                'email' => 'required|email|unique:App\Models\User,email',
            ];
        }

        return [
            'new' => 'required|bool',
            'email' => 'required|email|exists:App\Models\User,email',
        ];
    }

    public function toUser(): User
    {
        if ($this->boolean('new')) {
            return User::query()
                ->create([
                    'email' => $this->input('email'),
                    'name' => $this->input('name'),
                ]);
        }

        return User::whereEmail($this->input('email'))
            ->firstOrFail();
    }
}
