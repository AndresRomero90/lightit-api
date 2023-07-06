<?php

namespace App\Interfaces;

use App\Models\User;

interface AuthServiceInterface
{
    function register(array $userData): string;
    function login(array $credentials): string | null;
    function logout(User $user): void;
}
