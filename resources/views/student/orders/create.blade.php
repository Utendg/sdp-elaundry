<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('New Laundry Order') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 pb-4 border-b border-gray-100">
                    <div class="text-sm text-gray-500">Booking</div>
                    <div class="font-semibold text-gray-900">{{ $worker->name }} · {{ $worker->dorm?->name }}</div>
                </div>

                <x-input-error :messages="$errors->all()" class="mb-4" />

                <form method="POST" action="{{ route('student.orders.store') }}"
                      x-data="orderForm({{ $serviceItems->map(fn ($i) => ['id' => $i->id, 'price' => (float) $i->unit_price])->toJson() }})">
                    @csrf
                    <input type="hidden" name="worker_id" value="{{ $worker->id }}">

                    <h3 class="font-semibold text-gray-900 mb-2">Select items</h3>
                    <div class="divide-y divide-gray-100 border border-gray-100 rounded-lg">
                        @foreach ($serviceItems as $index => $item)
                            <div class="flex items-center justify-between p-3">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->serviceLabel() }} · ₦{{ number_format((float) $item->unit_price) }} each</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="items[{{ $index }}][service_item_id]" value="{{ $item->id }}">
                                    <button type="button" class="w-8 h-8 rounded border border-gray-300 text-gray-600 hover:bg-gray-50"
                                            @click="dec({{ $item->id }})">−</button>
                                    <input type="number" min="0" max="100"
                                           name="items[{{ $index }}][quantity]"
                                           x-model.number="qty[{{ $item->id }}]"
                                           class="w-16 text-center border-gray-300 rounded-md">
                                    <button type="button" class="w-8 h-8 rounded border border-gray-300 text-gray-600 hover:bg-gray-50"
                                            @click="inc({{ $item->id }})">+</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Live total -->
                    <div class="mt-4 flex items-center justify-between bg-orange-50 rounded-lg px-4 py-3">
                        <span class="font-medium text-aun-navy">Estimated total</span>
                        <span class="text-xl font-bold text-aun-navy">₦<span x-text="total.toLocaleString()">0</span></span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Final price is confirmed from the official university rate when you submit.</p>

                    <!-- Pickup details -->
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="pickup_location" :value="__('Pickup location (optional)')" />
                            <x-text-input id="pickup_location" name="pickup_location" class="block mt-1 w-full"
                                          :value="old('pickup_location')" placeholder="e.g. Room 214" />
                        </div>
                        <div>
                            <x-input-label for="scheduled_pickup_at" :value="__('Preferred pickup time (optional)')" />
                            <x-text-input id="scheduled_pickup_at" name="scheduled_pickup_at" type="datetime-local"
                                          class="block mt-1 w-full" :value="old('scheduled_pickup_at')" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="notes" :value="__('Special instructions (optional)')" />
                        <textarea id="notes" name="notes" rows="3"
                                  class="block mt-1 w-full border-gray-300 focus:border-aun-navy focus:ring-aun-orange rounded-md shadow-sm"
                                  placeholder="e.g. Handle the white shirt with care">{{ old('notes') }}</textarea>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('student.workers.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                        <button type="submit" :disabled="total === 0"
                                class="px-5 py-2 bg-aun-navy text-white rounded-md font-medium hover:bg-aun-navy-light disabled:opacity-50 disabled:cursor-not-allowed">
                            Place order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function orderForm(items) {
            const prices = {};
            items.forEach(i => prices[i.id] = i.price);
            const qty = {};
            items.forEach(i => qty[i.id] = 0);
            return {
                prices,
                qty,
                get total() {
                    return Object.keys(this.qty).reduce((sum, id) => sum + (this.prices[id] * (this.qty[id] || 0)), 0);
                },
                inc(id) { this.qty[id] = (this.qty[id] || 0) + 1; },
                dec(id) { this.qty[id] = Math.max(0, (this.qty[id] || 0) - 1); },
            };
        }
    </script>
</x-app-layout>
