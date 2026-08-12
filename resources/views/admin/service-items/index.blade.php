<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Price List') }}</h2>
    </x-slot>

    @php $services = ['wash' => 'Wash', 'iron' => 'Iron', 'wash_iron' => 'Wash & Iron', 'dry_clean' => 'Dry Clean']; @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif
            <x-input-error :messages="$errors->all()" />

            <!-- Add new item -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6" x-data="{ open: false }">
                <button @click="open = !open" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">+ Add item</button>
                <form x-show="open" x-cloak method="POST" action="{{ route('admin.service-items.store') }}" class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf
                    <input type="text" name="name" placeholder="Item name" required class="border-gray-300 rounded-md text-sm">
                    <select name="service" class="border-gray-300 rounded-md text-sm">
                        @foreach ($services as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                    </select>
                    <input type="number" step="0.01" min="0" name="unit_price" placeholder="Unit price (₦)" required class="border-gray-300 rounded-md text-sm">
                    <input type="number" min="0" name="sort_order" placeholder="Sort order (optional)" class="border-gray-300 rounded-md text-sm">
                    <input type="text" name="description" placeholder="Description (optional)" class="border-gray-300 rounded-md text-sm sm:col-span-2">
                    <div class="sm:col-span-2"><button class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Save item</button></div>
                </form>
            </div>

            <!-- List -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                @forelse ($items as $item)
                    <div class="px-6 py-4 border-b border-gray-50" x-data="{ edit: false }">
                        <div class="flex items-center justify-between" x-show="!edit">
                            <div>
                                <div class="font-medium text-gray-900 {{ $item->is_active ? '' : 'line-through text-gray-400' }}">{{ $item->name }}</div>
                                <div class="text-sm text-gray-500">{{ $services[$item->service] ?? $item->service }} · ₦{{ number_format((float) $item->unit_price) }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="edit = true" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-50">Edit</button>
                                <form method="POST" action="{{ route('admin.service-items.toggle', $item) }}">
                                    @csrf
                                    <button class="px-3 py-1.5 text-sm border rounded-md {{ $item->is_active ? 'border-gray-300 hover:bg-gray-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                        {{ $item->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.service-items.destroy', $item) }}" onsubmit="return confirm('Delete this item?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 text-sm border border-red-300 text-red-700 rounded-md hover:bg-red-50">Delete</button>
                                </form>
                            </div>
                        </div>

                        <!-- Inline edit -->
                        <form x-show="edit" x-cloak method="POST" action="{{ route('admin.service-items.update', $item) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @csrf @method('PATCH')
                            <input type="text" name="name" value="{{ $item->name }}" required class="border-gray-300 rounded-md text-sm">
                            <select name="service" class="border-gray-300 rounded-md text-sm">
                                @foreach ($services as $val => $label)<option value="{{ $val }}" @selected($item->service === $val)>{{ $label }}</option>@endforeach
                            </select>
                            <input type="number" step="0.01" min="0" name="unit_price" value="{{ $item->unit_price }}" required class="border-gray-300 rounded-md text-sm">
                            <input type="number" min="0" name="sort_order" value="{{ $item->sort_order }}" class="border-gray-300 rounded-md text-sm">
                            <div class="sm:col-span-2 flex gap-2">
                                <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Save</button>
                                <button type="button" @click="edit = false" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">No items yet. Add your first priced item above.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
