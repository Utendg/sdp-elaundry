<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $worker->name }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $worker->name }}</h3>
                        <div class="text-sm text-gray-500">{{ $worker->dorm?->name ?? '—' }}</div>
                        <div class="mt-2">
                            <x-stars :value="$worker->workerProfile->rating_avg" :count="$worker->workerProfile->ratings_count" />
                        </div>
                        <div class="mt-1 text-sm text-gray-500">{{ $worker->workerProfile->orders_completed }} orders completed</div>
                    </div>
                    @if ($worker->workerProfile->is_available)
                        <a href="{{ route('student.orders.create', ['worker' => $worker->id]) }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Book this worker</a>
                    @else
                        <span class="text-xs bg-gray-200 text-gray-600 px-3 py-1 rounded-full">Currently unavailable</span>
                    @endif
                </div>

                @if ($worker->workerProfile->bio)
                    <p class="mt-4 text-gray-700">{{ $worker->workerProfile->bio }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Recent reviews</h3>
                </div>
                @forelse ($worker->ratingsReceived as $rating)
                    <div class="px-6 py-4 border-b border-gray-50">
                        <div class="flex items-center justify-between">
                            <x-stars :value="$rating->stars" />
                            <span class="text-xs text-gray-400">{{ $rating->created_at->diffForHumans() }}</span>
                        </div>
                        @if ($rating->comment)
                            <p class="mt-1 text-sm text-gray-700">{{ $rating->comment }}</p>
                        @endif
                        <div class="mt-1 text-xs text-gray-500">— {{ $rating->rater?->name ?? 'Student' }}</div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">No reviews yet.</div>
                @endforelse
            </div>

            <a href="{{ route('student.workers.index') }}" class="text-sm text-indigo-600 hover:underline">← Back to workers</a>
        </div>
    </div>
</x-app-layout>
