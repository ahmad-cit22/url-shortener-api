<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'name.string' => 'Your name should contain only text.',
            'name.max' => 'Your name can be up to 255 characters long.',
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Make sure to enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'A password is required.',
            'password.string' => 'Your password should be text-based.',
            'password.min' => 'Your password should have at least 8 characters.',
            'password.confirmed' => 'Your password confirmation doesn’t match.',
            'password.regex' => 'Password must include at least one lowercase letter, one uppercase letter, one number, and one special character.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
