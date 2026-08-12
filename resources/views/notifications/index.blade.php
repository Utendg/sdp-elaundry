<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Notifications') }}</h2>
            @if (Auth::user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button class="text-sm text-aun-navy hover:underline">Mark all read</button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                @forelse ($notifications as $note)
                    <a href="{{ route('notifications.read', $note->id) }}"
                       class="block px-6 py-4 border-b border-gray-50 hover:bg-gray-50 {{ is_null($note->read_at) ? 'bg-orange-50/40' : '' }}">
                        <div class="flex items-start gap-3">
                            @if (is_null($note->read_at))
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-aun-navy shrink-0"></span>
                            @else
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-transparent shrink-0"></span>
                            @endif
                            <div>
                                <div class="text-sm text-gray-800">{{ $note->data['message'] ?? 'Notification' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $note->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-10 text-center text-gray-500">No notifications yet.</div>
                @endforelse
            </div>

            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
