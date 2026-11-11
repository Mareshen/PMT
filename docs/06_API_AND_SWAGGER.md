# HTTP, REST and OpenAPI

REST organizes operations around resources such as tasks. GET reads, POST creates or adds, PATCH edits selected fields, PUT replaces the assignment list, DELETE removes. Task PUT also accepts partial edits as documented; assignment PUT always replaces the list.

A path parameter identifies a record (`/tasks/1`). A query parameter modifies a read (`?status=pending`). A JSON body supplies write data. Headers describe the representation: Accept requests JSON responses; Content-Type identifies a JSON body.

| Method | URL under /api | Action | Success |
|---|---|---|---|
| GET | /students | StudentController::index | 200 |
| GET | /students/{student} | StudentController::show | 200 |
| GET | /students/{student}/tasks | StudentController::tasks | 200 |
| GET | /categories | CategoryController::index | 200 |
| GET | /categories/{category} | CategoryController::show | 200 |
| GET | /categories/{category}/tasks | CategoryController::tasks | 200 |
| GET | /tasks | TaskController::index | 200 |
| POST | /tasks | TaskController::store | 201 |
| GET | /tasks/{task} | TaskController::show | 200 |
| PUT/PATCH | /tasks/{task} | TaskController::update | 200 |
| DELETE | /tasks/{task} | TaskController::destroy | 204 |
| POST | /tasks/{task}/students | TaskAssignmentController::store | 200 |
| PUT | /tasks/{task}/students | TaskAssignmentController::update | 200 |
| DELETE | /tasks/{task}/students/{student} | TaskAssignmentController::destroy | 204 |
| PATCH | /tasks/{task}/status | TaskStatusController::update | 200 |

200 means successful read/update. 201 means created. 204 means success without response content. 404 means the path-bound record is missing. 422 means the request matched a route but its fields failed validation. Unexpected server failures return 500; inspect local logs, never expose them to clients.

## Run requests in PowerShell

Start the server first, then use another terminal. IDs in examples assume a fresh seed; inspect yours.

```powershell
$base = 'http://127.0.0.1:8000'
$headers = @{ Accept = 'application/json' }
Invoke-RestMethod "$base/api/tasks?status=pending&category_id=2" -Headers $headers
Invoke-RestMethod "$base/api/tasks?student_id=3" -Headers $headers
Invoke-RestMethod "$base/api/tasks?search=database&sort_by=due_date&sort_dir=asc" -Headers $headers
Invoke-RestMethod "$base/api/tasks?page=2&per_page=2" -Headers $headers
$created = Invoke-RestMethod "$base/api/tasks" -Method Post -Headers $headers -ContentType 'application/json' -Body '{"title":"Practice API","category_id":4}'
$taskId = $created.data.id
Invoke-RestMethod "$base/api/tasks/$taskId/status" -Method Patch -Headers $headers -ContentType 'application/json' -Body '{"status":"in_progress"}'
Invoke-RestMethod "$base/api/tasks/$taskId/students" -Method Post -Headers $headers -ContentType 'application/json' -Body '{"student_ids":[1,2]}'
Invoke-RestMethod "$base/api/tasks/$taskId/students" -Method Put -Headers $headers -ContentType 'application/json' -Body '{"student_ids":[3]}'
Invoke-RestMethod "$base/api/tasks/$taskId/students/3" -Method Delete -Headers $headers
Invoke-RestMethod "$base/api/tasks/$taskId" -Method Delete -Headers $headers
```

Expected create shape: `{"data":{"id":7,"title":"Practice API","status":"pending","category_id":4,"students":[],"category":{"id":4,"name":"Laravel"}}}`. Actual output includes description, due_date and timestamps. See lesson 05 for errors and assignment snapshots.

Postman provides the same operations visually: choose method, paste URL, add Accept header, select Body → raw → JSON for writes, and send. Inspect status and response body. No authorization header is used in this introductory lesson.

## Scramble and OpenAPI

OpenAPI is a machine-readable contract for paths, parameters, schemas and responses. Swagger is an ecosystem of tools for such contracts; Scramble is the one Laravel documentation generator in this project. Scramble supplies its own interactive UI; a second Swagger package is unnecessary.

Expected local URLs after installation: `/docs/api` and `/docs/api.json`. Package auto-discovery registers them. config/scramble.php sets title, version and description. Supported `#[Group]` controller attributes order Students, Categories, Tasks, Task Assignments and Task Status. Action PHPDoc summaries supply readable names; inline validation supplies request schemas. Route model binding supplies path parameter context. See [Scramble attributes](https://scramble.dedoc.co/developers/reference).

Task filters are all validated in TaskController::index: status, category_id, student_id, search, due_before, due_after, sort_by, sort_dir, page and per_page. Assignment body is student_ids (array of existing integers); status body is one allowed status string.

Verification when extending the API:

1. Run `php artisan test --filter=ApiDocumentationTest` and `php artisan scramble:export`.
2. Inspect every generated response schema and status, including inferred 404/422 responses.
3. Open the UI, confirm all five groups and readable summaries, and send a real GET through it.
4. Compare create, validation, assignment and pagination responses with the generated contract; add supported response attributes/examples where inference is incomplete.

**Verified on 11 November 2026:** the documentation test passes, the UI and JSON routes return 200, and `scramble:export` generates OpenAPI 3.1.2 with 11 paths. The test checks routes, summaries, groups, and all filter names. Browser interaction and a field-by-field review of every inferred response schema remain manual checks; successful generation alone does not establish complete response schemas.

## Production boundary

The lesson deliberately excludes authentication. Real APIs need authentication to identify callers and authorization to restrict which students/tasks they can read or change. They also need HTTPS, rate limits, deployment configuration and privacy controls. Keep this unauthenticated teaching server local; authentication implementation is a later lesson.
