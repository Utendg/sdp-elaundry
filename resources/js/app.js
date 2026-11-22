import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// ----- Installable app (PWA) -----------------------------------------------------------
if ('serviceWorker' in navigator && window.isSecureContext) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => { /* the site still works without it */ });
    });
}

let deferredInstall = null;
window.addEventListener('beforeinstallprompt', event => {
    event.preventDefault();
    deferredInstall = event;
    window.dispatchEvent(new CustomEvent('elaundry:installable'));
});

const DISMISS_KEY = 'elaundry-install-dismissed';
const isStandalone = () => window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
const wasDismissed = () => { try { return localStorage.getItem(DISMISS_KEY) === '1'; } catch { return false; } };

Alpine.data('installBanner', () => ({
    visible: false,
    ios: false,
    init() {
        if (isStandalone() || wasDismissed()) return;
        const ua = window.navigator.userAgent;
        this.ios = /iphone|ipad|ipod/i.test(ua) && /safari/i.test(ua) && !/crios|fxios|edgios/i.test(ua);
        if (this.ios) { this.visible = true; return; }
        if (deferredInstall) this.visible = true;
        window.addEventListener('elaundry:installable', () => { this.visible = true; });
        window.addEventListener('appinstalled', () => { this.visible = false; });
    },
    async install() {
        if (!deferredInstall) return;
        deferredInstall.prompt();
        await deferredInstall.userChoice;
        deferredInstall = null;
        this.visible = false;
    },
    dismiss() {
        this.visible = false;
        try { localStorage.setItem(DISMISS_KEY, '1'); } catch { /* private mode: just hide it */ }
    },
}));

Alpine.start();
