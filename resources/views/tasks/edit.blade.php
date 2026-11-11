@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>Edit Task</h1>
        <a href="/tasks/{{ $task->id }}" class="btn btn-secondary">← Back to Task</a>
    </div>

    <div class="card">
        <form method="POST" action="/tasks/{{ $task->id }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $task->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>Done</option>
                </select>
            </div>

            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
            </div>

            <button type="submit" class="btn">Update Task</button>
        </form>
    </div>
@endsection
