const STATIC_CACHE = 'news-web-static-v2';
const ARTICLE_CACHE = 'news-web-articles-v1';
const MAX_OFFLINE_ARTICLES = 20;
const ARTICLE_PATH = /^\/bai-viet\/[^/]+$/;
const STATIC_ASSET_TYPES = new Set(['font', 'image', 'script', 'style']);

self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
        const cache = await caches.open(STATIC_CACHE);
        await cache.addAll([
            '/manifest.json',
            '/favicon.png',
            '/images/pwa-192.png',
            '/images/lightbulb-on.png',
            '/images/lightbulb-off.png',
            '/images/url-icon.png',
            '/images/bookmark-icon.png',
            '/images/history-icon.png',
        ]);
        await self.skipWaiting();
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const cacheNames = await caches.keys();
        await Promise.all(cacheNames
            .filter((name) => name.startsWith('news-web-') && ![STATIC_CACHE, ARTICLE_CACHE].includes(name))
            .map((name) => caches.delete(name)));
        await self.clients.claim();
    })());
});

self.addEventListener('message', (event) => {
    if (event.data?.type === 'CACHE_STATIC_ASSETS') {
        const urls = [...new Set(event.data.urls ?? [])]
            .filter((value) => typeof value === 'string');

        event.waitUntil(Promise.all(urls.map((url) => cacheStaticAssetUrl(url))));
        return;
    }

    if (event.data?.type !== 'CACHE_SAVED_POSTS') {
        return;
    }

    const urls = [...new Set(event.data.urls ?? [])]
        .filter((value) => typeof value === 'string')
        .slice(0, MAX_OFFLINE_ARTICLES);

    event.waitUntil(Promise.allSettled(urls.map((url) => cacheOfflineArticle(url))));
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate' && ARTICLE_PATH.test(url.pathname)) {
        event.waitUntil(touchOrCacheArticle(request.url).catch((error) => {
            console.warn('Could not cache article for offline reading.', error);
        }));
        event.respondWith(fetch(request).catch(async () => {
            const cache = await caches.open(ARTICLE_CACHE);
            return await cache.match(articleCacheRequest(request.url)) ?? offlineResponse();
        }));
        return;
    }

    if (STATIC_ASSET_TYPES.has(request.destination)) {
        event.respondWith(cacheStaticAsset(request));
    }
});

function articleCacheRequest(value) {
    const url = new URL(value);
    url.hash = '';
    url.searchParams.delete('offline_prefetch');

    return new Request(url.href, { method: 'GET' });
}

async function touchOrCacheArticle(url) {
    const cache = await caches.open(ARTICLE_CACHE);
    const key = articleCacheRequest(url);
    const cached = await cache.match(key);

    if (cached) {
        await storeArticle(cache, key, cached);
        return;
    }

    await cacheOfflineArticle(url);
}

async function cacheOfflineArticle(value) {
    const url = new URL(value, self.location.origin);

    if (url.origin !== self.location.origin || !ARTICLE_PATH.test(url.pathname)) {
        return;
    }

    const cache = await caches.open(ARTICLE_CACHE);
    const key = articleCacheRequest(url.href);
    if (await cache.match(key)) {
        return;
    }

    url.searchParams.set('offline_prefetch', '1');
    const response = await fetch(url.href, {
        cache: 'no-store',
        credentials: 'omit',
        headers: {
            Accept: 'text/html',
            'X-PWA-Prefetch': '1',
        },
    });

    if (!response.ok || !response.headers.get('content-type')?.includes('text/html')) {
        return;
    }

    await storeArticle(cache, key, response);
}

async function storeArticle(cache, key, response) {
    const copy = response.clone();
    const headers = new Headers(copy.headers);
    headers.set('X-PWA-Cached-At', String(Date.now()));
    await cache.put(key, new Response(copy.body, {
        status: copy.status,
        statusText: copy.statusText,
        headers,
    }));

    const entries = await Promise.all((await cache.keys()).map(async (request) => {
        const cached = await cache.match(request);

        return {
            request,
            cachedAt: Number(cached?.headers.get('X-PWA-Cached-At')) || 0,
        };
    }));

    entries.sort((a, b) => a.cachedAt - b.cachedAt);
    while (entries.length > MAX_OFFLINE_ARTICLES) {
        await cache.delete(entries.shift().request);
    }
}

async function cacheStaticAsset(request) {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);

    try {
        const response = await fetch(request);
        if (response.ok) {
            await cache.put(request, response.clone());
        }

        return response.ok || !cached ? response : cached;
    } catch (error) {
        if (cached) {
            return cached;
        }

        throw error;
    }
}

async function cacheStaticAssetUrl(value) {
    const url = new URL(value, self.location.origin);

    if (url.origin !== self.location.origin) {
        return;
    }

    const response = await fetch(url.href);
    if (response.ok) {
        const cache = await caches.open(STATIC_CACHE);
        await cache.put(url.href, response);
    }
}

function offlineResponse() {
    return new Response(
        '<!doctype html><html lang="vi"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Đang ngoại tuyến</title><body style="font:16px Arial,sans-serif;max-width:42rem;margin:10vh auto;padding:1rem;color:#1e293b"><h1>Hiện bạn đang ngoại tuyến</h1><p>Bài viết này chưa được lưu để đọc offline. Hãy kết nối mạng để mở bài viết trước.</p><a href="/">Về trang chủ</a></body></html>',
        {
            status: 503,
            headers: { 'Content-Type': 'text/html; charset=utf-8' },
        },
    );
}
