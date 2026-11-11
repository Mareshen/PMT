<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Students', weight: 1)]
class StudentController extends Controller
{
    /** List students. Returns the class roster in ID order. */
    public function index(): JsonResponse
    {
        return response()->json(['data' => Student::orderBy('id')->paginate(15)]);
    }

    /** View a student. A missing student returns 404. */
    public function show(Student $student): JsonResponse
    {
        return response()->json(['data' => $student]);
    }

    /** List a student's tasks. Includes each task's category and assignment date. */
    public function tasks(Student $student): JsonResponse
    {
        return response()->json(['data' => $student->tasks()->with('category')->orderBy('tasks.id')->paginate(15)]);
    }
}
