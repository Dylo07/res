<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all tasks with their category
        $tasks = Task::with('taskCategory')->get();

        // Separate pending and completed tasks
        $pendingTasks = $tasks->where('is_done', false);
        $completedTasks = $tasks->where('is_done', true);

        // Return the index view with tasks
        return view('tasks.index', compact('pendingTasks', 'completedTasks'));
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Fetch all task categories
        $taskCategories = TaskCategory::all();

        // Return the create view with categories
        return view('tasks.create', compact('taskCategories'));
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'user' => 'required|string',
            'date_added' => 'required|date',
            'task' => 'required|string',
            'task_category_id' => 'required|exists:task_categories,id',
            'person_incharge' => 'required|string',
            'priority_order' => 'required|in:High,Medium,Low',
            'is_done' => 'nullable|boolean',
        ]);

        // Create the task
        Task::create($request->all());

        // Redirect back with a success message
        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    /**
     * Update the status of a task (mark as done).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        // Find the task by ID
        $task = Task::findOrFail($id);

        // Update the 'is_done' status
        $task->update(['is_done' => $request->has('is_done')]);

        // Redirect back with a success message
        return redirect()->route('tasks.index')->with('success', 'Task status updated successfully!');
    }
}
