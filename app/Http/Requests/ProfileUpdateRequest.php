<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Always true since only authenticated users can reach this
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'username' => [
                'required',
                'string',
                'max:50',
                'lowercase', 
                Rule::unique(User::class, 'username')->ignore($this->user()->id),
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($this->user()->id),
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048',
            ],

            'bio' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom error messages for clarity.
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'Username may only contain letters, numbers, dots, underscores, or hyphens.',
        ];
    }
}
