<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Student;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Refuse to mix fixtures with user data, including on a second run.
        if (Student::exists() || Category::exists() || Task::exists()) {
            $this->command?->warn('Seed skipped: training tables already contain data.');
            return;
        }

        DB::transaction(function () {
            $students = collect([
                ['name' => 'Sara Ali', 'email' => 'sara@example.test'],
                ['name' => 'Omar Hassan', 'email' => 'omar@example.test'],
                ['name' => 'Lina Ahmed', 'email' => 'lina@example.test'],
                ['name' => 'Nabil Saleh', 'email' => 'nabil@example.test'],
            ])->map(fn ($data) => Student::create($data));
            $categories = [];
            foreach (['HTML', 'CSS', 'JavaScript', 'Laravel', 'Database'] as $name) {
                $categories[$name] = Category::create(['name' => $name]);
            }
            $examples = [
                ['Build a semantic HTML page', 'HTML', 'done', '2026-11-01', [0, 1]],
                ['Style a responsive form', 'CSS', 'in_progress', '2026-11-06', [1, 2]],
                ['Validate a JavaScript form', 'JavaScript', 'pending', '2026-11-10', [0, 2]],
                ['Create a Laravel API', 'Laravel', 'pending', '2026-11-15', [0, 1, 2]],
                ['Design a database schema', 'Database', 'in_progress', '2026-11-08', [1]],
                ['Explain database joins', 'Database', 'pending', null, []],
            ];
            foreach ($examples as [$title, $category, $status, $date, $assigned]) {
                $task = Task::create(['title' => $title, 'description' => 'Practice and explain your solution.',
                    'category_id' => $categories[$category]->id, 'status' => $status, 'due_date' => $date]);
                foreach ($assigned as $index) {
                    $task->students()->attach($students[$index]->id, ['assigned_at' => '2026-11-04 09:00:00']);
                }
            }
        });
    }
}
