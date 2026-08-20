<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Repository\Contracts\TestimonialRepositoryInterface;
use Illuminate\Http\Response;

class TestimonialController extends Controller
{
    protected $testimonialRepository;

    public function __construct(TestimonialRepositoryInterface $testimonialRepository)
    {
        $this->testimonialRepository = $testimonialRepository;
    }

    /**
     * Display a listing of active testimonials.
     */
    public function index()
    {
        $testimonials = $this->testimonialRepository->getOrdered();
        return Response::api(__('message.Success'), 200, true, null, TestimonialResource::collection($testimonials));
    }

    /**
     * Display featured testimonials.
     */
    public function featured()
    {
        $testimonials = $this->testimonialRepository->getFeatured();
        return Response::api(__('message.Success'), 200, true, null, TestimonialResource::collection($testimonials));
    }

    /**
     * Display the specified testimonial.
     */
    public function show($id)
    {
        try {
            $testimonial = $this->testimonialRepository->getById($id);
            return Response::api(__('message.Success'), 200, true, null, new TestimonialResource($testimonial));
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false, $e->getMessage());
        }
    }
}
