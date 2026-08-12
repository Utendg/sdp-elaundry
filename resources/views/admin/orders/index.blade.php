<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('All Orders') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filters -->
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Reference</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="ELD-…" class="border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select name="status" class="border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Dorm</label>
                    <select name="dorm_id" class="border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach ($dorms as $dorm)
                            <option value="{{ $dorm->id }}" @selected((string) ($filters['dorm_id'] ?? '') === (string) $dorm->id)>{{ $dorm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-sm text-gray-600">Reset</a>
            </form>

            <!-- Table -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="px-4 py-3">Ref</th>
                            <th class="px-4 py-3">Student</th>
                            <th class="px-4 py-3">Worker</th>
                            <th class="px-4 py-3">Dorm</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Placed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                                <td class="px-4 py-3 font-medium text-indigo-700">{{ $order->reference }}</td>
                                <td class="px-4 py-3">{{ $order->student?->name }}</td>
                                <td class="px-4 py-3">{{ $order->worker?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $order->dorm?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">₦{{ number_format((float) $order->total_price) }}</td>
                                <td class="px-4 py-3"><x-order-status-badge :status="$order->status" /></td>
                                <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('d M, g:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No orders match your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
