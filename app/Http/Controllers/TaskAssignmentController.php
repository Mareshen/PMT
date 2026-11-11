<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Task;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

#[Group('Task Assignments', weight: 4)]
class TaskAssignmentController extends Controller
{
    /** Add students. Existing assignments are preserved; repeated IDs are harmless. */
    public function store(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'student_ids' => 'required|array|min:1|max:100',
            'student_ids.*' => 'required|integer|exists:students,id',
        ]);
        DB::transaction(function () use ($task, $data) {
            // Serialize assignment writes for this task, including simultaneous requests.
            $lockedTask = Task::whereKey($task->id)->lockForUpdate()->firstOrFail();
            $existing = $lockedTask->students()->pluck('students.id')->all();
            $newIds = array_diff(array_unique($data['student_ids']), $existing);
            $records = [];
            foreach ($newIds as $id) {
                $records[$id] = ['assigned_at' => now()];
            }
            $lockedTask->students()->syncWithoutDetaching($records);
        });
        return response()->json(['data' => $task->load(['category', 'students'])]);
    }

    /** Replace all students. An empty array removes every assignment. */
    public function update(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'student_ids' => 'present|array|max:100',
            'student_ids.*' => 'required|integer|exists:students,id',
        ]);
        DB::transaction(function () use ($task, $data) {
            $lockedTask = Task::whereKey($task->id)->lockForUpdate()->firstOrFail();
            $existing = $lockedTask->students()->pluck('students.id')->all();
            $records = [];
            foreach (array_unique($data['student_ids']) as $id) {
                // Empty attributes preserve assigned_at for retained students.
                $records[$id] = in_array($id, $existing) ? [] : ['assigned_at' => now()];
            }
            $lockedTask->students()->sync($records);
        });
        return response()->json(['data' => $task->load(['category', 'students'])]);
    }

    /** Remove one assignment. Repeating a removal succeeds; unknown students return 404. */
    public function destroy(Task $task, Student $student): \Illuminate\Http\Response
    {
        DB::transaction(function () use ($task, $student) {
            $lockedTask = Task::whereKey($task->id)->lockForUpdate()->firstOrFail();
            $lockedTask->students()->detach($student->id);
        });
        return response()->noContent();
    }
}
