<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('profile.saved');
    }

    public function savedPosts(Request $request): View
    {
        $user = $request->user();
        $savedPosts = $user->savedPosts()
            ->with('category')
            ->orderByPivot('created_at', 'desc')
            ->paginate(12, ['posts.*'], 'saved_page')
            ->withQueryString();

        return view('profile.saved-posts', compact('savedPosts'));
    }

    public function readingHistory(Request $request): View
    {
        $recentPosts = $this->recentPosts($request);

        return view('profile.reading-history', compact('recentPosts'));
    }

    private function recentPosts(Request $request): Collection
    {
        $recentPostIds = array_values(array_unique(array_filter(
            array_map('intval', $request->session()->get('recently_read_posts', [])),
            static fn (int $id): bool => $id > 0,
        )));
        $recentPostOrder = array_flip($recentPostIds);
        $recentPosts = Post::published()
            ->with('category')
            ->whereIn('id', $recentPostIds)
            ->get()
            ->sortBy(static fn (Post $post): int => $recentPostOrder[$post->id])
            ->values();

        return $recentPosts;
    }
}
