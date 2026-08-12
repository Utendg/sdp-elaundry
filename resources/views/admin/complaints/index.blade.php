<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Complaints') }}</h2>
    </x-slot>

    @php
        $statusColors = [
            'open' => 'bg-yellow-100 text-yellow-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'resolved' => 'bg-green-100 text-green-800',
            'dismissed' => 'bg-gray-200 text-gray-700',
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <!-- Status filter -->
            <form method="GET" class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Filter:</span>
                @foreach (['' => 'All', 'open' => 'Open', 'under_review' => 'Under review', 'resolved' => 'Resolved', 'dismissed' => 'Dismissed'] as $val => $label)
                    <a href="{{ route('admin.complaints.index', $val ? ['status' => $val] : []) }}"
                       class="px-3 py-1 rounded-full {{ ($filters['status'] ?? '') === $val ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </form>

            <div class="space-y-3">
                @forelse ($complaints as $complaint)
                    <div class="bg-white shadow-sm sm:rounded-lg p-5" x-data="{ manage: false }">
                        <div class="flex items-start justify-between">
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900">{{ $complaint->subject }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ ucfirst($complaint->type) }} ·
                                    by {{ $complaint->complainant?->name }}
                                    @if ($complaint->against) against {{ $complaint->against?->name }} @endif
                                    @if ($complaint->order) · Order {{ $complaint->order->reference }} @endif
                                    · {{ $complaint->created_at->diffForHumans() }}
                                </div>
                                <p class="mt-2 text-sm text-gray-700">{{ $complaint->description }}</p>
                                @if ($complaint->resolution)
                                    <div class="mt-2 text-sm bg-green-50 border border-green-100 rounded p-2">
                                        <span class="font-medium text-green-800">Resolution:</span> {{ $complaint->resolution }}
                                    </div>
                                @endif
                            </div>
                            <span class="ms-3 shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$complaint->status] ?? 'bg-gray-100' }}">
                                {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                            </span>
                        </div>

                        <button @click="manage = !manage" class="mt-3 text-sm text-indigo-600 hover:underline">Manage</button>
                        <form x-show="manage" x-cloak method="POST" action="{{ route('admin.complaints.update', $complaint) }}" class="mt-3 space-y-2">
                            @csrf @method('PATCH')
                            <select name="status" class="block w-full border-gray-300 rounded-md text-sm">
                                @foreach (['open' => 'Open', 'under_review' => 'Under review', 'resolved' => 'Resolved', 'dismissed' => 'Dismissed'] as $val => $label)
                                    <option value="{{ $val }}" @selected($complaint->status === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <textarea name="resolution" rows="2" placeholder="Resolution note (optional)" class="block w-full border-gray-300 rounded-md text-sm">{{ $complaint->resolution }}</textarea>
                            <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Update</button>
                        </form>
                    </div>
                @empty
                    <div class="bg-white shadow-sm sm:rounded-lg px-6 py-10 text-center text-gray-500">No complaints{{ ($filters['status'] ?? '') ? ' with this status' : '' }}.</div>
                @endforelse
            </div>

            {{ $complaints->links() }}
        </div>
    </div>
</x-app-layout>
