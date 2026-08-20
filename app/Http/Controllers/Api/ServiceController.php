<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Repository\Contracts\ServiceRepositoryInterface;
use Illuminate\Http\Response;

class ServiceController extends Controller
{
    protected $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Display a listing of active services.
     */
    public function index()
    {
        $services = $this->serviceRepository->getOrdered();
        return Response::api(__('message.Success'), 200, true, null, ServiceResource::collection($services));
    }

    /**
     * Display the specified service by slug.
     */
    public function show($slug)
    {
        try {
            $service = $this->serviceRepository->getBySlug($slug);
            return Response::api(__('message.Success'), 200, true, null, new ServiceResource($service));
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false, $e->getMessage());
        }
    }
}
