<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">{{ __('Profile photo') }}</h2>
        <p class="mt-1 text-sm text-gray-600">
            @if ($user->isWorker())
                Students see this photo when they choose a worker and when you collect their clothes. Use a clear, recent photo of your face.
            @else
                Optional. Your laundry worker sees it on your orders.
            @endif
        </p>
    </header>

    <form method="post" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
        @csrf
        <x-avatar-input :user="$user" required />
        <x-input-error :messages="$errors->avatar->get('avatar')" />

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save photo') }}</x-primary-button>
            @if (session('status') === 'avatar-updated')
                <p class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @elseif (session('status') === 'avatar-removed')
                <p class="text-sm text-gray-600">{{ __('Photo removed.') }}</p>
            @endif
        </div>
    </form>

    @if ($user->hasAvatar() && ! $user->isWorker())
        <form method="post" action="{{ route('profile.avatar.destroy') }}" class="mt-3">
            @csrf
            @method('delete')
            <button class="text-sm text-red-600 hover:underline">{{ __('Remove photo') }}</button>
        </form>
    @endif
</section>
