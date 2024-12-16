<?php

namespace App\Http\Controllers;

use Exception;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DeleteAccountRequest;

class AccountController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository
    ) {
    }

    public function me(): JsonResponse
    {
        try {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(DeleteAccountRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            $this->userRepository->delete($user->id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
