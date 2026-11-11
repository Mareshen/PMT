<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Task Status', weight: 5)]
class TaskStatusController extends Controller
{
    /** Change overall task status. Allowed values: pending, in_progress, done. */
    public function update(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate(['status' => 'required|in:pending,in_progress,done']);
        $task->update($data);
        return response()->json(['data' => $task->load(['category', 'students'])]);
    }
}
