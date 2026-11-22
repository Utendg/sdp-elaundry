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

// ----- Show / hide password -------------------------------------------------------------
// Adds an eye button inside every password box (login, register, reset, profile, admin forms).
const EYE = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
const EYE_OFF = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-6.5 0-10-7-10-7a18.5 18.5 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c6.5 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"/><path d="M1 1l22 22"/></svg>';

function addPasswordToggle(input) {
    if (input.dataset.reveal) return;
    input.dataset.reveal = '1';

    const wrapper = document.createElement('div');
    wrapper.style.position = 'relative';
    // keep the input's top margin outside the wrapper so the button stays centred on the box
    ['mt-1', 'mt-2'].forEach(cls => { if (input.classList.contains(cls)) { input.classList.remove(cls); wrapper.classList.add(cls); } });
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    input.style.paddingRight = '2.75rem';
    if (!input.style.width) input.style.width = '100%';

    const button = document.createElement('button');
    button.type = 'button';
    button.innerHTML = EYE;
    button.setAttribute('aria-label', 'Show password');
    button.setAttribute('aria-pressed', 'false');
    Object.assign(button.style, {
        position: 'absolute', top: '0', bottom: '0', right: '0', width: '2.75rem',
        display: 'flex', alignItems: 'center', justifyContent: 'center',
        background: 'transparent', border: '0', color: '#6b7280', cursor: 'pointer',
    });
    button.addEventListener('click', () => {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.innerHTML = show ? EYE_OFF : EYE;
        button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        button.setAttribute('aria-pressed', String(show));
        input.focus();
    });
    wrapper.appendChild(button);
}

const enhancePasswords = () => document.querySelectorAll('input[type="password"]').forEach(addPasswordToggle);
document.addEventListener('DOMContentLoaded', enhancePasswords);
// fields that appear later (e.g. the "New admin account" form opened with Alpine)
new MutationObserver(enhancePasswords).observe(document.documentElement, { childList: true, subtree: true });

// ----- Profile photo picker --------------------------------------------------------------
// Shows a preview and shrinks the photo to at most 512px (JPEG) before it is uploaded,
// so phone photos of several MB become a few dozen KB. If the browser cannot read the
// image, the original file is sent and the server validates it.
const AVATAR_MAX = 512;

function shrinkImage(file) {
    return new Promise(resolve => {
        const url = URL.createObjectURL(file);
        const img = new Image();
        img.onload = () => {
            const scale = Math.min(1, AVATAR_MAX / Math.max(img.width, img.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(img.width * scale);
            canvas.height = Math.round(img.height * scale);
            canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
            URL.revokeObjectURL(url);
            canvas.toBlob(blob => {
                if (!blob) return resolve(file);
                const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                resolve(new File([blob], name, { type: 'image/jpeg' }));
            }, 'image/jpeg', 0.85);
        };
        img.onerror = () => { URL.revokeObjectURL(url); resolve(file); };
        img.src = url;
    });
}

document.addEventListener('change', async event => {
    const input = event.target;
    if (!input.matches?.('input[data-avatar-input]') || !input.files?.length) return;

    const field = input.closest('[data-avatar-field]');
    const original = input.files[0];
    const file = original.type.startsWith('image/') ? await shrinkImage(original) : original;

    if (file !== original && window.DataTransfer) {
        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
    }

    const preview = field?.querySelector('[data-avatar-preview]');
    if (preview) {
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        field.querySelector('[data-avatar-placeholder]')?.classList.add('hidden');
    }
    const label = field?.querySelector('[data-avatar-filename]');
    if (label) label.textContent = original.name;
});

Alpine.start();
