<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil ID user yang sedang login
        $userId = $this->user()->user_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                Rule::unique(User::class)->ignore($userId, 'user_id'), 
            ],

            'no_hp' => [
                'required', 
                'string', 
                'max:15',
                Rule::unique(User::class)->ignore($userId, 'user_id'),
            ],
        ];
    }
}
