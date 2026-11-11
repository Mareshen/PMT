<?php
namespace Tests\Feature;

use App\Models\Category;
use App\Models\Student;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TrainingApiTest extends TestCase
{
    // DatabaseMigrations runs after the base class has verified SQLite isolation.
    use DatabaseMigrations;

    private function category(): Category
    {
        return Category::firstOrCreate(['name' => 'Laravel']);
    }

    private function task(array $attributes = []): Task
    {
        return Task::create(array_merge(['title' => 'Build API', 'category_id' => $this->category()->id], $attributes));
    }

    private function student(string $email = 'sara@example.test'): Student
    {
        return Student::create(['name' => 'Sara', 'email' => $email]);
    }

    public function test_task_crud_and_category_relationship(): void
    {
        $category = $this->category();
        $id = $this->postJson('/api/tasks', ['title' => 'Build API', 'category_id' => $category->id])
            ->assertCreated()->assertJsonPath('data.status', 'pending')->json('data.id');
        $this->getJson("/api/tasks/$id")->assertOk()->assertJsonPath('data.category.name', 'Laravel');
        $this->patchJson("/api/tasks/$id", ['title' => 'Updated', 'description' => null])
            ->assertOk()->assertJsonPath('data.title', 'Updated');
        $this->getJson("/api/categories/{$category->id}/tasks")->assertOk()->assertJsonPath('data.total', 1);
        $this->assertSame($category->id, Task::findOrFail($id)->category->id);
        $this->assertSame(1, $category->tasks()->count());
        $this->deleteJson("/api/tasks/$id")->assertNoContent();
        $this->getJson("/api/tasks/$id")->assertNotFound();
    }

    public function test_validation_and_invalid_ids(): void
    {
        $this->postJson('/api/tasks', [])->assertUnprocessable()->assertJsonValidationErrors(['title', 'category_id']);
        $this->postJson('/api/tasks', ['title' => 'X', 'category_id' => 999, 'status' => 'blocked'])
            ->assertUnprocessable()->assertJsonValidationErrors(['category_id', 'status']);
        $task = $this->task();
        $this->patchJson("/api/tasks/{$task->id}", ['title' => str_repeat('x', 121)])
            ->assertUnprocessable()->assertJsonValidationErrors('title');
        $this->postJson("/api/tasks/{$task->id}/students", ['student_ids' => [999]])
            ->assertUnprocessable()->assertJsonValidationErrors('student_ids.0');
        $this->getJson('/api/tasks?sort_by=password&sort_dir=wrong&per_page=101&page=0&student_id=999&due_before=bad&status=bad')
            ->assertUnprocessable()->assertJsonValidationErrors(['sort_by', 'sort_dir', 'per_page', 'page', 'student_id', 'due_before', 'status']);
        $this->getJson('/api/tasks/999')->assertNotFound();
        $this->getJson('/api/students/999')->assertNotFound();
        $this->getJson('/api/categories/999')->assertNotFound();
        $this->patchJson('/api/tasks/999/status', ['status' => 'done'])->assertNotFound();
    }

    public function test_combined_filters_search_sort_and_pagination(): void
    {
        $student = $this->student();
        $match = $this->task(['title' => 'Database joins', 'status' => 'pending', 'due_date' => '2026-11-10']);
        $match->students()->attach($student->id, ['assigned_at' => now()]);
        $this->task(['title' => 'CSS', 'status' => 'done', 'due_date' => '2026-11-20']);
        $this->task(['title' => 'Other', 'description' => 'database practice']);
        $this->getJson('/api/tasks?status=pending&category_id='.$match->category_id.'&student_id='.$student->id.'&search=Database&due_after=2026-11-01&due_before=2026-11-10')
            ->assertOk()->assertJsonPath('data.total', 1)->assertJsonPath('data.data.0.id', $match->id);
        $this->getJson('/api/tasks?search=database')->assertOk()->assertJsonPath('data.total', 2);
        $this->getJson('/api/tasks?sort_by=title&sort_dir=asc&per_page=1&page=2')
            ->assertOk()->assertJsonPath('data.total', 3)->assertJsonPath('data.current_page', 2)
            ->assertJsonPath('data.data.0.title', 'Database joins');
        $this->getJson('/api/tasks?sort_by=due_date&sort_dir=desc')->assertOk()->assertJsonPath('data.data.0.title', 'CSS');
    }

    public function test_adding_duplicate_removing_and_replacing_assignments(): void
    {
        $task = $this->task();
        $sara = $this->student();
        $omar = $this->student('omar@example.test');
        $url = "/api/tasks/{$task->id}/students";
        $this->postJson($url, ['student_ids' => [$sara->id]])->assertOk();
        $originalDate = DB::table('student_task')->value('assigned_at');
        $this->postJson($url, ['student_ids' => [$omar->id, $sara->id, $sara->id]])->assertOk();
        $this->assertDatabaseCount('student_task', 2);
        $this->assertDatabaseHas('student_task', ['student_id' => $sara->id, 'assigned_at' => $originalDate]);
        $this->assertSame(1, $sara->tasks()->count());
        $this->getJson("/api/students/{$sara->id}/tasks")->assertOk()->assertJsonPath('data.total', 1);
        $this->putJson($url, ['student_ids' => [$omar->id]])->assertOk();
        $this->assertDatabaseCount('student_task', 1);
        $this->assertDatabaseMissing('student_task', ['student_id' => $sara->id]);
        $this->deleteJson($url.'/'.$omar->id)->assertNoContent();
        $this->deleteJson($url.'/'.$omar->id)->assertNoContent();
        $this->deleteJson($url.'/999')->assertNotFound();
        $this->postJson($url, ['student_ids' => []])->assertUnprocessable();
        $this->putJson($url, [])->assertUnprocessable();
        $this->postJson($url, ['student_ids' => [$sara->id]])->assertOk();
        $this->putJson($url, ['student_ids' => []])->assertOk();
        $this->assertDatabaseCount('student_task', 0);
    }

    public function test_status_and_deletion_rules(): void
    {
        $task = $this->task();
        $student = $this->student();
        $task->students()->attach($student->id, ['assigned_at' => now()]);
        $this->patchJson("/api/tasks/{$task->id}/status", ['status' => 'in_progress'])
            ->assertOk()->assertJsonPath('data.status', 'in_progress');
        $this->patchJson("/api/tasks/{$task->id}/status", ['status' => 'invalid'])
            ->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->deleteJson("/api/tasks/{$task->id}")->assertNoContent();
        $this->assertDatabaseCount('student_task', 0);
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    public function test_unique_email_validation_and_database_constraint(): void
    {
        $student = $this->student();
        $validator = Validator::make(['email' => $student->email], ['email' => 'required|email|unique:students,email']);
        $this->assertTrue($validator->fails());
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->student($student->email);
    }

    public function test_database_rejects_duplicate_assignment_rows(): void
    {
        $task = $this->task();
        $student = $this->student();
        $task->students()->attach($student->id, ['assigned_at' => now()]);
        $this->expectException(\Illuminate\Database\QueryException::class);
        $task->students()->attach($student->id, ['assigned_at' => now()]);
    }

    public function test_category_with_tasks_cannot_be_deleted(): void
    {
        $task = $this->task();
        $this->expectException(\Illuminate\Database\QueryException::class);
        $task->category->delete();
    }

    public function test_deleting_student_preserves_task_and_removes_assignment(): void
    {
        $task = $this->task();
        $student = $this->student();
        $task->students()->attach($student->id, ['assigned_at' => now()]);
        $student->delete();
        $this->assertDatabaseCount('student_task', 0);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_seed_is_deterministic_and_does_not_overwrite_data(): void
    {
        $this->seed();
        $this->assertDatabaseCount('students', 4);
        $this->assertDatabaseCount('tasks', 6);
        Task::first()->update(['title' => 'Student edit']);
        $this->seed();
        $this->assertDatabaseCount('tasks', 6);
        $this->assertDatabaseHas('tasks', ['title' => 'Student edit']);
    }

    public function test_read_endpoints_and_blade(): void
    {
        $student = $this->student();
        $task = $this->task(['title' => '<script>alert(1)</script>']);
        $this->getJson('/api/students')->assertOk();
        $this->getJson("/api/students/{$student->id}")->assertOk();
        $this->getJson('/api/categories')->assertOk();
        $this->getJson("/api/categories/{$task->category_id}")->assertOk();
        $this->get('/tasks')->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }
}
