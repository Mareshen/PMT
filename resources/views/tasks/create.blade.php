@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>Create New Task</h1>
        <a href="/tasks" class="btn btn-secondary">← Back to Tasks</a>
    </div>

    <div class="card">
        <form method="POST" action="/tasks">
            @csrf

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
                </select>
            </div>

            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}">
            </div>

            <button type="submit" class="btn">Save Task</button>
        </form>
    </div>
@endsection
