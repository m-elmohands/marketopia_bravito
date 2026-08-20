<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NavigationMenuResource;
use App\Repository\Contracts\NavigationMenuRepositoryInterface;
use Illuminate\Http\Response;

class NavigationMenuController extends Controller
{
    protected $navigationMenuRepository;

    public function __construct(NavigationMenuRepositoryInterface $navigationMenuRepository)
    {
        $this->navigationMenuRepository = $navigationMenuRepository;
    }

    /**
     * Display a listing of all active navigation menus.
     */
    public function index()
    {
        $menus = $this->navigationMenuRepository->getOrdered();
        return Response::api(__('message.Success'), 200, true, null, NavigationMenuResource::collection($menus));
    }

    /**
     * Display navigation menu tree by location.
     */
    public function byLocation($location)
    {
        if (!in_array($location, ['header', 'footer', 'sidebar'])) {
            return Response::api(__('message.Invalid location'), 400, false, 'Location must be header, footer, or sidebar');
        }

        $menuTree = $this->navigationMenuRepository->getMenuTree($location);
        return Response::api(__('message.Success'), 200, true, null, NavigationMenuResource::collection($menuTree));
    }
}
