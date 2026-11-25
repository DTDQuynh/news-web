<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::with(['category', 'user'])
            ->latest('id')
            ->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $isPublished = $data['status'] === 'published';
        $slugBase = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['title']);

        $payload = [
            'category_id' => $data['category_id'],
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($slugBase),
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'],
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ];

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('posts', 'public');
            $payload['image_url'] = Storage::url($path);
        }

        Post::create($payload);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Đã thêm bài viết mới.');
    }

    public function edit(Post $post): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        $isPublished = $data['status'] === 'published';
        $slugBase = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['title']);

        $payload = [
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($slugBase, $post->id),
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'],
            'is_published' => $isPublished,
            'published_at' => $isPublished ? ($post->published_at ?? now()) : null,
        ];

        if ($request->hasFile('thumbnail')) {
            $this->deleteLocalImage($post->image_url);
            $path = $request->file('thumbnail')->store('posts', 'public');
            $payload['image_url'] = Storage::url($path);
        }

        $post->update($payload);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Đã cập nhật bài viết.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->deleteLocalImage($post->image_url);
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Đã xóa bài viết.');
    }

    public function fetchRss(): RedirectResponse
    {
        $exitCode = Artisan::call('news:fetch-rss', ['--limit' => 5]);

        if ($exitCode !== 0) {
            return redirect()
                ->route('admin.posts.index')
                ->with('error', 'Không thể lấy tin RSS. Vui lòng kiểm tra lại cấu hình nguồn.');
        }

        $lines = array_filter(array_map('trim', explode("\n", trim(Artisan::output()))));
        $summary = end($lines) ?: 'Đã cập nhật tin tức từ RSS.';

        return redirect()
            ->route('admin.posts.index')
            ->with('success', $summary);
    }

    private function uniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'bai-viet';
        $original = $slug;
        $counter = 1;

        while (
            Post::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }

    private function deleteLocalImage(?string $imageUrl): void
    {
        if (! $imageUrl || ! str_starts_with($imageUrl, '/storage/')) {
            return;
        }

        $relativePath = Str::after($imageUrl, '/storage/');
        Storage::disk('public')->delete($relativePath);
    }
}