<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order {{ $order->reference }}</h2>
            <x-order-status-badge :status="$order->status" />
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Student:</span> <span class="font-medium">{{ $order->student?->name }}</span> ({{ $order->student?->phone ?? 'no phone' }})</div>
                    <div><span class="text-gray-500">Worker:</span> <span class="font-medium">{{ $order->worker?->name ?? 'Unassigned' }}</span></div>
                    <div><span class="text-gray-500">Dorm:</span> <span class="font-medium">{{ $order->dorm?->name ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Placed:</span> <span class="font-medium">{{ $order->created_at->format('d M Y, g:i A') }}</span></div>
                </div>

                @if ($order->notes)
                    <div class="mt-4 text-sm"><span class="text-gray-500">Instructions:</span> {{ $order->notes }}</div>
                @endif

                <table class="mt-4 w-full text-sm">
                    <thead><tr class="text-left text-gray-500 border-b"><th class="py-2">Item</th><th class="py-2 text-center">Qty</th><th class="py-2 text-right">Total</th></tr></thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr class="border-b border-gray-50"><td class="py-2">{{ $item->item_name }}</td><td class="py-2 text-center">{{ $item->quantity }}</td><td class="py-2 text-right">₦{{ number_format((float) $item->line_total) }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr><td colspan="2" class="py-2 text-right font-semibold">Total</td><td class="py-2 text-right font-bold text-aun-navy">₦{{ number_format((float) $order->total_price) }}</td></tr></tfoot>
                </table>
            </div>

            @if ($order->ratings->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Ratings</h3>
                    @foreach ($order->ratings as $rating)
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs text-gray-500 w-40">{{ $rating->direction === 'student_to_worker' ? 'Student → Worker' : 'Worker → Student' }}</span>
                            <x-stars :value="$rating->stars" />
                            @if ($rating->comment)<span class="text-sm text-gray-600">— {{ $rating->comment }}</span>@endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($order->complaints->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Complaints on this order</h3>
                    @foreach ($order->complaints as $c)
                        <div class="text-sm border-b border-gray-50 py-2">
                            <span class="font-medium">{{ $c->subject }}</span> — {{ $c->description }}
                            <a href="{{ route('admin.complaints.index') }}" class="text-aun-navy hover:underline">manage</a>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-3">History</h3>
                <ul class="space-y-2">
                    @foreach ($order->statusHistory as $entry)
                        <li class="flex items-start gap-3 text-sm">
                            <span class="text-gray-400 whitespace-nowrap">{{ $entry->created_at->format('d M, g:i A') }}</span>
                            <div><x-order-status-badge :status="$entry->status" />
                                @if ($entry->changedBy)<span class="text-gray-500"> by {{ $entry->changedBy->name }}</span>@endif
                                @if ($entry->note)<span class="text-gray-600"> — {{ $entry->note }}</span>@endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="text-sm text-aun-navy hover:underline">← Back to orders</a>
        </div>
    </div>
</x-app-layout>
