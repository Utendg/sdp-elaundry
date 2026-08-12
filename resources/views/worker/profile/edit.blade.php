<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Worker Profile') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <!-- Status + availability -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Approval status</div>
                    @if ($profile->is_approved)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending approval</span>
                    @endif
                </div>
                @if ($profile->is_approved)
                    <form method="POST" action="{{ route('worker.profile.availability') }}">
                        @csrf
                        <button class="px-3 py-2 text-sm rounded-md border {{ $profile->is_available ? 'border-gray-300 hover:bg-gray-50' : 'bg-green-600 text-white hover:bg-green-700' }}">
                            {{ $profile->is_available ? 'Currently available — go busy' : 'Currently busy — go available' }}
                        </button>
                    </form>
                @endif
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format((float) $profile->rating_avg, 1) }}</div>
                    <div class="text-xs text-gray-500">Avg rating</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $profile->ratings_count }}</div>
                    <div class="text-xs text-gray-500">Reviews</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $profile->orders_completed }}</div>
                    <div class="text-xs text-gray-500">Completed</div>
                </div>
            </div>

            <!-- Edit form -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('worker.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="phone" :value="__('Phone number')" />
                        <x-text-input id="phone" name="phone" type="tel" class="block mt-1 w-full" :value="old('phone', auth()->user()->phone)" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="bio" :value="__('Bio / description')" />
                        <textarea id="bio" name="bio" rows="4"
                                  class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                  placeholder="Tell students about your service, turnaround time, specialities…">{{ old('bio', $profile->bio) }}</textarea>
                        <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                    </div>

                    <x-primary-button>{{ __('Save profile') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
