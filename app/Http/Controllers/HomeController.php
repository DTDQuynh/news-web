<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return $this->renderPosts(
            Post::query()
                ->with('category')
                ->where('is_published', true)
                ->orderByDesc('published_at')
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
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->orderByDesc('id'),
            $category,
        );
    }

    private function renderPosts(mixed $query, ?Category $category): View
    {
        $posts = (clone $query)->paginate(6);
        $missedPosts = $posts->isEmpty()
            ? collect()
            : (clone $query)->skip($posts->lastItem())->take(2)->get();

        return view('home', compact('posts', 'missedPosts', 'category'));
    }
}
