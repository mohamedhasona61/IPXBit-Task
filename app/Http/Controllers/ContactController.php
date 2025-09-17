<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tenant\StoreContactRequest;
use App\Services\ContactService;
use App\Traits\ApiResponseTrait;

class ContactController extends Controller
{
    use ApiResponseTrait;

    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index()
    {
        $contacts = $this->contactService->listContacts();
        return $this->successResponse(200, 'Contacts retrieved successfully', $contacts);
    }

    public function store(StoreContactRequest $request)
    {
        try {
            $contact = $this->contactService->createContact($request->validated());
            return $this->successResponse(201, 'Contact created successfully', $contact);
        } catch (\Exception $e) {
            return $this->errorResponse(500, 'Failed to create contact: ' . $e->getMessage());
        }
    }
}
