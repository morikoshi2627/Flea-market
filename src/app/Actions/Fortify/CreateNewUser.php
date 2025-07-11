<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $request = app(RegisterRequest::class);
        $request->merge($input);
        $request->validateResolved();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // // バリデーション
        // Validator::make($input, [
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => $this->passwordRules(),

        //     'profile_image' => ['nullable', 'string', 'max:255'],
        //     'postal_code' => ['nullable', 'string', 'max:20'],
        //     'address' => ['nullable', 'string', 'max:255'],
        //     'building' => ['nullable', 'string', 'max:255'],
        // ])->validate();

        // // 保存処理
        // return User::create([
        //     'name' => $input['name'],
        //     'email' => $input['email'],
        //     'password' => Hash::make($input['password']),

        //     'profile_image' => $input['profile_image'] ?? null,
        //     'postal_code' => $input['postal_code'] ?? null,
        //     'address' => $input['address'] ?? null,
        //     'building' => $input['building'] ?? null,
        // ]);
    }
}
