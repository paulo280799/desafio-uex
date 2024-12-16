<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

       try {
            $this->userRepository->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.'
            ], 201);
       } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register user.',
                'error' => $th->getMessage(),
            ], 500);
       }

    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {

            if (Auth::attempt($validated)) {

                $request->user()->tokens()->delete();
                $token = $request->user()->createToken('auth_token',['auth_token'],now()->addHours(1))->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful.',
                    'token' => $token
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $token = Password::createToken(
                $this->userRepository->getUserByEmail($validated['email'])
            );

            Mail::to($validated['email'])->send(new ForgotPassword($token));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email.',
            ]);
        } catch (HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link.',
                'error' => $e->getMessage(),
            ], $e->getStatusCode());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $status = $this->userRepository->resetPassword($validated);

            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'success' => true,
                    'message' => __('Password reset successfully.'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Failed to reset password.'),
                'error' => __($status),
            ], 422);

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
