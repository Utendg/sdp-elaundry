<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order {{ $order->reference }}</h2>
            <x-order-status-badge :status="$order->status" />
        </div>
    </x-slot>

    @php
        $pipeline = ['pending' => 'Placed', 'accepted' => 'Accepted', 'picked_up' => 'Picked Up', 'washing' => 'Washing', 'ironing' => 'Ironing', 'ready' => 'Ready', 'completed' => 'Completed'];
        $currentIndex = $order->pipelineIndex();
        $isClosedAbnormally = in_array($order->status, ['cancelled', 'rejected'], true);
        $studentRating = $order->ratings->firstWhere('direction', 'student_to_worker');
        // Students found the stage timeline the hardest part to read in user testing, so it says what happens next.
        $whatNext = [
            'pending' => 'Waiting for your laundry agent to accept the order. You can still cancel it.',
            'accepted' => 'Your agent has accepted the order and will collect your clothes. Their phone number is below.',
            'picked_up' => 'Your clothes have been collected. Washing is next.',
            'washing' => 'Your clothes are being washed. Ironing comes next, unless the order is wash-only.',
            'ironing' => 'Your clothes are being ironed. Your agent will mark them ready when they are done.',
            'ready' => 'Your clothes are ready. Your agent will bring them back, and you pay on return.',
            'completed' => 'This order is complete. You can rate your agent below.',
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif
            <x-input-error :messages="$errors->all()" />

            <!-- Tracking timeline -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                @if ($isClosedAbnormally)
                    <div class="text-center py-4">
                        <x-order-status-badge :status="$order->status" class="text-sm" />
                        @if ($order->cancel_reason)
                            <p class="mt-2 text-sm text-gray-500">Reason: {{ $order->cancel_reason }}</p>
                        @endif
                    </div>
                @else
                    <ol class="flex items-center w-full">
                        @foreach ($pipeline as $key => $label)
                            @php $done = ! is_null($currentIndex) && $loop->index <= $currentIndex; @endphp
                            <li class="flex-1 flex flex-col items-center relative">
                                @unless ($loop->first)
                                    <div class="absolute right-1/2 top-3 w-full h-0.5 {{ $done ? 'bg-aun-navy' : 'bg-gray-200' }}"></div>
                                @endunless
                                <div class="relative z-10 w-6 h-6 rounded-full flex items-center justify-center text-xs
                                            {{ $done ? 'bg-aun-navy text-white' : 'bg-gray-200 text-gray-500' }}">
                                    @if ($done) <x-heroicon-m-check class="w-4 h-4" aria-label="done" /> @else {{ $loop->iteration }} @endif
                                </div>
                                <span class="mt-1 text-[10px] sm:text-xs text-center {{ $done ? 'text-aun-navy font-medium' : 'text-gray-400' }}">{{ $label }}</span>
                            </li>
                        @endforeach
                    </ol>
                    @if (isset($whatNext[$order->status]))
                        <p class="mt-4 text-sm text-gray-700 bg-aun-navy/5 rounded-md px-3 py-2" data-what-next>
                            <span class="font-semibold">What happens next:</span> {{ $whatNext[$order->status] }}
                        </p>
                    @endif
                @endif
            </div>

            <!-- Summary -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-sm">
                    <div class="flex items-center gap-2"><span class="text-gray-500">Agent:</span> @if ($order->worker)<x-avatar :user="$order->worker" size="w-8 h-8" text="text-xs" />@endif <span class="font-medium">{{ $order->worker?->name ?? 'Unassigned' }}</span></div>
                    @if ($order->worker?->phone && ! in_array($order->status, ['pending', 'cancelled', 'rejected'], true))
                        <div><span class="text-gray-500">Agent phone:</span> <a href="tel:{{ $order->worker->phone }}" class="font-medium text-aun-navy hover:underline">{{ $order->worker->phone }}</a></div>
                    @endif
                    <div><span class="text-gray-500">Dorm:</span> <span class="font-medium">{{ $order->dorm?->name ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Placed:</span> <span class="font-medium">{{ $order->created_at->format('d M Y, g:i A') }}</span></div>
                    @if ($order->scheduled_pickup_at)
                        <div><span class="text-gray-500">Pickup:</span> <span class="font-medium">{{ $order->scheduled_pickup_at->format('d M Y, g:i A') }}</span></div>
                    @endif
                    @if ($order->pickup_location)
                        <div><span class="text-gray-500">Location:</span> <span class="font-medium">{{ $order->pickup_location }}</span></div>
                    @endif
                </div>

                @if ($order->notes)
                    <div class="mt-4 text-sm"><span class="text-gray-500">Instructions:</span> {{ $order->notes }}</div>
                @endif

                <!-- Items -->
                <div class="overflow-x-auto -mx-1 px-1">
                <table data-scroll class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">Item</th>
                            <th class="py-2 text-center">Qty</th>
                            <th class="py-2 text-right">Unit</th>
                            <th class="py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr class="border-b border-gray-50">
                                <td class="py-2">{{ $item->item_name }}</td>
                                <td class="py-2 text-center">{{ $item->quantity }}</td>
                                <td class="py-2 text-right">₦{{ number_format((float) $item->unit_price) }}</td>
                                <td class="py-2 text-right">₦{{ number_format((float) $item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="py-2 text-right font-semibold">Total</td>
                            <td class="py-2 text-right font-bold text-aun-navy">₦{{ number_format((float) $order->total_price) }}</td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>

            <!-- Actions -->
            @if (in_array($order->status, ['pending', 'accepted'], true))
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <form method="POST" action="{{ route('student.orders.cancel', $order) }}"
                          onsubmit="return confirm('Cancel this order?')">
                        @csrf
                        <label class="block text-sm text-gray-700 mb-1">Cancel this order</label>
                        <div class="flex gap-2">
                            <input type="text" name="cancel_reason" placeholder="Reason (optional)"
                                   class="flex-1 border-gray-300 rounded-md text-sm">
                            <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Cancel order</button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Rating (after completion) -->
            @if ($order->isCompleted())
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Rate your agent</h3>
                    @if ($studentRating)
                        <div class="flex items-center gap-2">
                            <x-stars :value="$studentRating->stars" />
                            <span class="text-sm text-gray-500">You rated this order.</span>
                        </div>
                        @if ($studentRating->comment)
                            <p class="mt-2 text-sm text-gray-700">{{ $studentRating->comment }}</p>
                        @endif
                    @else
                        <form method="POST" action="{{ route('student.orders.rate', $order) }}" x-data="{ stars: 0 }">
                            @csrf
                            <div class="flex items-center gap-1 mb-3">
                                <template x-for="n in 5" :key="n">
                                    <button type="button" @click="stars = n"
                                            class="p-0.5" :class="n <= stars ? 'text-amber-400' : 'text-gray-300'" :aria-label="n + ' out of 5'"><x-heroicon-s-star class="w-7 h-7" aria-hidden="true" /></button>
                                </template>
                                <input type="hidden" name="stars" :value="stars">
                            </div>
                            <textarea name="comment" rows="2" placeholder="Leave a comment (optional)"
                                      class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                            <button type="submit" :disabled="stars === 0"
                                    class="mt-3 px-4 py-2 bg-aun-navy text-white text-sm rounded-md hover:bg-aun-navy-light disabled:opacity-50">
                                Submit rating
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <!-- File a complaint -->
            <div class="bg-white shadow-sm rounded-lg p-6" x-data="{ open: false }">
                <button @click="open = !open" class="text-sm text-red-600 hover:underline">Report a problem with this order</button>
                <form x-show="open" x-cloak method="POST" action="{{ route('student.complaints.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <select name="type" class="block w-full border-gray-300 rounded-md text-sm">
                        <option value="damaged">Damaged item</option>
                        <option value="missing">Missing item</option>
                        <option value="delayed">Delayed service</option>
                        <option value="pricing">Pricing issue</option>
                        <option value="conduct">Agent conduct</option>
                        <option value="other">Other</option>
                    </select>
                    <input type="text" name="subject" placeholder="Subject" required class="block w-full border-gray-300 rounded-md text-sm">
                    <textarea name="description" rows="3" placeholder="Describe the issue" required class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                    <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Submit complaint</button>
                </form>
            </div>

            <!-- Notes from the worker -->
            @if ($order->workerNotes->isNotEmpty())
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Notes from your agent</h3>
                    @foreach ($order->workerNotes as $note)
                        <div class="border-t border-gray-100 py-2 text-sm first:border-t-0">
                            <div class="text-xs text-gray-400">{{ $note->created_at->format('d M, g:i A') }}</div>
                            <p class="text-gray-700 whitespace-pre-line">{{ $note->body }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Status history -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-3">History</h3>
                <ul class="space-y-2">
                    @foreach ($order->statusHistory as $entry)
                        <li class="flex items-start gap-3 text-sm">
                            <span class="text-gray-400 whitespace-nowrap">{{ $entry->created_at->format('d M, g:i A') }}</span>
                            <div>
                                <x-order-status-badge :status="$entry->status" />
                                @if ($entry->note)<span class="text-gray-600">: {{ $entry->note }}</span>@endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <a href="{{ route('student.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-aun-navy hover:underline"><x-heroicon-m-arrow-left class="w-4 h-4" aria-hidden="true" /> Back to my orders</a>
        </div>
    </div>
</x-app-layout>
