<?php

namespace App\Services;

use App\Interfaces\AuthServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function register(array $userData): string
    {
        $user = User::create([
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'gender' => $userData['gender'],
            'date_of_birth' => $userData['date_of_birth'],
        ]);

        $token = $user->createToken('lightit-spa-token')->plainTextToken;

        return $token;
    }

    public function login(array $credentials): string | null
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('lightit-spa-token')->plainTextToken;

        return $token;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
