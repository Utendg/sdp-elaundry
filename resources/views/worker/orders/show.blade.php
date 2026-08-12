<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order {{ $order->reference }}</h2>
            <x-order-status-badge :status="$order->status" />
        </div>
    </x-slot>

    @php
        $statusLabels = ['picked_up' => 'Mark picked up', 'washing' => 'Start washing', 'ironing' => 'Start ironing', 'ready' => 'Mark ready', 'completed' => 'Mark completed'];
    @endphp

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif
            <x-input-error :messages="$errors->all()" />

            <!-- Customer + items -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Student:</span> <span class="font-medium">{{ $order->student?->name }}</span></div>
                    <div><span class="text-gray-500">Phone:</span> <span class="font-medium">{{ $order->student?->phone ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Placed:</span> <span class="font-medium">{{ $order->created_at->format('d M Y, g:i A') }}</span></div>
                    @if ($order->pickup_location)
                        <div><span class="text-gray-500">Location:</span> <span class="font-medium">{{ $order->pickup_location }}</span></div>
                    @endif
                    @if ($order->scheduled_pickup_at)
                        <div><span class="text-gray-500">Preferred pickup:</span> <span class="font-medium">{{ $order->scheduled_pickup_at->format('d M, g:i A') }}</span></div>
                    @endif
                </div>

                @if ($order->notes)
                    <div class="mt-4 text-sm bg-amber-50 border border-amber-100 rounded p-3">
                        <span class="font-medium text-amber-800">Instructions:</span> {{ $order->notes }}
                    </div>
                @endif

                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">Item</th><th class="py-2 text-center">Qty</th><th class="py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr class="border-b border-gray-50">
                                <td class="py-2">{{ $item->item_name }} <span class="text-xs text-gray-400">({{ $item->service }})</span></td>
                                <td class="py-2 text-center">{{ $item->quantity }}</td>
                                <td class="py-2 text-right">₦{{ number_format((float) $item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="2" class="py-2 text-right font-semibold">Total</td>
                            <td class="py-2 text-right font-bold text-aun-navy">₦{{ number_format((float) $order->total_price) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Update status</h3>

                @if ($order->status === 'pending')
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('worker.orders.accept', $order) }}">
                            @csrf
                            <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Accept order</button>
                        </form>
                        <form method="POST" action="{{ route('worker.orders.reject', $order) }}" class="flex gap-2" onsubmit="return confirm('Reject this order?')">
                            @csrf
                            <input type="text" name="reason" placeholder="Reason (optional)" class="border-gray-300 rounded-md text-sm">
                            <button class="px-4 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50">Reject</button>
                        </form>
                    </div>
                @elseif (! empty($nextStatuses))
                    <div class="flex flex-wrap gap-3">
                        @foreach ($nextStatuses as $next)
                            <form method="POST" action="{{ route('worker.orders.advance', $order) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ $next }}">
                                <button class="px-4 py-2 bg-aun-navy text-white rounded-md hover:bg-aun-navy-light">
                                    {{ $statusLabels[$next] ?? ucfirst($next) }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No further status updates available for this order.</p>
                @endif
            </div>

            <!-- Rate the student (after completion) -->
            @if ($order->isCompleted())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Rate this student</h3>
                    @if ($workerRating)
                        <div class="flex items-center gap-2"><x-stars :value="$workerRating->stars" /><span class="text-sm text-gray-500">You rated this student.</span></div>
                        @if ($workerRating->comment)<p class="mt-2 text-sm text-gray-700">{{ $workerRating->comment }}</p>@endif
                    @else
                        <form method="POST" action="{{ route('worker.orders.rate', $order) }}" x-data="{ stars: 0 }">
                            @csrf
                            <div class="flex items-center gap-1 mb-3">
                                <template x-for="n in 5" :key="n">
                                    <button type="button" @click="stars = n" class="text-2xl" :class="n <= stars ? 'text-amber-400' : 'text-gray-300'">★</button>
                                </template>
                                <input type="hidden" name="stars" :value="stars">
                            </div>
                            <textarea name="comment" rows="2" placeholder="Comment (optional)" class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                            <button type="submit" :disabled="stars === 0" class="mt-3 px-4 py-2 bg-aun-navy text-white text-sm rounded-md hover:bg-aun-navy-light disabled:opacity-50">Submit rating</button>
                        </form>
                    @endif
                </div>
            @endif

            <!-- History -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-3">History</h3>
                <ul class="space-y-2">
                    @foreach ($order->statusHistory as $entry)
                        <li class="flex items-start gap-3 text-sm">
                            <span class="text-gray-400 whitespace-nowrap">{{ $entry->created_at->format('d M, g:i A') }}</span>
                            <div><x-order-status-badge :status="$entry->status" />@if ($entry->note)<span class="text-gray-600"> — {{ $entry->note }}</span>@endif</div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <a href="{{ route('worker.orders.index') }}" class="text-sm text-aun-navy hover:underline">← Back to orders</a>
        </div>
    </div>
</x-app-layout>
