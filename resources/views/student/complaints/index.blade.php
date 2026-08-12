<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Complaints') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <!-- New general complaint -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6" x-data="{ open: false }">
                <button @click="open = !open" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                    + File a complaint
                </button>
                <form x-show="open" x-cloak method="POST" action="{{ route('student.complaints.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <select name="type" class="block w-full border-gray-300 rounded-md text-sm">
                        <option value="damaged">Damaged item</option>
                        <option value="missing">Missing item</option>
                        <option value="delayed">Delayed service</option>
                        <option value="pricing">Pricing issue</option>
                        <option value="conduct">Worker conduct</option>
                        <option value="other">Other</option>
                    </select>
                    <input type="text" name="subject" placeholder="Subject" required class="block w-full border-gray-300 rounded-md text-sm">
                    <textarea name="description" rows="3" placeholder="Describe the issue" required class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                    <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Submit</button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                @forelse ($complaints as $complaint)
                    <div class="px-6 py-4 border-b border-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="font-medium text-gray-900">{{ $complaint->subject }}</div>
                            @php
                                $statusColors = [
                                    'open' => 'bg-yellow-100 text-yellow-800',
                                    'under_review' => 'bg-blue-100 text-blue-800',
                                    'resolved' => 'bg-green-100 text-green-800',
                                    'dismissed' => 'bg-gray-200 text-gray-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$complaint->status] ?? 'bg-gray-100' }}">
                                {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ ucfirst($complaint->type) }}
                            @if ($complaint->order) · Order {{ $complaint->order->reference }} @endif
                            · {{ $complaint->created_at->diffForHumans() }}
                        </div>
                        <p class="mt-1 text-sm text-gray-700">{{ $complaint->description }}</p>
                        @if ($complaint->resolution)
                            <div class="mt-2 text-sm bg-green-50 border border-green-100 rounded p-2">
                                <span class="font-medium text-green-800">Resolution:</span> {{ $complaint->resolution }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-gray-500">You haven't filed any complaints.</div>
                @endforelse
            </div>

            {{ $complaints->links() }}
        </div>
    </div>
</x-app-layout>
