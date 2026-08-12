<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Orders') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                @forelse ($orders as $order)
                    <a href="{{ route('student.orders.show', $order) }}"
                       class="flex items-center justify-between px-6 py-4 border-b border-gray-50 hover:bg-gray-50">
                        <div>
                            <div class="font-medium text-gray-900">{{ $order->reference }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $order->worker?->name ?? 'Unassigned' }} ·
                                {{ $order->items_count ?? $order->items()->count() }} item(s) ·
                                ₦{{ number_format((float) $order->total_price) }}
                            </div>
                            <div class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, g:i A') }}</div>
                        </div>
                        <x-order-status-badge :status="$order->status" />
                    </a>
                @empty
                    <div class="px-6 py-10 text-center text-gray-500">
                        You haven't placed any orders yet.
                        <a href="{{ route('student.workers.index') }}" class="text-indigo-600 hover:underline">Find a worker</a>.
                    </div>
                @endforelse
            </div>

            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
