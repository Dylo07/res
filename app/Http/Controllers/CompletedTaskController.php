<?php

namespace App\Http\Controllers;

use App\Models\CompletedTask;
use Illuminate\Http\Request;

class CompletedTaskController extends Controller
{
    public function index()
    {
        $completedTasks = CompletedTask::with('taskCategory')->get();
        return view('completed_tasks.index', compact('completedTasks'));
    }

    // Add other methods if necessary
}
