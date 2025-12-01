<?php

namespace App\Http\Controllers;

use App\Models\PackageCategory;
use Illuminate\Http\Request;

class PackageCategoryController extends Controller
{
    public function index()
    {
        $categories = PackageCategory::withCount('packages')->get();
        return view('packages.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('packages.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:package_categories',
            'description' => 'nullable|string'
        ]);

        PackageCategory::create($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Category created successfully');
    }

    public function edit(PackageCategory $packageCategory)
    {
        return view('packages.categories.edit', compact('packageCategory'));
    }

    public function update(Request $request, PackageCategory $packageCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:package_categories,name,' . $packageCategory->id,
            'description' => 'nullable|string'
        ]);

        $packageCategory->update($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Category updated successfully');
    }

    public function destroy(PackageCategory $packageCategory)
    {
        // Check if category has packages
        if ($packageCategory->packages()->exists()) {
            return redirect()->route('packages.index')
                ->with('error', 'Cannot delete category that has packages. Please delete or move the packages first.');
        }

        $packageCategory->delete();

        return redirect()->route('packages.index')
            ->with('success', 'Category deleted successfully');
    }
}