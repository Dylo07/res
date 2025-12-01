@extends('layouts.app')

@section('styles')
<style>
    /* Task column styling */
    td:nth-child(4),
    th:nth-child(4) {
        width: 30% !important;
        background-color: #f0f8ff !important;
        padding: 15px !important;
    }

    td:nth-child(4) {
        font-size: 1.1em !important;
        font-weight: 500 !important;
    }

    th:nth-child(4) {
        background-color: #e6f3ff !important;
        font-weight: 600 !important;
    }

    /* Maintain table structure */
    .table {
        table-layout: fixed;
        width: 100%;
    }

    .table td, .table th {
        word-wrap: break-word;
    }
</style>
@endsection

@section('content')
<div class="container">
    <h1 class="my-4">Tasks</h1>
    <p class="lead">Welcome, {{ Auth::user()->name ?? 'Guest' }}!</p>

    <div class="mb-4">
        <a href="{{ route('tasks.create') }}" class="btn btn-primary mr-2">
            <i class="fas fa-plus"></i> Add Task
        </a>
        <a href="{{ route('task-categories.create') }}" class="btn btn-secondary">
            <i class="fas fa-folder-plus"></i> Create Task Category
        </a>
    </div>

    <h2 class="mt-5">Pending Tasks</h2>
    <table class="table table-bordered table-hover mt-3">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Date Added</th>
                <th>Task</th>
                <th>Task Category</th>
                <th>Person Incharge</th>
                <th>Priority Order</th>
                <th>Mark as Done</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingTasks as $task)
            <tr>
                <td>{{ $task->id }}</td>
                <td>{{ $task->user }}</td>
                <td>{{ $task->date_added }}</td>
                <td class="my-4" width="40%" style="font-size: 1.1em; font-weight: bold; background-color: #ffffcc;">
                    {{ $task->task }}
                </td>
                <td>{{ $task->taskCategory->name }}</td>
                <td>{{ $task->person_incharge }}</td>
                <td>
                    @if($task->priority_order == 'High')
                        <span class="badge badge-danger">High</span>
                    @elseif($task->priority_order == 'Medium')
                        <span class="badge badge-warning">Medium</span>
                    @elseif($task->priority_order == 'Low')
                        <span class="badge badge-success">Low</span>
                    @else
                        <span class="badge badge-secondary">{{ $task->priority_order }}</span>
                    @endif
                </td>
                <td>
                    <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="is_done" value="1">
                        <button type="submit" class="btn btn-success" aria-label="Mark task as done">
                            <i class="fas fa-check"></i> Mark as Done
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No active tasks found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="mt-5">Completed Tasks</h2>
    <table class="table table-bordered table-hover mt-3">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Date Added</th>
                <th>Task</th>
                <th>Task Category</th>
                <th>Person Incharge</th>
                <th>Priority Order</th>
                <th>Date Completed</th>
            </tr>
        </thead>
        <tbody>
            @forelse($completedTasks as $completedTask)
            <tr>
                <td>{{ $completedTask->id }}</td>
                <td>{{ $completedTask->user }}</td>
                <td>{{ $completedTask->date_added }}</td>
                <td class="my-4" width="40%" style="font-size: 1.1em; font-weight: bold; background-color:#c2ffbd;">{{ $completedTask->task }}</td>
                <td>{{ $completedTask->taskCategory->name }}</td>
                <td>{{ $completedTask->person_incharge }}</td>
                <td>
                    @if($completedTask->priority_order == 'High')
                        <span class="badge badge-danger">High</span>
                    @elseif($completedTask->priority_order == 'Medium')
                        <span class="badge badge-warning">Medium</span>
                    @elseif($completedTask->priority_order == 'Low')
                        <span class="badge badge-success">Low</span>
                    @else
                        <span class="badge badge-secondary">{{ $completedTask->priority_order }}</span>
                    @endif
                </td>
                <td>{{ $completedTask->updated_at->format('Y-m-d') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No completed tasks found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
