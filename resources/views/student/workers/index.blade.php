<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Find a Laundry Worker') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filter -->
            <form method="GET" class="flex items-center gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="my_dorm" value="1" onchange="this.form.submit()"
                           @checked($sameDormOnly) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Show only workers in my dorm
                </label>
            </form>

            @if ($workers->isEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center text-gray-500">
                    No approved workers available{{ $sameDormOnly ? ' in your dorm' : '' }} right now. Please check back later.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($workers as $worker)
                        <div class="bg-white shadow-sm rounded-lg p-5 flex flex-col">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $worker->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $worker->dorm?->name ?? '—' }}</div>
                                </div>
                                @if (! $worker->workerProfile->is_available)
                                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Busy</span>
                                @endif
                            </div>

                            <div class="mt-3">
                                <x-stars :value="$worker->workerProfile->rating_avg" :count="$worker->workerProfile->ratings_count" />
                            </div>

                            @if ($worker->workerProfile->bio)
                                <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $worker->workerProfile->bio }}</p>
                            @endif

                            <div class="mt-2 text-xs text-gray-500">{{ $worker->completed_orders_count }} orders completed</div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('student.workers.show', $worker) }}"
                                   class="flex-1 text-center px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                    View profile
                                </a>
                                @if ($worker->workerProfile->is_available)
                                    <a href="{{ route('student.orders.create', ['worker' => $worker->id]) }}"
                                       class="flex-1 text-center px-3 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        Book
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
