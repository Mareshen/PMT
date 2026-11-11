<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Student;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskPageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::with(['category', 'students']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('student_id')) {
            $query->whereHas('students', function ($q) use ($request) {
                $q->where('students.id', $request->student_id);
            });
        }

        $tasks = $query->orderBy('id')->paginate(10)->withQueryString();
        $categories = Category::orderBy('id')->get();
        $students = Student::orderBy('id')->get();

        return view('tasks.index', compact('tasks', 'categories', 'students'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('id')->get();

        return view('tasks.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,done',
            'category_id' => 'required|exists:categories,id',
        ]);

        Task::create($data);

        return redirect('/tasks')->with('success', 'Task created successfully.');
    }

    public function show(Task $task): View
    {
        $task->load(['category', 'students']);
        $students = Student::orderBy('id')->get();

        return view('tasks.show', compact('task', 'students'));
    }

    public function edit(Task $task): View
    {
        $categories = Category::orderBy('id')->get();

        return view('tasks.edit', compact('task', 'categories'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,done',
            'category_id' => 'required|exists:categories,id',
        ]);

        $task->update($data);

        return redirect("/tasks/{$task->id}")->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect('/tasks')->with('success', 'Task deleted successfully.');
    }

    public function assign(Request $request, Task $task): RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $task->students()->syncWithoutDetaching([
            $request->student_id => ['assigned_at' => now()],
        ]);

        return redirect("/tasks/{$task->id}")->with('success', 'Student assigned successfully.');
    }

    public function unassign(Task $task, Student $student): RedirectResponse
    {
        $task->students()->detach($student->id);

        return redirect("/tasks/{$task->id}")->with('success', 'Student unassigned successfully.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $task->update($data);

        return back()->with('success', 'Task status updated.');
    }
}
