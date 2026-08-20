<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShippingReturnResource;
use App\Repository\Contracts\ShippingReturnRepositoryInterface;
use Illuminate\Http\Response;

class ShippingReturnController extends Controller
{
    protected $shippingReturnRepository;

    public function __construct(ShippingReturnRepositoryInterface $shippingReturnRepository)
    {
        $this->shippingReturnRepository = $shippingReturnRepository;
    }

    /**
     * Display a listing of active shipping & return policies.
     */
    public function index()
    {
        $items = $this->shippingReturnRepository->getOrdered();
        return Response::api(__('message.Success'), 200, true, null, ShippingReturnResource::collection($items));
    }

    /**
     * Display the specified shipping & return policy by slug.
     */
    public function show($slug)
    {
        try {
            $item = $this->shippingReturnRepository->getBySlug($slug);
            return Response::api(__('message.Success'), 200, true, null, new ShippingReturnResource($item));
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false, $e->getMessage());
        }
    }
}
