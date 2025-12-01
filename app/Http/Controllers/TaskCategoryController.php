<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function index()
    {
        $categories = TaskCategory::all();
        return view('task_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('task_categories.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|unique:task_categories,name|max:255',
    ]);

    TaskCategory::create(['name' => $request->name]);

    return redirect()->route('task-categories.index')->with('success', 'Task Category created successfully!');
}
    public function edit($id)
    {
        $category = TaskCategory::findOrFail($id);
        return view('task_categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:task_categories,name,' . $id . '|max:255',
        ]);

        $category = TaskCategory::findOrFail($id);
        $category->update(['name' => $request->name]);

        return redirect()->route('task-categories.index')->with('success', 'Task Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = TaskCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('task-categories.index')->with('success', 'Task Category deleted successfully!');
    }
}
