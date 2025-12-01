<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('category')->get();
        $categories = PackageCategory::all();
        return view('packages.index', compact('packages', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:package_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'menu_items' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        // Convert menu items from textarea to array
        if (!empty($validated['menu_items'])) {
            $validated['menu_items'] = array_filter(
                explode("\n", str_replace("\r", "", $validated['menu_items']))
            );
        }

        // Convert additional info from textarea to array
        if (!empty($validated['additional_info'])) {
            $additionalInfo = [];
            $lines = explode("\n", str_replace("\r", "", $validated['additional_info']));
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = array_map('trim', explode(':', $line, 2));
                    $additionalInfo[$key] = $value;
                }
            }
            $validated['additional_info'] = $additionalInfo;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('packages', 'public');
        }

        Package::create($validated);
        return redirect()->route('packages.index')
            ->with('success', 'Package created successfully');
    }

    public function show(Package $package)
    {
        return view('packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        $categories = PackageCategory::all();
        return view('packages.edit', compact('package', 'categories'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:package_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'menu_items' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        // Convert menu items from textarea to array
        if (!empty($validated['menu_items'])) {
            $validated['menu_items'] = array_filter(
                explode("\n", str_replace("\r", "", $validated['menu_items']))
            );
        }

        // Convert additional info from textarea to array
        if (!empty($validated['additional_info'])) {
            $additionalInfo = [];
            $lines = explode("\n", str_replace("\r", "", $validated['additional_info']));
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = array_map('trim', explode(':', $line, 2));
                    $additionalInfo[$key] = $value;
                }
            }
            $validated['additional_info'] = $additionalInfo;
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }
            $validated['image'] = $request->file('image')
                ->store('packages', 'public');
        }

        $package->update($validated);
        return redirect()->route('packages.show', $package)
            ->with('success', 'Package updated successfully');
    }

    public function destroy(Package $package)
    {
        if ($package->image) {
            Storage::disk('public')->delete($package->image);
        }
        
        $package->delete();
        return redirect()->route('packages.index')
            ->with('success', 'Package deleted successfully');
    }
    public function print(Package $package)
{
    return view('packages.print', compact('package'));
}
}