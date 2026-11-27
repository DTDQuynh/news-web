const navigationEntry = performance.getEntriesByType('navigation')[0];

const offlineStatus = document.querySelector('[data-offline-status]');
const canUseServiceWorker = window.isSecureContext && 'serviceWorker' in navigator;
let wasOffline = !navigator.onLine;
let restoreStatusTimeout;

const postToActiveServiceWorker = (worker, message) => {
    if (!worker) {
        return;
    }

    const postMessage = () => {
        if (worker.state === 'activated') {
            worker.removeEventListener('statechange', postMessage);
            worker.postMessage(message);
        } else if (worker.state === 'redundant') {
            worker.removeEventListener('statechange', postMessage);
        }
    };

    postMessage();
    if (worker.state !== 'activated' && worker.state !== 'redundant') {
        worker.addEventListener('statechange', postMessage);
    }
};

const updateOfflineStatus = () => {
    if (!offlineStatus) {
        return;
    }

    if (!navigator.onLine) {
        wasOffline = true;
        window.clearTimeout(restoreStatusTimeout);
        offlineStatus.textContent = 'Bạn đang ở chế độ ngoại tuyến';
        offlineStatus.hidden = false;
        return;
    }

    if (wasOffline) {
        wasOffline = false;
        offlineStatus.textContent = 'Đã khôi phục kết nối';
        offlineStatus.hidden = false;
        window.clearTimeout(restoreStatusTimeout);
        restoreStatusTimeout = window.setTimeout(() => {
            offlineStatus.hidden = true;
        }, 4000);
        return;
    }

    offlineStatus.hidden = true;
};

window.addEventListener('online', updateOfflineStatus);
window.addEventListener('offline', updateOfflineStatus);
updateOfflineStatus();

if (canUseServiceWorker) {
    navigator.serviceWorker.register('/sw.js', { scope: '/' })
        .then(async () => {
            const registration = await navigator.serviceWorker.ready;
            postToActiveServiceWorker(registration.active, {
                type: 'CACHE_SAVED_POSTS',
                urls: window.newsSavedPostUrls ?? [],
            });
            postToActiveServiceWorker(registration.active, {
                type: 'CACHE_STATIC_ASSETS',
                urls: window.newsStaticAssetUrls ?? [],
            });
        })
        .catch((error) => {
            console.error('Could not register the offline reading service worker.', error);
        });
}

if (navigationEntry?.type === 'reload') {
    const currentUrl = new URL(window.location.href);

    if (currentUrl.pathname === '/' && currentUrl.searchParams.has('page')) {
        currentUrl.searchParams.delete('page');
        window.location.replace(`${currentUrl.pathname}${currentUrl.search}${currentUrl.hash}`);
    }
}

const themeToggle = document.querySelector('[data-theme-toggle]');

if (themeToggle instanceof HTMLButtonElement) {
    const updateThemeToggle = (isDarkModeEnabled) => {
        themeToggle.setAttribute('aria-pressed', String(isDarkModeEnabled));
        themeToggle.setAttribute(
            'aria-label',
            isDarkModeEnabled ? 'Bật chế độ sáng' : 'Bật chế độ tối',
        );
    };

    updateThemeToggle(document.documentElement.classList.contains('dark'));

    themeToggle.addEventListener('click', () => {
        const isDarkModeEnabled = document.documentElement.classList.toggle('dark');
        updateThemeToggle(isDarkModeEnabled);
        localStorage.setItem('theme', isDarkModeEnabled ? 'dark' : 'light');
    });
}

const loginModal = document.querySelector('[data-login-modal]');

if (loginModal instanceof HTMLDialogElement && loginModal.hasAttribute('data-open-on-load')) {
    loginModal.showModal();
}

document.addEventListener('click', (event) => {
    const authPanelTrigger = event.target instanceof Element
        ? event.target.closest('[data-auth-panel-show]')
        : null;

    if (authPanelTrigger && loginModal instanceof HTMLDialogElement) {
        const nextPanel = authPanelTrigger.getAttribute('data-auth-panel-show');
        const panel = [...loginModal.querySelectorAll('[data-auth-panel]')]
            .find((item) => item.getAttribute('data-auth-panel') === nextPanel);

        if (panel && loginModal.open) {
            event.preventDefault();
            loginModal.querySelectorAll('[data-auth-panel]').forEach((item) => {
                item.hidden = item !== panel;
            });
            loginModal.setAttribute('aria-labelledby', panel.querySelector('h1')?.id ?? '');
            panel.querySelector('input:not([type="hidden"])')?.focus();
        }
    }

    const loginTrigger = event.target instanceof Element
        ? event.target.closest('[data-login-modal-open]')
        : null;

    if (loginTrigger && loginModal instanceof HTMLDialogElement && loginModal.showModal) {
        event.preventDefault();
        loginModal.showModal();
        loginModal.querySelector('.login-modal__close')?.focus();
    }

    if (event.target === loginModal && loginModal instanceof HTMLDialogElement) {
        loginModal.close();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    if (loginModal instanceof HTMLDialogElement && loginModal.open) {
        loginModal.close();
    }
});

document.querySelectorAll('[data-comments]').forEach((comments) => {
    const list = comments.querySelector('[data-comments-list]');
    const status = comments.querySelector('[data-comments-status]');
    const pagination = comments.querySelector('[data-comments-pagination]');
    const pageLabel = comments.querySelector('[data-comments-page-label]');
    const form = comments.querySelector('[data-comment-form]');
    const formStatus = comments.querySelector('[data-comment-form-status]');
    const input = comments.querySelector('[data-comment-input]');
    const count = comments.querySelector('[data-comment-count]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const draftKey = form?.dataset.draftKey;
    let currentPage = 1;
    let lastPage = 1;
    let perPage = 10;
    let totalComments = 0;
    let loadRequestId = 0;

    const request = async (url, options = {}) => {
        const response = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                ...options.headers,
            },
        });
        const data = await response.json();

        if (!response.ok) {
            const validationError = Object.values(data.errors ?? {})[0]?.[0];
            throw new Error(validationError ?? data.message ?? 'Đã xảy ra lỗi. Vui lòng thử lại.');
        }

        return data;
    };

    const createCommentCard = (comment) => {
        const card = document.createElement('article');
        card.className = 'article-page__comment';

        const header = document.createElement('div');
        header.className = 'article-page__comment-header';
        const author = document.createElement('strong');
        author.textContent = comment.author;
        const date = document.createElement('time');
        date.textContent = comment.created_at;
        header.append(author, date);
        card.appendChild(header);

        const content = document.createElement('p');
        content.textContent = comment.content;
        card.appendChild(content);

        if (!comment.is_approved) {
            const pending = document.createElement('span');
            pending.className = 'article-page__comment-pending';
            pending.textContent = 'Chờ duyệt';
            card.appendChild(pending);
        }

        const actions = document.createElement('div');
        actions.className = 'article-page__comment-actions';

        if (comment.can_moderate) {
            const moderate = document.createElement('button');
            moderate.type = 'button';
            moderate.dataset.commentAction = 'moderate';
            moderate.dataset.commentId = String(comment.id);
            moderate.dataset.approved = String(!comment.is_approved);
            moderate.textContent = comment.is_approved ? 'Ẩn' : 'Duyệt';
            actions.appendChild(moderate);
        }

        if (comment.can_delete) {
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.dataset.commentAction = 'delete';
            remove.dataset.commentId = String(comment.id);
            remove.textContent = 'Xóa';
            actions.appendChild(remove);
        }

        if (actions.childElementCount > 0) {
            card.appendChild(actions);
        }

        return card;
    };

    const updatePagination = () => {
        lastPage = Math.max(1, Math.ceil(totalComments / perPage));
        pagination.hidden = lastPage < 2;
        pageLabel.textContent = `Trang ${currentPage}/${lastPage}`;
        pagination.querySelector('[data-comments-page="previous"]').disabled = currentPage <= 1;
        pagination.querySelector('[data-comments-page="next"]').disabled = currentPage >= lastPage;
    };

    const loadComments = async (page = 1, keepCurrentComments = false) => {
        const requestId = ++loadRequestId;
        status.textContent = 'Đang tải bình luận...';
        if (!keepCurrentComments) {
            list.replaceChildren();
        }

        try {
            const url = new URL(comments.dataset.commentsUrl, window.location.origin);
            url.searchParams.set('page', String(page));
            const result = await request(url);
            if (requestId !== loadRequestId) {
                return;
            }

            currentPage = result.meta.current_page;
            lastPage = result.meta.last_page;
            perPage = result.meta.per_page;
            totalComments = result.meta.total;
            list.replaceChildren();
            result.data.forEach((comment) => list.appendChild(createCommentCard(comment)));
            status.textContent = totalComments
                ? `${totalComments} bình luận`
                : 'Chưa có bình luận nào.';
            updatePagination();
        } catch (error) {
            if (requestId === loadRequestId) {
                status.textContent = error.message;
            }
        }
    };

    if (form && input && count) {
        if (form.dataset.commentAuthenticated === 'true' && draftKey) {
            const draft = sessionStorage.getItem(draftKey);
            if (draft) {
                input.value = draft;
                count.textContent = `${draft.length}/500`;
            }
        }

        input.addEventListener('input', () => {
            count.textContent = `${input.value.length}/500`;

            if (form.dataset.commentAuthenticated !== 'true' && draftKey) {
                sessionStorage.setItem(draftKey, input.value);
            }
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            formStatus.textContent = '';

            if (form.dataset.commentAuthenticated !== 'true') {
                if (draftKey) {
                    sessionStorage.setItem(draftKey, input.value);
                }

                if (loginModal instanceof HTMLDialogElement) {
                    loginModal.showModal();
                    loginModal.querySelector('.login-modal__close')?.focus();
                } else {
                    window.location.href = form.dataset.loginUrl;
                }
                return;
            }

            const submit = form.querySelector('[type="submit"]');
            submit.disabled = true;

            try {
                const result = await request(form.action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ content: input.value }),
                });
                loadRequestId += 1;
                form.reset();
                count.textContent = '0/500';
                if (draftKey) {
                    sessionStorage.removeItem(draftKey);
                }
                formStatus.textContent = result.message;

                if (currentPage !== 1) {
                    currentPage = 1;
                    list.replaceChildren();
                }

                list.prepend(createCommentCard(result.data));
                if (list.children.length > perPage) {
                    list.lastElementChild.remove();
                }
                totalComments += 1;
                status.textContent = `${totalComments} bình luận`;
                updatePagination();
                loadComments(1, true);
            } catch (error) {
                formStatus.textContent = error.message;
            } finally {
                submit.disabled = false;
            }
        });
    }

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('[data-comments-page]');
        if (!button) {
            return;
        }

        loadComments(currentPage + (button.dataset.commentsPage === 'next' ? 1 : -1));
    });

    list.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-comment-action]');
        if (!button) {
            return;
        }

        button.disabled = true;
        const commentUrl = `${comments.dataset.commentsUrl}/${button.dataset.commentId}`;

        try {
            if (button.dataset.commentAction === 'delete') {
                await request(commentUrl, { method: 'DELETE' });
            } else {
                await request(`${commentUrl}/approval`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ is_approved: button.dataset.approved === 'true' }),
                });
            }

            await loadComments(currentPage);
        } catch (error) {
            status.textContent = error.message;
            button.disabled = false;
        }
    });

    loadComments();
});
