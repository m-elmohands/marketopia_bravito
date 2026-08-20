<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Repository\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Response;

class ContactMessageController extends Controller
{
    protected $contactMessageRepository;

    public function __construct(ContactMessageRepositoryInterface $contactMessageRepository)
    {
        $this->contactMessageRepository = $contactMessageRepository;
    }

    /**
     * Store a newly created contact message in storage.
     * Public endpoint - no authentication required.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return Response::api($validator->errors()->first(), 422, false);
        }

        try {
            $contactMessage = $this->contactMessageRepository->create($request->all());

            return Response::api(
                __('message.Contact message sent successfully'),
                201,
                true,
                null,
                new ContactMessageResource($contactMessage)
            );
        } catch (\Exception $e) {
            return Response::api(__('message.Failed to send message'), 500, false);
        }
    }
}
