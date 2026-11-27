<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        $comments = $post->comments()
            ->with('user')
            ->when(! $user?->isAdmin(), function ($query) use ($user): void {
                $query->where(function ($query) use ($user): void {
                    $query->where('is_approved', true);

                    if ($user) {
                        $query->orWhere('user_id', $user->id);
                    }
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json([
            'data' => $comments->getCollection()
                ->map(fn (Comment $comment): array => $this->commentData($comment, $user))
                ->values(),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    public function store(Request $request, Post $post): JsonResponse
    {
        $request->merge(['content' => trim((string) $request->input('content'))]);
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:500'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'is_approved' => true,
        ]);
        $comment->load('user');

        return response()->json([
            'message' => 'Đã gửi bình luận.',
            'data' => $this->commentData($comment, $request->user()),
        ], 201);
    }

    public function destroy(Request $request, Post $post, Comment $comment): JsonResponse
    {
        abort_unless($comment->post_id === $post->id, 404);
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Đã xóa bình luận.']);
    }

    public function updateApproval(
        Request $request,
        Post $post,
        Comment $comment
    ): JsonResponse {
        abort_unless($comment->post_id === $post->id, 404);

        $validated = $request->validate([
            'is_approved' => ['required', 'boolean'],
        ]);

        $comment->update(['is_approved' => $validated['is_approved']]);
        $comment->load('user');

        return response()->json([
            'message' => $comment->is_approved
                ? 'Đã duyệt bình luận.'
                : 'Bình luận đã được ẩn.',
            'data' => $this->commentData($comment, $request->user()),
        ]);
    }

    private function commentData(Comment $comment, ?User $user): array
    {
        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'author' => $comment->user->name,
            'created_at' => $comment->created_at->format('d/m/Y H:i'),
            'is_approved' => $comment->is_approved,
            'can_delete' => $user?->can('delete', $comment) ?? false,
            'can_moderate' => $user?->isAdmin() ?? false,
        ];
    }
}
