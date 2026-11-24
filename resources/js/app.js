const navigationEntry = performance.getEntriesByType('navigation')[0];

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
