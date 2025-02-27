<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * All categories
     */
    public function index(Request $request): JsonResponse
    {
        // Check if the request is authenticated

        // Fetch all categories from the database
        $categories = Category::all();

        return response()->json($categories, 200);
    }
}
