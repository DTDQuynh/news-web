<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SavedPostController extends Controller
{
    public function toggle(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->is_published, 404);

        $changes = $request->user()->savedPosts()->toggle($post->id);

        return back()->with(
            'status',
            $changes['attached']
                ? 'Đã lưu bài viết.'
                : 'Đã bỏ lưu bài viết.'
        );
    }
}
