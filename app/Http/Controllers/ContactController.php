<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\GetContactsRequest;
use App\Repositories\ContactsRepository;
use Illuminate\Http\JsonResponse;
use App\Services\GoogleMapsService;
use Exception;

class ContactController extends Controller
{
    public function __construct(
        protected ContactsRepository $contactRepository,
        protected GoogleMapsService $googleMapsService
    ) {
    }

    public function index(GetContactsRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $contacts = $this->contactRepository->getAll($validated);

            return response()->json([
                'success' => true,
                'message' => 'Contacts retrieved successfully.',
                'contacts' => $contacts,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contacts.'
            ], 500);
        }
    }

    public function store(ContactRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $fullAddress = "{$validated['address']}, {$validated['city']}, {$validated['state']}, {$validated['country']}, {$validated['cep']}";

            $coordinates = $this->googleMapsService->getCoordinates($fullAddress);

            $validated['latitude'] = $coordinates['latitude'] ?? null;
            $validated['longitude'] = $coordinates['longitude'] ?? null;

            $contact = $this->contactRepository->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully.',
                'contact' => $contact,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create contact.'
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {

        try {

            $contact = $this->contactRepository->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Contact retrieved successfully.',
                'contact' => $contact,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contact.'
            ], 500);
        }
    }

    public function update(ContactRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            $contact = $this->contactRepository->update($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully.',
                'contact' => $contact,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact.'
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {

            $this->contactRepository->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contact.'
            ], 500);
        }
    }
}
