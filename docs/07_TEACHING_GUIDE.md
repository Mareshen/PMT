# Instructor teaching sequence

Start with requirements and finish with a request traced from URL to database and back. Use a disposable database for student write exercises. Open phpMyAdmin alongside the code. Do not reset the user's pmt data.

| Lesson / objective | Files to open | Practical demonstration | Important concept | Short exercise | Ask students |
|---|---|---|---|---|---|
| 1. Extract entities | 01_REQUIREMENTS_AND_DATABASE.md | Highlight students, categories, tasks and assignment date in requirements | A requirement becomes stored facts | List required and optional fields | Why isn't status on student_task? |
| 2. Design constraints | migration, lesson 01 | Inspect primary/foreign keys in phpMyAdmin | Database protects integrity | Predict category deletion result | Why composite PK and no pivot ID? |
| 3. Compare relationships | models, seeder | Follow one category to tasks and one student to several tasks | One-to-many differs from many-to-many | Draw the ER diagram | Why no comma-separated IDs? |
| 4. Read SQL | lesson 02 | Run INNER and LEFT JOIN SELECTs | JOIN is an operation; pivot is a table | Include unassigned tasks | Why does INNER JOIN hide those tasks? |
| 5. Read PHP | lesson 03, Task model | Explain variables, arrays, methods and namespaces | PHP language versus Laravel library | Explain one closure aloud | What does -> mean versus ::? |
| 6. Bootstrap Laravel | public/index.php, bootstrap/app.php, routes/api.php | Trace GET /api/tasks | Router chooses a controller | Add a harmless practice route in a branch | Where is the /api prefix registered? |
| 7. Create/read/edit/delete | TaskController, TrainingApiTest | Create a task, edit its title and delete it | HTTP method expresses intention | Trigger missing title 422 | Why is create 201 and delete 204? |
| 8. Validate input | controller rules, lesson 05 | Invalid category/status; duplicate email validation test | Validation differs from DB constraints | Test an overlong title | What happens if a parent disappears after validation? |
| 9. Assign students | TaskAssignmentController, student_task | SELECT pivot before/after POST, PUT and DELETE | Add preserves; replace removes omitted IDs | Repeat a POST, inspect dates | Why does sync differ from syncWithoutDetaching? |
| 10. Change progress | TaskStatusController | PATCH pending to in_progress to done | Status belongs to overall task | Send an invalid value | Do assigned students have separate progress? |
| 11. Build useful queries | TaskController::index, lesson 02 | Combine status, student, search and dates | Filter before get/paginate | Query student 3, sort due dates | Why group the search OR inside parentheses? |
| 12. Render a view | TaskPageController, tasks/index.blade.php | Compare /tasks and /api/tasks | Same models, HTML versus JSON | Add a safe read-only column | Why escape Blade output? |
| 13. Read a contract | config/scramble.php, ApiDocumentationTest | Open UI/spec and test a request | OpenAPI describes actual behavior | Locate all ten filters | How do we detect stale documentation? |
| 14. Verify and use Git | tests, GIT_LESSON.md | Run isolated tests, inspect git diff/log | Tests and history make changes reviewable | Write a meaningful commit for a small fix | Why never commit .env or run fresh on pmt? |

After each demonstration, have students predict the database effect before sending the request, inspect the response, and then verify actual rows. Let them explain one controller method in their own words. Finish by designing a small new feature on paper: its fields, constraints, endpoint, validation, response and test, before writing code.
