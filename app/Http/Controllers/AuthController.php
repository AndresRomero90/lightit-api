<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
        ]);

        $token = $this->authService->register($request->all());

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string']
        ]);

        $credentials = $request->only('email', 'password');
        $token = $this->authService->login($credentials);

        if ($token) {
            return response()->json(['token' => $token]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([], 200);
    }
}
