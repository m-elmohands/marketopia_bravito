<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TermsOfServiceResource;
use App\Repository\Contracts\TermsOfServiceRepositoryInterface;
use Illuminate\Http\Response;

class TermsOfServiceController extends Controller
{
    protected $termsOfServiceRepository;

    public function __construct(TermsOfServiceRepositoryInterface $termsOfServiceRepository)
    {
        $this->termsOfServiceRepository = $termsOfServiceRepository;
    }

    /**
     * Display a listing of active terms of service.
     */
    public function index()
    {
        $items = $this->termsOfServiceRepository->getOrdered();
        return Response::api(__('message.Success'), 200, true, null, TermsOfServiceResource::collection($items));
    }

    /**
     * Display the specified terms of service by slug.
     */
    public function show($slug)
    {
        try {
            $item = $this->termsOfServiceRepository->getBySlug($slug);
            return Response::api(__('message.Success'), 200, true, null, new TermsOfServiceResource($item));
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false, $e->getMessage());
        }
    }
}
