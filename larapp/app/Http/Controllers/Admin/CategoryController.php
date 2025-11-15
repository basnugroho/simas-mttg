<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = Category::query();
        if ($q) $query->where('name', 'like', "%{$q}%");
        $categories = $query->orderBy('name')->paginate(20);
        return view('admin.master.categories.index', compact('categories', 'q'));
    }

    public function create()
    {
        return view('admin.master.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:50',
        ]);
        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category created');
    }

    public function edit(Category $category)
    {
        return view('admin.master.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:50',
        ]);
        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted');
    }
}
