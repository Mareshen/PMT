<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Tasks', weight: 3)]
class TaskController extends Controller
{
    /**
     * List and filter tasks.
     * Combine filters with AND. Dates are inclusive; search matches title or description.
     * Sorting defaults to ID ascending. Pagination defaults to 15 and is limited to 100.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'status' => 'sometimes|required|in:pending,in_progress,done',
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'student_id' => 'sometimes|required|integer|exists:students,id',
            'search' => 'sometimes|required|string|max:120',
            'due_before' => 'sometimes|required|date_format:Y-m-d',
            'due_after' => 'sometimes|required|date_format:Y-m-d',
            'sort_by' => 'sometimes|required|in:id,title,due_date,status,created_at',
            'sort_dir' => 'sometimes|required|in:asc,desc',
            'page' => 'sometimes|required|integer|min:1',
            'per_page' => 'sometimes|required|integer|min:1|max:100',
        ]);
        $query = Task::with(['category', 'students']);
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (isset($filters['student_id'])) {
            $query->whereHas('students', fn ($students) => $students->where('students.id', $filters['student_id']));
        }
        if (isset($filters['search'])) {
            $query->where(function ($text) use ($filters) {
                $text->where('title', 'like', '%'.$filters['search'].'%')
                    ->orWhere('description', 'like', '%'.$filters['search'].'%');
            });
        }
        if (isset($filters['due_before'])) {
            $query->whereDate('due_date', '<=', $filters['due_before']);
        }
        if (isset($filters['due_after'])) {
            $query->whereDate('due_date', '>=', $filters['due_after']);
        }
        $sort = $filters['sort_by'] ?? 'id';
        $query->orderBy($sort, $filters['sort_dir'] ?? 'asc');
        if ($sort !== 'id') {
            $query->orderBy('id');
        }
        return response()->json(['data' => $query->paginate($filters['per_page'] ?? 15)->withQueryString()]);
    }

    /** Create a task. Status defaults to pending; assignments use a separate endpoint. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'sometimes|required|in:pending,in_progress,done',
            'category_id' => 'required|integer|exists:categories,id',
        ]);
        $task = Task::create($data);
        return response()->json(['data' => $task->refresh()->load(['category', 'students'])], 201);
    }

    /** View a task. Includes its category and all assigned students. */
    public function show(Task $task): JsonResponse
    {
        return response()->json(['data' => $task->load(['category', 'students'])]);
    }

    /** Update task fields. Omitted fields remain unchanged; nullable fields may be cleared. */
    public function update(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:120',
            'description' => 'sometimes|nullable|string',
            'due_date' => 'sometimes|nullable|date',
            'status' => 'sometimes|required|in:pending,in_progress,done',
            'category_id' => 'sometimes|required|integer|exists:categories,id',
        ]);
        $task->update($data);
        return response()->json(['data' => $task->load(['category', 'students'])]);
    }

    /** Delete a task. Foreign keys also remove its assignments; students remain. */
    public function destroy(Task $task): \Illuminate\Http\Response
    {
        $task->delete();
        return response()->noContent();
    }
}
