<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Categories', weight: 2)]
class CategoryController extends Controller
{
    /** List categories. Returns categories in ID order. */
    public function index(): JsonResponse
    {
        return response()->json(['data' => Category::orderBy('id')->paginate(15)]);
    }

    /** View a category. A missing category returns 404. */
    public function show(Category $category): JsonResponse
    {
        return response()->json(['data' => $category]);
    }

    /** List category tasks. Includes assigned students. */
    public function tasks(Category $category): JsonResponse
    {
        return response()->json(['data' => $category->tasks()->with('students')->orderBy('id')->paginate(15)]);
    }
}
