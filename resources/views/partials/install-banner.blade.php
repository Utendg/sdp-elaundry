{{--
    "Install the app" banner. Android and desktop Chrome/Edge show an Install button (beforeinstallprompt);
    iPhone/iPad Safari shows how to add it with Share > Add to Home Screen. Hidden once installed or dismissed.
--}}
<div x-data="installBanner()" x-init="init()" x-show="visible" x-cloak
     x-effect="document.body.style.paddingBottom = visible ? '9rem' : ''"
     class="fixed inset-x-0 bottom-0 z-50 p-3 sm:p-4" role="dialog" aria-label="Install AUN E-Laundry">
    <div class="mx-auto max-w-xl bg-white border border-gray-200 shadow-lg rounded-xl p-4 flex items-start gap-3">
        <img src="{{ asset('icons/icon-192.png') }}" alt="" class="w-11 h-11 rounded-lg shrink-0">
        <div class="flex-1 text-sm text-gray-700">
            <p class="font-semibold text-gray-900">Install AUN E-Laundry</p>
            <p x-show="!ios">Add it to your home screen and open it like an app.</p>
            <p x-show="ios">Tap the <strong>Share</strong> button in Safari, then <strong>Add to Home Screen</strong>.</p>
            <div class="mt-3 flex gap-2">
                <button type="button" x-show="!ios" @click="install()"
                        class="px-4 py-2 rounded-md bg-aun-navy text-white text-sm font-medium hover:bg-aun-navy-light">Install</button>
                <button type="button" @click="dismiss()"
                        class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Not now</button>
            </div>
        </div>
    </div>
</div>
