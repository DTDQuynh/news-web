<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Comment;
use App\Policies\CommentPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Gate::policy(Comment::class, CommentPolicy::class);

        View::composer('partials.navbar', static function (ViewInstance $view): void {
            $view->with('categories', Cache::remember(
                'navbar.categories.v2',
                3600,
                static fn () => Category::query()
                    ->orderBy('name')
                    ->get(['name', 'slug'])
                    ->toArray(),
            ));
        });
    }
}
