<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Repository\Contracts\FaqRepositoryInterface;
use Illuminate\Http\Response;

class FaqController extends Controller
{
    protected $faqRepository;

    public function __construct(FaqRepositoryInterface $faqRepository)
    {
        $this->faqRepository = $faqRepository;
    }

    /**
     * Display a listing of active FAQs.
     */
    public function index()
    {
        $faqs = $this->faqRepository->getOrdered();
        $grouped = $faqs->groupBy('category');
        
        return Response::api(__('message.Success'), 200, true, null, [
            'faqs' => FaqResource::collection($faqs),
            'grouped_by_category' => $grouped->map(function ($items) {
                return FaqResource::collection($items);
            })
        ]);
    }

    /**
     * Display the specified FAQ.
     */
    public function show($id)
    {
        try {
            $faq = $this->faqRepository->getById($id);
            return Response::api(__('message.Success'), 200, true, null, new FaqResource($faq));
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false, $e->getMessage());
        }
    }

    /**
     * Get all FAQ categories.
     */
    public function categories()
    {
        $categories = $this->faqRepository->getCategories();
        return Response::api(__('message.Success'), 200, true, null, $categories);
    }
}
