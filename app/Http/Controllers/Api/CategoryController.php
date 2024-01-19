<?php

namespace App\Http\Controllers\Api;

use App\Events\CategoryUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function list(Request $request): ResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return CategoryResource::collection($user->categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws AuthorizationException
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $category = new Category([
            'order' => $user->categories->pluck('order')->max() + 1,
            ...$request->validated(),
        ]);

        $user->categories()->save($category);

        return CategoryResource::make($category->refresh())
            ->toResponse($request)
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws AuthorizationException
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResource
    {
        $category->update($request->validated());

        return CategoryResource::make($category->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws AuthorizationException
     */
    public function destroy(Category $category): Response
    {
        $this->authorize('delete', $category);

        CategoryUpdated::dispatch($category);

        $category->delete();

        return response()->noContent();
    }
}
