<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Admin Accounts') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif
            <x-input-error :messages="$errors->all()" />

            <p class="text-sm text-gray-600">
                As Head of Residence Life you can open admin accounts and decide which responsibilities each admin has.
                An admin can only open the areas you tick for them.
            </p>

            <!-- Create admin -->
            <div class="bg-white shadow-sm rounded-lg p-6" x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }">
                <button @click="open = !open" class="px-4 py-2 bg-aun-navy text-white text-sm rounded-md hover:bg-aun-navy-light">+ New admin account</button>
                <form x-show="open" x-cloak method="POST" action="{{ route('admin.admins.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Full name" required class="border-gray-300 rounded-md text-sm">
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required class="border-gray-300 rounded-md text-sm">
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone (optional)" class="border-gray-300 rounded-md text-sm">
                        <div></div>
                        <input type="password" name="password" placeholder="Temporary password" required autocomplete="new-password" class="border-gray-300 rounded-md text-sm">
                        <input type="password" name="password_confirmation" placeholder="Confirm password" required autocomplete="new-password" class="border-gray-300 rounded-md text-sm">
                    </div>
                    <fieldset>
                        <legend class="text-sm font-medium text-gray-700 mb-2">Responsibilities</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($responsibilities as $key => $label)
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, old('permissions', []), true)) class="rounded border-gray-300 text-aun-navy">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    <p class="text-xs text-gray-500">Give the new admin this temporary password. They can change it from their Profile page after signing in.</p>
                    <button class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Create admin</button>
                </form>
            </div>

            <!-- Admin list -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @foreach ($admins as $admin)
                    @php $held = $admin->adminPermissions->pluck('permission')->all(); @endphp
                    <div class="px-6 py-4 border-b border-gray-50" x-data="{ edit: false }">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 sm:gap-4">
                            <div>
                                <div class="font-medium {{ $admin->is_active ? 'text-gray-900' : 'line-through text-gray-400' }}">
                                    {{ $admin->name }}
                                    <span class="ms-1 text-xs rounded-full px-2 py-0.5 {{ $admin->isHeadAdmin() ? 'bg-aun-orange/10 text-aun-orange' : 'bg-gray-100 text-gray-600' }}">{{ $admin->adminTitle() }}</span>
                                    @unless ($admin->is_active)<span class="ms-1 text-xs text-red-600">Deactivated</span>@endunless
                                </div>
                                <div class="text-sm text-gray-500">{{ $admin->email }}</div>
                                <div class="mt-1 text-xs text-gray-600">
                                    @if ($admin->isHeadAdmin())
                                        All responsibilities, including admin accounts
                                    @elseif (empty($held))
                                        <span class="text-amber-700">No responsibilities assigned</span>
                                    @else
                                        {{ collect($held)->map(fn ($p) => $responsibilities[$p] ?? $p)->join(' · ') }}
                                    @endif
                                </div>
                            </div>
                            @unless ($admin->isHeadAdmin() || $admin->is(auth()->user()))
                                <div class="flex flex-wrap items-center gap-2 shrink-0">
                                    <button @click="edit = !edit" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-50">Responsibilities</button>
                                    <form method="POST" action="{{ route('admin.admins.toggle', $admin) }}"
                                          onsubmit="return confirm('{{ $admin->is_active ? 'Deactivate' : 'Reactivate' }} {{ $admin->name }}?')">
                                        @csrf
                                        <button class="px-3 py-1.5 text-sm border rounded-md {{ $admin->is_active ? 'border-red-300 text-red-700 hover:bg-red-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                            {{ $admin->is_active ? 'Deactivate' : 'Reactivate' }}
                                        </button>
                                    </form>
                                </div>
                            @endunless
                        </div>
                        @unless ($admin->isHeadAdmin() || $admin->is(auth()->user()))
                            <form x-show="edit" x-cloak method="POST" action="{{ route('admin.admins.update', $admin) }}" class="mt-3 space-y-3">
                                @csrf @method('PATCH')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach ($responsibilities as $key => $label)
                                        <label class="flex items-center gap-2 text-sm text-gray-700">
                                            <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $held, true)) class="rounded border-gray-300 text-aun-navy">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                <button class="px-4 py-2 bg-aun-navy text-white text-sm rounded-md hover:bg-aun-navy-light">Save responsibilities</button>
                            </form>
                        @endunless
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
