<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

#[Signature('news:fetch-rss {--limit= : Gioi han so tin moi kenh}')]
#[Description('Lay tin tu dong tu cac nguon RSS')]
class FetchRssNews extends Command
{
    public function handle(): int
    {
        $feeds = config('feeds.sources', []);
        $limit = (int) ($this->option('limit') ?: config('feeds.limit_per_feed', 10));

        if (empty($feeds)) {
            $this->warn('Chua cau hinh nguon RSS trong config/feeds.php');
            return self::FAILURE;
        }

        $categories = Category::pluck('id', 'slug');
        $authorId = User::where('role', 'admin')->value('id') ?? User::value('id');

        $added = 0;
        $skipped = 0;

        foreach ($feeds as $feed) {
            $sourceName = $feed['name'] ?? 'RSS';
            $catSlug = $feed['category_slug'] ?? '';
            $url = $feed['url'] ?? '';

            $catId = $categories[$catSlug] ?? null;
            if (! $catId || ! $url) {
                $this->warn("Bo qua {$sourceName} ({$catSlug}): sai cau hinh.");
                continue;
            }

            $this->line("Dang lay tin: {$sourceName} - {$catSlug}...");

            try {
                $res = Http::timeout(15)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($url);

                if (! $res->successful()) {
                    $this->error("Loi ket noi {$url} (HTTP {$res->status()})");
                    continue;
                }

                $xml = simplexml_load_string($res->body(), SimpleXMLElement::class, LIBXML_NOCDATA);
                if (! $xml || ! isset($xml->channel->item)) {
                    $this->warn("Khong doc duoc XML tu {$url}");
                    continue;
                }

                $i = 0;
                foreach ($xml->channel->item as $item) {
                    if ($i >= $limit) {
                        break;
                    }
                    $i++;

                    $title = trim(html_entity_decode((string) $item->title, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    $link = trim((string) $item->link);
                    $desc = (string) $item->description;

                    if ($title === '' || $link === '') {
                        continue;
                    }

                    if (Post::where('original_link', $link)->exists()) {
                        $skipped++;
                        continue;
                    }

                    $plainDesc = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8'))));

                    Post::create([
                        'category_id' => $catId,
                        'user_id' => $authorId,
                        'title' => Str::limit($title, 250, ''),
                        'slug' => $this->makeSlug($title),
                        'summary' => Str::limit($plainDesc, 480),
                        'content' => $this->cleanContent($desc, $plainDesc, $link, $sourceName),
                        'image_url' => $this->getThumbnail($item, $desc),
                        'rss_source' => $sourceName,
                        'original_link' => Str::limit($link, 500, ''),
                        'is_published' => true,
                        'published_at' => $this->formatPubDate((string) $item->pubDate),
                    ]);

                    $added++;
                }
            } catch (Throwable $e) {
                $this->error("Loi {$url}: " . $e->getMessage());
            }
        }

        $this->info("Xong! Them moi: {$added} bai | Trung lap: {$skipped} bai.");

        return self::SUCCESS;
    }

    private function getThumbnail(SimpleXMLElement $item, string $html): ?string
    {
        if (isset($item->enclosure['url'])) {
            $img = trim((string) $item->enclosure['url']);
            if (filter_var($img, FILTER_VALIDATE_URL)) {
                return Str::limit($img, 500, '');
            }
        }

        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m)) {
            $img = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (filter_var($img, FILTER_VALIDATE_URL)) {
                return Str::limit($img, 500, '');
            }
        }

        return null;
    }

    private function cleanContent(string $html, string $fallback, string $link, string $source): string
    {
        $content = trim(strip_tags($html, '<p><br><strong><em><b><i><ul><ol><li>'));

        if ($content === '') {
            $content = '<p>' . e($fallback) . '</p>';
        }

        return $content . '<p class="mt-3 text-muted small">Nguồn: <a href="' . e($link) . '" target="_blank" rel="noopener noreferrer">' . e($source) . '</a></p>';
    }

    private function makeSlug(string $title): string
    {
        $base = Str::limit(Str::slug($title) ?: 'tin-tuc', 240, '');
        $slug = $base;
        $n = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = Str::limit($base, 235, '') . '-' . $n++;
        }

        return $slug;
    }

    private function formatPubDate(string $date): Carbon
    {
        try {
            return trim($date) !== '' ? Carbon::parse($date) : now();
        } catch (Throwable) {
            return now();
        }
    }
}