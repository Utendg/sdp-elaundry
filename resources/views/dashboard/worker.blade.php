<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Agent Dashboard') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            @unless (Auth::user()->hasAvatar())
                <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-camera class="w-6 h-6 shrink-0" aria-hidden="true" />
                        <div><strong>Add a photo of yourself.</strong> Every laundry agent needs a clear profile photo so students can recognise who collects their clothes.</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="shrink-0 px-4 py-2 bg-aun-navy text-white text-sm rounded-md hover:bg-aun-navy-light text-center">Upload photo</a>
                </div>
            @endunless

            @if ($profile?->isDeclined())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    <strong>Your laundry agent application was declined.</strong>
                    @if ($profile->decline_reason) Reason: {{ $profile->decline_reason }}. @endif
                    Please contact the Residence Life office if you think this is a mistake.
                </div>
            @elseif (! $profile?->is_approved)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4">
                    <strong>Your agent account is pending approval.</strong>
                    An administrator must approve your account before you appear to students or can receive orders.
                    You will get a notification once you are approved. You can still complete
                    <a href="{{ route('worker.profile.edit') }}" class="underline">your profile</a> in the meantime.
                </div>
            @else
                <div class="bg-white shadow-sm rounded-lg p-4 flex items-center justify-between">
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
            @endif

            @if ($profile?->is_approved)
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

            <div class="bg-white shadow-sm rounded-lg">
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
            @endif
        </div>
    </div>
</x-app-layout>
