# Read the PHP behind PMT

PHP is the programming language. Laravel is a PHP framework supplying routing, validation, models, responses and application conventions. `if` and `foreach` are PHP; `Task::with()` is Laravel Eloquent.

```php
<?php
namespace App\Examples;             // class address, avoids naming collisions
use App\Models\Task;               // import a class name

class Lesson
{
    public string $topic;           // public: callable/readable outside the class
    protected array $notes = [];    // protected: class and subclasses
    private int $count = 0;         // private: only this class

    public function __construct(string $topic) // constructor called by new
    {
        $this->topic = $topic;       // -> accesses an object's property
    }

    public function titles(?string $status = null): array
    {
        $filters = ['status' => $status]; // associative array: key => value
        $titles = [];                   // empty indexed array
        $query = Task::query();         // :: calls a class/static method
        if ($filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }
        foreach ($query->get() as $task) {
            $titles[] = $task->title;
        }
        return $titles;                 // send value back to caller
    }
}
$lesson = new Lesson('Tasks');          // instantiate an object
$titles = $lesson->titles('pending');   // call an instance method
```

`$` starts a variable. Strings use quotes. `.` concatenates strings. `[]` creates arrays or reads array keys. `=>` separates keys and values and also appears in arrow functions. `;` ends a statement. `{}` encloses a body. `===` checks both type and value; `!==` means not identical. `??` supplies a fallback for missing/null values. `?->` safely accesses a method/property when the object may be null. `?string` permits null. `: array`, `: void`, `: JsonResponse` declare return types. `return` ends a function with a value; void returns no value.

```php
$sort = $filters['sort_by'] ?? 'id';
$date = $task->due_date?->format('Y-m-d') ?? 'No date';
$callback = fn ($q) => $q->where('students.id', 3); // one-expression closure
$callback = function ($q) use ($filters) {        // capture outside variable
    return $q->where('title', 'like', '%'.$filters['search'].'%');
};
```

Namespaces match directories through Composer's PSR-4 autoloader: App maps to app/. `Task::class` is the fully qualified class name string. `extends Model` inherits Eloquent behavior; `use DatabaseMigrations` inside a class imports a trait. `#[Group(...)]` is a PHP attribute read by Scramble. PHPDoc `/** ... */` gives endpoint summaries. Laravel injects Request and finds typed route models automatically.

## Laravel methods in this project

| Method or feature | Meaning and real use |
|---|---|
| Application::configure / withRouting / withMiddleware / withExceptions / create | Bootstrap the app, register route files and configure HTTP handling |
| Route::get/post/put/patch/delete | Match an HTTP method and URL to a controller action |
| Route::apiResource | Generate task index/store/show/update/destroy routes; no HTML create/edit forms |
| Request::validate | Validate and return accepted fields; failure becomes JSON 422 for API requests |
| response()->json / noContent | Serialize data or return empty 204 |
| view / compact | Render a Blade file; compact('tasks') becomes ['tasks' => $tasks] |
| redirect | Return a redirect to /tasks |
| Model::create / update / delete / refresh | Insert, modify, remove or reload a row |
| $fillable | Permit only listed attributes during mass assignment |
| casts | Convert due_date into a date object and serialize YYYY-MM-DD |
| with / load | Load relationships with a query / on an existing model |
| hasMany / belongsTo / belongsToMany / withPivot | Define relationships and expose assigned_at; see lesson 02 |
| where / whereDate / whereHas / whereKey | Add column, date, relationship or primary-key conditions |
| orderBy / paginate / withQueryString | Database sorting, limited pages, preserved filter links |
| firstOrFail / findOrFail | Read one record or raise a 404 |
| firstOrCreate / exists | Reuse a matching category or insert one; check row existence |
| pluck / all / count | Extract column values, convert a collection to array, count rows |
| attach / detach / sync / syncWithoutDetaching | Change assignment rows; see lesson 02 |
| DB::transaction / lockForUpdate | Atomic writes and per-task serialization of assignment changes |
| Schema::create / Blueprint | Build database tables with id, string, text, date, enum, foreignId, primary, timestamps |
| constrained / restrictOnDelete / cascadeOnDelete | Declare foreign keys and parent deletion rules |
| collect / map | Transform small in-memory seed arrays, not filter the full database |
| now / env / storage_path / resource_path | Current timestamp, configuration variables, application paths |
| @forelse / @empty / @endforelse / @if | Blade loops and conditions |
| {{ expression }} | Escape HTML output to prevent script injection |

PHP built-ins `isset`, `array_unique`, `array_diff`, `in_array` and `array_merge` check presence and manipulate small validated ID arrays. They are not Eloquent queries. Models use Laravel's dynamic property handling for column access. Do not assume every object property is explicitly declared in the model.
