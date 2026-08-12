<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Worker Dashboard') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            @unless ($profile?->is_approved)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4">
                    <strong>Your worker account is pending approval.</strong>
                    An administrator must approve your profile before you can receive orders.
                    You can still complete <a href="{{ route('worker.profile.edit') }}" class="underline">your profile</a> in the meantime.
                </div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <span class="text-sm text-gray-500">Availability</span>
                        <div class="font-semibold {{ $profile->is_available ? 'text-green-700' : 'text-gray-600' }}">
                            {{ $profile->is_available ? 'Available for orders' : 'Marked as busy' }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('worker.profile.availability') }}">
                        @csrf
                        <button class="px-3 py-2 text-sm rounded-md border {{ $profile->is_available ? 'border-gray-300 hover:bg-gray-50' : 'bg-green-600 text-white hover:bg-green-700' }}">
                            {{ $profile->is_available ? 'Go busy' : 'Go available' }}
                        </button>
                    </form>
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
                    <a href="{{ route('worker.orders.show', $order) }}" class="flex items-center justify-between px-6 py-4 border-b border-gray-50 hover:bg-gray-50">
                        <div>
                            <div class="font-medium text-gray-900">{{ $order->reference }}</div>
                            <div class="text-sm text-gray-500">{{ $order->student?->name }} · ₦{{ number_format((float) $order->total_price) }}</div>
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
