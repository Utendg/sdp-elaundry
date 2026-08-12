<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Dashboard') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <!-- Welcome / quick action -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Hello, {{ Auth::user()->name }} 👋</h3>
                    <p class="text-gray-600">You have <span class="font-semibold">{{ $activeCount }}</span> active order(s).</p>
                </div>
                <a href="{{ route('student.workers.index') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-aun-navy text-white rounded-md font-medium hover:bg-aun-navy-light">
                    + New laundry order
                </a>
            </div>

            <!-- Recent orders -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Recent orders</h3>
                    <a href="{{ route('student.orders.index') }}" class="text-sm text-aun-navy hover:underline">View all</a>
                </div>
                @forelse ($recentOrders as $order)
                    <a href="{{ route('student.orders.show', $order) }}"
                       class="flex items-center justify-between px-6 py-4 border-b border-gray-50 hover:bg-gray-50">
                        <div>
                            <div class="font-medium text-gray-900">{{ $order->reference }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $order->worker?->name ?? 'Unassigned' }} · ₦{{ number_format((float) $order->total_price) }}
                            </div>
                        </div>
                        <x-order-status-badge :status="$order->status" />
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        No orders yet. <a href="{{ route('student.workers.index') }}" class="text-aun-navy hover:underline">Find a worker</a> to get started.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
