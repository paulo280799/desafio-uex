<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressSearchRequest;
use Illuminate\Http\JsonResponse;
use App\Services\ViaCepService;

class ViaCepController extends Controller
{
    public function __construct(
        protected ViaCepService $viaCepService
    ) {}

    public function getAddresses(AddressSearchRequest $request): JsonResponse
    {
        try {

            $cep = $request->input('cep');

            $data = $this->viaCepService->getAddresses($cep);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error when querying addresses.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
