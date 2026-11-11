<?php
namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_documentation_routes_and_api_contract(): void
    {
        $this->app['env'] = 'local';
        $this->get('/docs/api')->assertOk();
        $spec = $this->getJson('/docs/api.json')->assertOk()->json();
        $this->assertArrayHasKey('openapi', $spec);
        $paths = $spec['paths'];
        foreach (['/tasks', '/tasks/{task}', '/tasks/{task}/students', '/tasks/{task}/status', '/students', '/categories'] as $path) {
            $this->assertArrayHasKey($path, $paths);
        }
        $parameters = array_column($paths['/tasks']['get']['parameters'], 'name');
        foreach (['status', 'category_id', 'student_id', 'search', 'due_before', 'due_after', 'sort_by', 'sort_dir', 'page', 'per_page'] as $name) {
            $this->assertContains($name, $parameters);
        }
        $groups = [];
        foreach ($paths as $operations) {
            foreach ($operations as $method => $operation) {
                if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
                    $this->assertNotEmpty($operation['summary'] ?? null);
                    $groups = array_merge($groups, $operation['tags'] ?? []);
                }
            }
        }
        foreach (['Students', 'Categories', 'Tasks', 'Task Assignments', 'Task Status'] as $group) {
            $this->assertContains($group, $groups);
        }
    }
}
