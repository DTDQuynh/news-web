<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(): View
    {
        $comments = Comment::with(['post', 'user'])
            ->latest('id')
            ->paginate(15);

        return view('admin.comments.index', compact('comments'));
    }
}
