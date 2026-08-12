<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Admin Dashboard') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="{{ route('admin.workers.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow">
                    <div class="text-sm text-gray-500">Students</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $totalStudents }}</div>
                </a>
                <a href="{{ route('admin.workers.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow">
                    <div class="text-sm text-gray-500">Workers</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $totalWorkers }}</div>
                </a>
                <a href="{{ route('admin.workers.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow">
                    <div class="text-sm text-gray-500">Pending approval</div>
                    <div class="text-3xl font-bold text-amber-600">{{ $pendingWorkers }}</div>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow">
                    <div class="text-sm text-gray-500">Total orders</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $totalOrders }}</div>
                </a>
                <a href="{{ route('admin.complaints.index') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow">
                    <div class="text-sm text-gray-500">Open complaints</div>
                    <div class="text-3xl font-bold text-red-600">{{ $openComplaints }}</div>
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Latest orders</h3>
                </div>
                @forelse ($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between px-6 py-4 border-b border-gray-50 hover:bg-gray-50">
                        <div>
                            <div class="font-medium text-gray-900">{{ $order->reference }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $order->student?->name }} → {{ $order->worker?->name ?? 'Unassigned' }} · ₦{{ number_format((float) $order->total_price) }}
                            </div>
                        </div>
                        <x-order-status-badge :status="$order->status" />
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">No orders yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
