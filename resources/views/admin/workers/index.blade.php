<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Workers') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <!-- Pending approval -->
            <section>
                <h3 class="font-semibold text-gray-900 mb-3">Awaiting approval
                    <span class="ms-1 text-xs bg-yellow-100 text-yellow-800 rounded-full px-2 py-0.5">{{ $pending->count() }}</span>
                </h3>
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    @forelse ($pending as $worker)
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                            <div>
                                <div class="font-medium text-gray-900">{{ $worker->name }}</div>
                                <div class="text-sm text-gray-500">{{ $worker->email }} · {{ $worker->dorm?->name ?? 'No dorm' }} · {{ $worker->phone ?? 'No phone' }}</div>
                            </div>
                            <form method="POST" action="{{ route('admin.workers.approve', $worker) }}">
                                @csrf
                                <button class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">Approve</button>
                            </form>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">No workers awaiting approval.</div>
                    @endforelse
                </div>
            </section>

            <!-- Approved -->
            <section>
                <h3 class="font-semibold text-gray-900 mb-3">Approved workers
                    <span class="ms-1 text-xs bg-green-100 text-green-800 rounded-full px-2 py-0.5">{{ $approved->count() }}</span>
                </h3>
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    @forelse ($approved as $worker)
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                            <div>
                                <div class="font-medium text-gray-900">{{ $worker->name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $worker->dorm?->name ?? '—' }} ·
                                    <x-stars :value="$worker->workerProfile->rating_avg" :count="$worker->workerProfile->ratings_count" class="align-middle" /> ·
                                    {{ $worker->workerProfile->orders_completed }} completed
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.workers.revoke', $worker) }}" onsubmit="return confirm('Revoke approval for {{ $worker->name }}?')">
                                @csrf
                                <button class="px-3 py-2 text-sm border border-red-300 text-red-700 rounded-md hover:bg-red-50">Revoke</button>
                            </form>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">No approved workers yet.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
