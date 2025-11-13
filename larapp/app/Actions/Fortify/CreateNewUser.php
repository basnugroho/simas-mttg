<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password; 

use Laravel\Fortify\Contracts\CreatesNewUsers;


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
        Validator::make($input, [
        'name' => ['required','string','max:255'],
        'username' => ['required','alpha_dash','max:255','unique:'.User::class],
        'email' => ['nullable','email','max:255','unique:'.User::class], // opsional
        'password' => ['required', Password::defaults(), 'confirmed'],
        ])->validate();

        // Determine role/approval: first registered user becomes 'webmaster' and is approved.
        $isFirst = User::count() === 0;

        $user = User::create([
            'name' => $input['name'],
            'username' => $input['username'],
            'email' => $input['email'] ?? null,
            'password' => Hash::make($input['password']),
            'role' => $isFirst ? 'webmaster' : 'user',
            'approved' => $isFirst ? true : false,
        ]);

        return $user;
    }
}
