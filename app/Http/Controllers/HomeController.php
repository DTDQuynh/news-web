<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(): View
    {
        return $this->renderPosts(
            Post::query()
                ->with('category')
                ->published()
                ->orderByRaw('COALESCE(published_at, created_at) DESC')
                ->orderByDesc('id'),
            null,
        );
    }

    public function category(string $slug): View
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();

        return $this->renderPosts(
            $category->posts()
                ->with('category')
                ->published()
                ->orderByRaw('COALESCE(published_at, created_at) DESC')
                ->orderByDesc('id'),
            $category,
        );
    }

    public function search(Request $request): View
    {
        $keyword = trim((string) ($request->query('keyword') ?? $request->query('q', '')));

        $filters = [
            'keyword' => $keyword,
            'category' => $request->query('category'),
            'sort' => (string) $request->query('sort', 'latest'),
        ];

        $posts = Post::query()
            ->with('category')
            ->filter($filters)
            ->paginate(6)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $activeCategory = filled($filters['category'])
            ? $categories->first(fn (Category $item): bool => (string) $item->id === (string) $filters['category'] || $item->slug === $filters['category'])
            : null;

        return view('posts.search', compact('posts', 'categories', 'filters', 'activeCategory'));
    }

    private function renderPosts(mixed $query, ?Category $category): View
    {
        $posts = (clone $query)->paginate(6)->withQueryString();
        $missedPosts = $posts->isEmpty()
            ? collect()
            : (clone $query)->skip($posts->lastItem())->take(2)->get();

        return view('home', compact('posts', 'missedPosts', 'category'));
    }
}