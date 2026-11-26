<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'totalPosts' => Post::count(),
            'totalCategories' => Category::count(),
            'totalComments' => Comment::count(),
            'totalViews' => (int) Post::sum('views_count'),
        ]);
    }
}