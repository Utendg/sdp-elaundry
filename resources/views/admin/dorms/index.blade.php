<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Residence Halls') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif
            <x-input-error :messages="$errors->all()" />

            <!-- Add dorm -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6" x-data="{ open: false }">
                <button @click="open = !open" class="px-4 py-2 bg-aun-navy text-white text-sm rounded-md hover:bg-aun-navy-light">+ Add hall</button>
                <form x-show="open" x-cloak method="POST" action="{{ route('admin.dorms.store') }}" class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf
                    <input type="text" name="name" placeholder="Hall name" required class="border-gray-300 rounded-md text-sm">
                    <input type="text" name="code" placeholder="Code (e.g. NDA)" required class="border-gray-300 rounded-md text-sm">
                    <input type="text" name="description" placeholder="Description (optional)" class="border-gray-300 rounded-md text-sm sm:col-span-2">
                    <div class="sm:col-span-2"><button class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Save hall</button></div>
                </form>
            </div>

            <!-- List -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                @foreach ($dorms as $dorm)
                    <div class="px-6 py-4 border-b border-gray-50" x-data="{ edit: false }">
                        <div class="flex items-center justify-between" x-show="!edit">
                            <div>
                                <div class="font-medium text-gray-900 {{ $dorm->is_active ? '' : 'line-through text-gray-400' }}">{{ $dorm->name }}
                                    <span class="text-xs text-gray-400">({{ $dorm->code }})</span>
                                </div>
                                <div class="text-sm text-gray-500">{{ $dorm->users_count }} user(s)</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="edit = true" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-50">Edit</button>
                                <form method="POST" action="{{ route('admin.dorms.toggle', $dorm) }}">
                                    @csrf
                                    <button class="px-3 py-1.5 text-sm border rounded-md {{ $dorm->is_active ? 'border-gray-300 hover:bg-gray-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                        {{ $dorm->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                        <form x-show="edit" x-cloak method="POST" action="{{ route('admin.dorms.update', $dorm) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @csrf @method('PATCH')
                            <input type="text" name="name" value="{{ $dorm->name }}" required class="border-gray-300 rounded-md text-sm">
                            <input type="text" name="code" value="{{ $dorm->code }}" required class="border-gray-300 rounded-md text-sm">
                            <input type="text" name="description" value="{{ $dorm->description }}" placeholder="Description" class="border-gray-300 rounded-md text-sm sm:col-span-2">
                            <div class="sm:col-span-2 flex gap-2">
                                <button class="px-4 py-2 bg-aun-navy text-white text-sm rounded-md hover:bg-aun-navy-light">Save</button>
                                <button type="button" @click="edit = false" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
