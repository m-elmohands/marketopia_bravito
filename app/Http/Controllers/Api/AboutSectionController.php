<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutSectionResource;
use App\Repository\Contracts\AboutSectionRepositoryInterface;
use Illuminate\Http\Response;

class AboutSectionController extends Controller
{
    protected $aboutSectionRepository;

    public function __construct(AboutSectionRepositoryInterface $aboutSectionRepository)
    {
        $this->aboutSectionRepository = $aboutSectionRepository;
    }

    /**
     * Display a listing of active about sections.
     */
    public function index()
    {
        $sections = $this->aboutSectionRepository->getOrdered();
        return Response::api(__('message.Success'), 200, true, null, AboutSectionResource::collection($sections));
    }

    /**
     * Display the specified about section.
     */
    public function show($id)
    {
        try {
            $section = $this->aboutSectionRepository->getById($id);
            return Response::api(__('message.Success'), 200, true, null, new AboutSectionResource($section));
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false, $e->getMessage());
        }
    }
}
