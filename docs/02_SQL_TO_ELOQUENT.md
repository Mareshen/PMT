# SQL and Eloquent side by side

Eloquent models represent table rows. A query builder accumulates conditions; methods such as get(), first(), paginate() and create() actually contact the database. Values are bound as parameters rather than concatenated into SQL. Column names cannot be parameter-bound; this is why sort_by has an allowlist.

The following examples assume category ID 1 exists. Write examples are exercises for a disposable database, not instructions to modify existing user data.

| SQL | Eloquent |
|---|---|
| `SELECT * FROM tasks;` | `Task::all();` |
| `SELECT * FROM tasks WHERE id=1 LIMIT 1;` | `Task::find(1);` |
| `INSERT INTO tasks(title,category_id,created_at,updated_at) VALUES ('Practice',1,NOW(),NOW());` | `Task::create(['title'=>'Practice','category_id'=>1]);` |
| `UPDATE tasks SET status='done',updated_at=NOW() WHERE id=1;` | `Task::findOrFail(1)->update(['status'=>'done']);` |
| `DELETE FROM tasks WHERE id=1;` | `Task::findOrFail(1)->delete();` |
| `SELECT * FROM tasks WHERE status='pending';` | `Task::where('status','pending')->get();` |
| `SELECT * FROM tasks WHERE title LIKE '%database%';` | `Task::where('title','like','%database%')->get();` |
| `SELECT * FROM tasks ORDER BY due_date ASC,id ASC LIMIT 10 OFFSET 10;` | `Task::orderBy('due_date')->orderBy('id')->skip(10)->take(10)->get();` |

`paginate(10)` also counts the total and returns metadata and links. `get()` returns a collection; `first()` returns one model or null. Avoid `Task::all()->filter(...)` for API filtering: that transfers everything into PHP first.

## Relationships used in PMT

```php
// Definitions in Category, Task and Student models:
return $this->hasMany(Task::class);       // category_id on tasks
return $this->belongsTo(Category::class); // this task's category_id
return $this->belongsToMany(Student::class)->withPivot('assigned_at');

$category->tasks;                // task collection
$task->category;                 // one category
$student->tasks;                 // via student_task
Task::with(['category', 'students'])->get(); // eager loading avoids a query per row
Task::whereHas('students', fn ($q) => $q->where('students.id', 3))->get();

$task->students()->attach(3, ['assigned_at' => now()]); // inserts; duplicate throws
$task->students()->detach(3); // removes this relationship only
$task->students()->sync([2 => ['assigned_at' => now()]]); // replaces list
$task->students()->syncWithoutDetaching([3 => ['assigned_at' => now()]]); // adds
```

The assignment controller removes existing IDs before adding so retries preserve original assigned_at. Replacement supplies empty attributes for retained IDs, preserving their dates. `sync([])` removes all assignments. The transaction groups writes atomically; a parent-row lock serializes concurrent assignment requests for the same task. This small safeguard is necessary because checking then inserting without a lock can race.

`with()` normally runs separate eager-loading queries, rather than one giant JOIN. `whereHas()` builds a relationship existence condition, conceptually:

```sql
SELECT * FROM tasks
WHERE EXISTS (
  SELECT 1 FROM students
  INNER JOIN student_task ON students.id = student_task.student_id
  WHERE student_task.task_id = tasks.id AND students.id = 3
);
```

## Filter SQL concepts

| Parameter | Database concept |
|---|---|
| status | `WHERE status = ?` |
| category_id | `AND category_id = ?` |
| student_id | `AND EXISTS (...)` through student_task |
| search | `AND (title LIKE ? OR description LIKE ?)` |
| due_before | `AND DATE(due_date) <= ?` inclusive |
| due_after | `AND DATE(due_date) >= ?` inclusive |
| sort_by / sort_dir | allowed `ORDER BY column ASC/DESC`, then ID for stable ties |
| page / per_page | `LIMIT` and `OFFSET` plus a count query |

NULL due dates do not satisfy date comparisons. MariaDB sorts NULL first ascending, last descending. Search uses SQL LIKE semantics: `%` and `_` in input are wildcard characters; case sensitivity follows the database collation. These are bound values, not executable SQL. Multiple filter conditions combine with AND; parentheses keep OR search terms inside the other filters.
