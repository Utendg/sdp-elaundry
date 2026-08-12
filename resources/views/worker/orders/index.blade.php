<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Orders') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif
            <x-input-error :messages="$errors->all()" />

            <!-- New requests -->
            <section>
                <h3 class="font-semibold text-gray-900 mb-3">New requests
                    <span class="ms-1 text-xs bg-yellow-100 text-yellow-800 rounded-full px-2 py-0.5">{{ $pending->count() }}</span>
                </h3>
                <div class="space-y-3">
                    @forelse ($pending as $order)
                        <div class="bg-white shadow-sm rounded-lg p-4 flex items-center justify-between">
                            <a href="{{ route('worker.orders.show', $order) }}" class="min-w-0">
                                <div class="font-medium text-gray-900">{{ $order->reference }}</div>
                                <div class="text-sm text-gray-500">{{ $order->student?->name }} · ₦{{ number_format((float) $order->total_price) }} · {{ $order->created_at->diffForHumans() }}</div>
                            </a>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('worker.orders.accept', $order) }}">
                                    @csrf
                                    <button class="px-3 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">Accept</button>
                                </form>
                                <form method="POST" action="{{ route('worker.orders.reject', $order) }}" onsubmit="return confirm('Reject this order?')">
                                    @csrf
                                    <button class="px-3 py-2 text-sm border border-red-300 text-red-700 rounded-md hover:bg-red-50">Reject</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No new requests.</p>
                    @endforelse
                </div>
            </section>

            <!-- Active -->
            <section>
                <h3 class="font-semibold text-gray-900 mb-3">In progress
                    <span class="ms-1 text-xs bg-blue-100 text-blue-800 rounded-full px-2 py-0.5">{{ $active->count() }}</span>
                </h3>
                <div class="space-y-3">
                    @forelse ($active as $order)
                        <a href="{{ route('worker.orders.show', $order) }}" class="bg-white shadow-sm rounded-lg p-4 flex items-center justify-between hover:bg-gray-50">
                            <div>
                                <div class="font-medium text-gray-900">{{ $order->reference }}</div>
                                <div class="text-sm text-gray-500">{{ $order->student?->name }} · ₦{{ number_format((float) $order->total_price) }}</div>
                            </div>
                            <x-order-status-badge :status="$order->status" />
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">Nothing in progress.</p>
                    @endforelse
                </div>
            </section>

            <!-- History -->
            <section>
                <h3 class="font-semibold text-gray-900 mb-3">Recent history</h3>
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    @forelse ($completed as $order)
                        <a href="{{ route('worker.orders.show', $order) }}" class="flex items-center justify-between px-6 py-3 border-b border-gray-50 hover:bg-gray-50">
                            <div>
                                <div class="font-medium text-gray-900">{{ $order->reference }}</div>
                                <div class="text-sm text-gray-500">{{ $order->student?->name }} · ₦{{ number_format((float) $order->total_price) }}</div>
                            </div>
                            <x-order-status-badge :status="$order->status" />
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">No history yet.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
