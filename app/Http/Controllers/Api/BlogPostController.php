<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Repository\Contracts\BlogPostRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BlogPostController extends Controller
{
    protected $blogPostRepository;

    public function __construct(BlogPostRepositoryInterface $blogPostRepository)
    {
        $this->blogPostRepository = $blogPostRepository;
    }

    /**
     * Display a listing of published blog posts.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 12);
        $posts = $this->blogPostRepository->getPublished();
        $paginatedPosts = $posts->paginate($perPage);
        
        return Response::api(__('message.Success'), 200, true, null, [
            'data' => BlogPostResource::collection($paginatedPosts->items()),
            'pagination' => [
                'current_page' => $paginatedPosts->currentPage(),
                'last_page' => $paginatedPosts->lastPage(),
                'per_page' => $paginatedPosts->perPage(),
                'total' => $paginatedPosts->total(),
            ]
        ]);
    }

    /**
     * Display the specified blog post by slug.
     */
    public function show($slug)
    {
        try {
            $post = $this->blogPostRepository->getBySlug($slug);
            $this->blogPostRepository->incrementViews($post->id);
            return Response::api(__('message.Success'), 200, true, null, new BlogPostResource($post));
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false, $e->getMessage());
        }
    }

    /**
     * Display blog posts by category.
     */
    public function byCategory($categoryId)
    {
        $posts = $this->blogPostRepository->getByCategory($categoryId);
        return Response::api(__('message.Success'), 200, true, null, BlogPostResource::collection($posts));
    }

    /**
     * Display featured blog posts.
     */
    public function featured()
    {
        $posts = $this->blogPostRepository->getFeatured();
        return Response::api(__('message.Success'), 200, true, null, BlogPostResource::collection($posts));
    }
}
