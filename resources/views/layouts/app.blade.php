<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PMT — PixelMindTrainning</title>
    <style>
        body {
            font: 15px/1.5 system-ui, -apple-system, sans-serif;
            background: #f4f6f9;
            color: #1e293b;
            margin: 0;
        }

        .navbar {
            background: #1e293b;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 1.2rem;
            font-weight: 500;
        }

        .navbar .brand {
            font-size: 1.2rem;
            font-weight: 700;
            margin-left: 0;
        }

        .container {
            max-width: 1050px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            margin: 0;
            font-size: 1.6rem;
        }

        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        input,
        select,
        textarea {
            padding: 0.55rem 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font: inherit;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            box-sizing: border-box;
        }

        .btn {
            display: inline-block;
            padding: 0.55rem 1rem;
            border-radius: 6px;
            border: 1px solid transparent;
            font: inherit;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        .btn-danger {
            background: #dc2626;
            color: #fff;
        }

        .btn-sm {
            padding: 0.3rem 0.65rem;
            font-size: 0.85rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 0.9rem;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-in_progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-done {
            background: #dcfce7;
            color: #166534;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 0.9rem 1.2rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.9rem 1.2rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .inline-form {
            display: inline;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="/tasks" class="brand">PMT — PixelMindTrainning</a>
        <div>
            <a href="/tasks">All Tasks</a>
            <a href="/tasks/create">+ New Task</a>
            <a href="/api/tasks">JSON API</a>
            <a href="/docs/api">API Docs</a>
        </div>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>

</html>
