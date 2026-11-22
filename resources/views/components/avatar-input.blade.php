@props(['user' => null, 'name' => 'avatar'])

{{-- Photo picker with a live preview. app.js shrinks the chosen photo before upload. --}}
<div data-avatar-field class="flex items-center gap-4">
    <div class="relative shrink-0">
        @if ($user?->hasAvatar())
            <img data-avatar-preview src="{{ $user->avatarUrl() }}" alt="Current photo" class="w-20 h-20 rounded-full object-cover bg-gray-100">
        @else
            <img data-avatar-preview alt="Selected photo" class="hidden w-20 h-20 rounded-full object-cover bg-gray-100">
            <span data-avatar-placeholder class="w-20 h-20 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                <x-heroicon-o-user class="w-10 h-10" aria-hidden="true" />
            </span>
        @endif
    </div>
    <div class="min-w-0">
        <label class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer focus-within:ring-2 focus-within:ring-aun-orange">
            <x-heroicon-o-camera class="w-5 h-5" aria-hidden="true" />
            <span>{{ $user?->hasAvatar() ? 'Change photo' : 'Choose photo' }}</span>
            <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp" data-avatar-input class="sr-only" {{ $attributes }}>
        </label>
        <p data-avatar-filename class="mt-1 text-xs text-gray-500 truncate">JPG, PNG or WebP.</p>
    </div>
</div>
