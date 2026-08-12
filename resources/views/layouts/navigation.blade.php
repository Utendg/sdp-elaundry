<nav x-data="{ open: false }" class="bg-aun-navy border-b border-black/10">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-10 w-auto bg-white rounded-full p-0.5" />
                        <span class="text-white font-semibold hidden lg:block">AUN E-Laundry</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if (Auth::user()->isStudent())
                        <x-nav-link :href="route('student.workers.index')" :active="request()->routeIs('student.workers.*')">
                            {{ __('Find a Worker') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.orders.index')" :active="request()->routeIs('student.orders.*')">
                            {{ __('My Orders') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.complaints.index')" :active="request()->routeIs('student.complaints.*')">
                            {{ __('Complaints') }}
                        </x-nav-link>
                    @elseif (Auth::user()->isWorker())
                        <x-nav-link :href="route('worker.orders.index')" :active="request()->routeIs('worker.orders.*')">
                            {{ __('Orders') }}
                        </x-nav-link>
                        <x-nav-link :href="route('worker.profile.edit')" :active="request()->routeIs('worker.profile.*')">
                            {{ __('My Profile') }}
                        </x-nav-link>
                    @elseif (Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.workers.index')" :active="request()->routeIs('admin.workers.*')">
                            {{ __('Workers') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                            {{ __('Orders') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.complaints.index')" :active="request()->routeIs('admin.complaints.*')">
                            {{ __('Complaints') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.service-items.index')" :active="request()->routeIs('admin.service-items.*')">
                            {{ __('Price List') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.dorms.index')" :active="request()->routeIs('admin.dorms.*')">
                            {{ __('Dorms') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Notification bell + Settings -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                @php $unread = Auth::user()->unreadNotifications()->take(8)->get(); $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-white/80 hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if ($unreadCount > 0)
                            <span class="absolute top-1 right-1 inline-flex items-center justify-center h-4 min-w-4 px-1 text-[10px] font-bold text-white bg-red-600 rounded-full">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg border border-gray-100 z-50">
                        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-100">
                            <span class="font-semibold text-sm text-gray-900">Notifications</span>
                            @if ($unreadCount > 0)
                                <form method="POST" action="{{ route('notifications.readAll') }}">
                                    @csrf
                                    <button class="text-xs text-aun-navy hover:underline">Mark all read</button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse ($unread as $note)
                                <a href="{{ route('notifications.read', $note->id) }}" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50">
                                    <div class="text-sm text-gray-800">{{ $note->data['message'] ?? 'Notification' }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $note->created_at->diffForHumans() }}</div>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-sm text-gray-500">You're all caught up.</div>
                            @endforelse
                        </div>
                        <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-center text-xs text-aun-navy hover:underline border-t border-gray-100">View all</a>
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white/80 bg-transparent hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white/80 hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if (Auth::user()->isStudent())
                <x-responsive-nav-link :href="route('student.workers.index')" :active="request()->routeIs('student.workers.*')">
                    {{ __('Find a Worker') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.orders.index')" :active="request()->routeIs('student.orders.*')">
                    {{ __('My Orders') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.complaints.index')" :active="request()->routeIs('student.complaints.*')">
                    {{ __('Complaints') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->isWorker())
                <x-responsive-nav-link :href="route('worker.orders.index')" :active="request()->routeIs('worker.orders.*')">
                    {{ __('Orders') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('worker.profile.edit')" :active="request()->routeIs('worker.profile.*')">
                    {{ __('My Profile') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.workers.index')" :active="request()->routeIs('admin.workers.*')">
                    {{ __('Workers') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                    {{ __('Orders') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.complaints.index')" :active="request()->routeIs('admin.complaints.*')">
                    {{ __('Complaints') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.service-items.index')" :active="request()->routeIs('admin.service-items.*')">
                    {{ __('Price List') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.dorms.index')" :active="request()->routeIs('admin.dorms.*')">
                    {{ __('Dorms') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                    {{ __('Notifications') }}
                    @php $mUnread = Auth::user()->unreadNotifications()->count(); @endphp
                    @if ($mUnread > 0)
                        <span class="ms-1 inline-flex items-center justify-center h-5 min-w-5 px-1.5 text-[10px] font-bold text-white bg-red-600 rounded-full">{{ $mUnread > 9 ? '9+' : $mUnread }}</span>
                    @endif
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
