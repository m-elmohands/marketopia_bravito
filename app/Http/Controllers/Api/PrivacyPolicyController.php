<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrivacyPolicyResource;
use App\Repository\Contracts\PrivacyPolicyRepositoryInterface;
use Illuminate\Http\Response;

class PrivacyPolicyController extends Controller
{
    protected $privacyPolicyRepository;

    public function __construct(PrivacyPolicyRepositoryInterface $privacyPolicyRepository)
    {
        $this->privacyPolicyRepository = $privacyPolicyRepository;
    }

    /**
     * Display a listing of active privacy policies.
     */
    public function index()
    {
        $items = $this->privacyPolicyRepository->getOrdered();
        return Response::api(__('message.Success'), 200, true, null, PrivacyPolicyResource::collection($items));
    }

    /**
     * Display the specified privacy policy by slug.
     */
    public function show($slug)
    {
        try {
            $item = $this->privacyPolicyRepository->getBySlug($slug);
            return Response::api(__('message.Success'), 200, true, null, new PrivacyPolicyResource($item));
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false, $e->getMessage());
        }
    }
}
