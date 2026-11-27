<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function show(Request $request, Post $post): View
    {
        abort_unless($post->is_published, 404);
        $post->load(['category', 'user']);

        if ($request->header('X-PWA-Prefetch') !== '1') {
            $viewedPosts = $request->session()->get('viewed_posts', []);

            if (!in_array($post->id, $viewedPosts, true)) {
                $post->increment('views_count');
                $post->refresh();
                $request->session()->put('viewed_posts', [...$viewedPosts, $post->id]);
            }

            $recentPostIds = array_values(array_filter(
                array_map('intval', $request->session()->get('recently_read_posts', [])),
                static fn (int $id): bool => $id !== $post->id,
            ));
            array_unshift($recentPostIds, $post->id);
            $request->session()->put('recently_read_posts', array_slice($recentPostIds, 0, 20));
        }

        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->limit(4)
            ->get();

        $post->content = $this->sanitizeHtml((string) $post->content);

        return view('posts.show', compact('post', 'relatedPosts'));
    }

    private function sanitizeHtml(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $allowedTags = [
            'a', 'b', 'blockquote', 'br', 'code', 'div', 'em', 'figcaption', 'figure',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'img', 'li', 'ol', 'p',
            'pre', 'span', 'strong', 'table', 'tbody', 'td', 'th', 'thead', 'tr', 'u', 'ul',
        ];
        $allowedAttributes = [
            'a' => ['href', 'title', 'target', 'rel'],
            'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
            '*' => ['class'],
        ];
        $blockedTags = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button'];

        $previousErrors = libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0', 'UTF-8');
        $loaded = $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="article-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        if (!$loaded) {
            return e(Str::limit(strip_tags($html), 500));
        }

        $root = $document->getElementById('article-root');
        if (!$root) {
            return '';
        }

        $nodes = [];
        foreach ($root->getElementsByTagName('*') as $node) {
            $nodes[] = $node;
        }

        foreach (array_reverse($nodes) as $node) {
            $tagName = strtolower($node->tagName);

            if (in_array($tagName, $blockedTags, true)) {
                $node->parentNode?->removeChild($node);
                continue;
            }

            if (!in_array($tagName, $allowedTags, true)) {
                $fragment = $document->createDocumentFragment();
                while ($node->firstChild) {
                    $fragment->appendChild($node->firstChild);
                }
                $node->parentNode?->replaceChild($fragment, $node);
                continue;
            }

            foreach (iterator_to_array($node->attributes) as $attribute) {
                $attributeName = strtolower($attribute->nodeName);
                $allowed = in_array($attributeName, $allowedAttributes[$tagName] ?? [], true)
                    || in_array($attributeName, $allowedAttributes['*'], true);

                if (!$allowed || str_starts_with($attributeName, 'on')) {
                    $node->removeAttribute($attribute->nodeName);
                    continue;
                }

                if (in_array($attributeName, ['href', 'src'], true)) {
                    $value = trim($attribute->nodeValue);
                    $scheme = parse_url($value, PHP_URL_SCHEME);

                    if ($scheme && !in_array(strtolower((string) $scheme), ['http', 'https', 'mailto', 'tel'], true)) {
                        $node->removeAttribute($attribute->nodeName);
                    }
                }
            }

            if ($tagName === 'a') {
                $node->setAttribute('target', '_blank');
                $node->setAttribute('rel', 'noopener noreferrer');
            }
        }

        $output = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $output .= $document->saveHTML($child);
        }

        return $output;
    }
}
