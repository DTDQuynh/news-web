<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    public function show(Post $post): View
    {
        abort_unless($post->is_published, 404);
        $post->load(['category', 'user']);

        return view('posts.show', compact('post'));
    }
}
