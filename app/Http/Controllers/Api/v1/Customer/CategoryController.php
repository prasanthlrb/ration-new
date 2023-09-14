<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
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
        $module_id = $request->query('module_id');
        $categories = Category::with('subCategories');
        if($module_id!=null){
            $categories->where('module_id', $module_id);
        }
        return CategoryResource::collection($categories->get());
    }

}

