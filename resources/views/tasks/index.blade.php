@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>Tasks</h1>
        <a href="/tasks/create" class="btn">+ Create Task</a>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card">
        <form method="GET" action="/tasks" class="filter-bar">
            <input type="text" name="search" placeholder="Search title or description..." value="{{ request('search') }}">

            <select name="status">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
            </select>

            <select name="category_id">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="student_id">
                <option value="">All Students</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                        {{ $student->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn">Filter</button>
            <a href="/tasks" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    <!-- Tasks List Table -->
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Assigned Students</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr>
                        <td>
                            <a href="/tasks/{{ $task->id }}" style="font-weight: 600; color: #1e293b; text-decoration: none;">
                                {{ $task->title }}
                            </a>
                        </td>
                        <td>{{ $task->category->name }}</td>
                        <td>
                            <form method="POST" action="/tasks/{{ $task->id }}/status" class="inline-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="badge badge-{{ $task->status }}">
                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                                </select>
                            </form>
                        </td>
                        <td>{{ $task->due_date?->format('Y-m-d') ?? 'No date' }}</td>
                        <td>{{ $task->students->pluck('name')->join(', ') ?: 'Unassigned' }}</td>
                        <td>
                            <a href="/tasks/{{ $task->id }}" class="btn btn-secondary btn-sm">View / Assign</a>
                            <a href="/tasks/{{ $task->id }}/edit" class="btn btn-secondary btn-sm">Edit</a>

                            <form method="POST" action="/tasks/{{ $task->id }}" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this task?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No tasks found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 1rem;">
            @if ($tasks->previousPageUrl())
                <a href="{{ $tasks->previousPageUrl() }}" class="btn btn-secondary btn-sm">Previous</a>
            @endif
            <span style="margin: 0 0.5rem;">Page {{ $tasks->currentPage() }} of {{ $tasks->lastPage() }}</span>
            @if ($tasks->nextPageUrl())
                <a href="{{ $tasks->nextPageUrl() }}" class="btn btn-secondary btn-sm">Next</a>
            @endif
        </div>
    </div>
@endsection
