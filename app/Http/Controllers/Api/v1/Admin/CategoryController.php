<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class CategoryController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->get('module_id');
        $categories = Category::with('subCategories', 'module');
        if ($module_id) {
            $categories = $categories->where('module_id', $module_id);
        }

        return CategoryResource::collection($categories->get());
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Category::rules());
        $category = new Category($validated_data);
        $category->saveImage($request,);
        $category->save();
        return CResponse::success('category is created');
    }

    public function show(Request $request, $id): CategoryResource
    {
        return new CategoryResource(Category::findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_CATEGORY_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('category');
        }
        $category = Category::findOrFail($id);
        $validated_data = $this->validate($request, Category::rules($id));
        $category->saveImage($request);
        $category->update($validated_data);
        $category->save();
        return CResponse::success('category is edited');
    }

    public function removeImage(Request $request, $id): Response|Application|ResponseFactory
    {
        $category = Category::findOrFail($id);
        $category->removeImage();
        $category->save();
        return CResponse::success('Image deleted');
    }

}

