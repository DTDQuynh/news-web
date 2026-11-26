<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = Category::withCount('posts')
            ->latest('id')
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        Category::create($data);
        Cache::forget('navbar.categories.v2');

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Đã thêm danh mục mới.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $posts = $category->posts()
        ->with('user')
        ->latest('id')
        ->paginate(10);

    return view('admin.categories.show', compact('category', 'posts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        $category->update($data);
        Cache::forget('navbar.categories.v2');

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Đã cập nhật danh mục.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->posts()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục đang có bài viết.');
        }

        $category->delete();
        Cache::forget('navbar.categories.v2');

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Đã xóa danh mục.');
    }
}
