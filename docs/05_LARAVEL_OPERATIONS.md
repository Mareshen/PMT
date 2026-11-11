# Operations and their effects

All API paths are registered in routes/api.php. Send `Accept: application/json`; JSON writes also need `Content-Type: application/json`. In examples replace IDs with actual records. Response objects also include timestamps and loaded relationships; examples below abbreviate those fields.

## Create: introduce work

POST /api/tasks to TaskController::store:

```json
{"title":"Practice database joins","category_id":5,"description":null,"due_date":"2026-11-20"}
```

Important code: `$data = $request->validate([...]); $task = Task::create($data);` Validation rejects missing title or category. The database inserts one task, defaults status to pending, and inserts no assignments. Response: 201 `{"data":{"id":7,"title":"Practice database joins","status":"pending","category_id":5,"students":[]}}`.

## Read: inspect work

GET /api/tasks/7 to TaskController::show runs `$task->load(['category','students'])`. Binding looks up the task; related SELECTs read category and assignments. No data changes. Response: 200 `{"data":{"id":7,"title":"Practice database joins","category":{"id":5,"name":"Database"},"students":[]}}`. Unknown task: 404 `{"message":"No query results for model ..."}`; exact message is framework dependent.

## Update: edit fields

PATCH /api/tasks/7 with `{"title":"Practice SQL joins","due_date":null}` to TaskController::update. `$task->update($data)` changes supplied fields only, clears nullable due_date and retains all other fields. Response: 200 `{"data":{"id":7,"title":"Practice SQL joins","due_date":null}}`. PUT is also routed to the same action with partial-update semantics; prefer PATCH for edits in this lesson.

## Delete: remove work

DELETE /api/tasks/7 to TaskController::destroy uses `$task->delete()`. DELETE tasks cascades assignment rows. Students remain. Response: **204 with no body**. Deleting a missing task returns 404.

## Validation: reject invalid requests before writing

`required` disallows missing/empty data; `string` checks text; `max:120` limits title length. `nullable` permits null, `integer` checks an integer value, `exists:categories,id` checks a referenced row, `in` restricts allowed values, `array` checks the ID list, `date` checks dates. Filters use the stricter `date_format:Y-m-d`. `sometimes` validates a field only if supplied; `present` requires a key while allowing an empty array.

`unique:students,email` checks email uniqueness before insertion; the database UNIQUE constraint is the final protection. Student creation is deliberately outside the API scope. This validation rule is demonstrated in TrainingApiTest rather than by adding an unrequested student-write endpoint.

| Bad request | Result |
|---|---|
| POST task without title | 422, errors.title |
| status `blocked` | 422, errors.status |
| category_id 999999 | 422, errors.category_id |
| student_ids [999999] | 422, errors.student_ids.0 |
| duplicate email in validation exercise | validation fails; UNIQUE also rejects direct duplicate SQL |
| repeated assignment or repeated IDs in POST | 200; one pivot row only, no original date change |
| nonexistent task URL | 404 |
| sort_by=password or per_page=101 | 422 |

Typical validation response:

```json
{"message":"The title field is required.","errors":{"title":["The title field is required."]}}
```

APP_DEBUG=false keeps raw SQL exceptions and stack traces out of API responses. Internal unexpected failures become a generic 500 and are logged to storage/logs/laravel.log. Do not turn debug on for public deployment. Validation and foreign keys solve different problems: a reference could disappear after validation; the database still guarantees integrity.

## Filter/search/sort/page: find a useful subset

GET /api/tasks?status=pending&category_id=5&search=database&sort_by=due_date&sort_dir=asc&page=1&per_page=10 goes to TaskController::index. It validates parameters, builds conditions before paginate(), and returns 200 `{"data":{"current_page":1,"data":[{"id":6,"title":"Explain database joins"}],"per_page":10,"total":1}}`. Example counts depend on current data.

Use `student_id=3` for many-to-many filtering via `whereHas('students', ...)`. Add due_after/due_before for inclusive bounds. No rows change. Database WHERE filters and ORDER BY sorts before LIMIT/OFFSET; see lesson 02 for each SQL concept. Allowed sort columns: id, title, due_date, status, created_at. Directions: asc/desc. Page starts at 1; per_page is 1–100, default 15. A page beyond the last returns 200 with an empty data array.

## Relationships: navigate without duplicating data

GET /api/students/1/tasks goes to StudentController::tasks, using `$student->tasks()->with('category')->paginate(15)`. GET /api/categories/5/tasks goes to CategoryController::tasks, using `$category->tasks()->with('students')->paginate(15)`. Both only read records and return 200 data containing a paginator. The former crosses student_task; the latter filters tasks.category_id.

## Assignments: add versus replace

POST /api/tasks/1/students with `{"student_ids":[3]}` goes to TaskAssignmentController::store. `syncWithoutDetaching` adds new rows and keeps existing rows. One student is a one-element list. PUT to the same URL with `{"student_ids":[2,3]}` goes to update and uses sync to replace the entire list. An empty list on PUT clears assignments; an empty POST list is rejected. Each returns 200 task JSON, e.g. `{"data":{"id":1,"students":[{"id":2},{"id":3}]}}` (actual student objects include names, emails, timestamps and pivot).

The following is a teaching example for task 1, **not a recorded live database write**. Inspect using `SELECT * FROM student_task WHERE task_id=1 ORDER BY student_id;` before and after each request.

| Stage | student_id | task_id | assigned_at |
|---|---:|---:|---|
| Before | 1 | 1 | 2026-11-04 09:00:00 |
| Before | 2 | 1 | 2026-11-04 09:00:00 |
| After POST [3] | 1 | 1 | 2026-11-04 09:00:00 |
| After POST [3] | 2 | 1 | 2026-11-04 09:00:00 |
| After POST [3] | 3 | 1 | time of request |
| After PUT [2,3] | 2 | 1 | 2026-11-04 09:00:00 |
| After PUT [2,3] | 3 | 1 | original request time retained |

DELETE /api/tasks/1/students/2 goes to destroy and uses detach(2). Only (2,1) disappears; response is 204. A valid student already unassigned also returns 204. An unknown student ID returns 404. The composite primary key prevents duplicate assignments even outside Eloquent.

## Status: change shared progress

PATCH /api/tasks/1/status with `{"status":"in_progress"}` goes to TaskStatusController::update. Validates the three allowed values, calls update, writes only tasks.status (and updated_at). Response: 200 `{"data":{"id":1,"status":"in_progress"}}`. The general update endpoint can change several task fields; the dedicated endpoint states a narrow intention and accepts only status. Progress is shared across all assigned students. There is no per-student status and no workflow transition engine.
