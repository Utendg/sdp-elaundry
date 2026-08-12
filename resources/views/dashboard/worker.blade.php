<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Worker Dashboard') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @unless ($profile?->is_approved)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4">
                    <strong>Your worker account is pending approval.</strong>
                    An administrator must approve your profile before you can receive orders.
                </div>
            @endunless

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">New requests</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $pendingCount }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">In progress</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $activeCount }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Completed</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $completedCount }}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Recent orders</h3>
                </div>
                @forelse ($recentOrders as $order)
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                        <div>
                            <div class="font-medium text-gray-900">{{ $order->reference }}</div>
                            <div class="text-sm text-gray-500">{{ $order->student?->name }} · ₦{{ number_format((float) $order->total_price) }}</div>
                        </div>
                        <x-order-status-badge :status="$order->status" />
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">No orders yet.</div>
                @endforelse
            </div>

            <p class="text-sm text-gray-500">Full order management for workers is coming in the next build increment.</p>
        </div>
    </div>
</x-app-layout>
