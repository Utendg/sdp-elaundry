<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Agents') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('status') }}</div>
            @endif
            <x-input-error :messages="$errors->all()" />

            <!-- Pending approval -->
            <section>
                <h3 class="font-semibold text-gray-900 mb-3">Awaiting approval
                    <span class="ms-1 text-xs bg-yellow-100 text-yellow-800 rounded-full px-2 py-0.5">{{ $pending->count() }}</span>
                </h3>
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    @forelse ($pending as $worker)
                        <div class="px-6 py-4 border-b border-gray-50" x-data="{ declining: false }">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                                <div class="flex items-center gap-4 min-w-0">
                                    @if ($worker->hasAvatar())
                                        <a href="{{ $worker->avatarUrl() }}" target="_blank" rel="noopener" title="Open full photo">
                                            <x-avatar :user="$worker" size="w-16 h-16" />
                                        </a>
                                    @else
                                        <x-avatar :user="$worker" size="w-16 h-16" />
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-900">{{ $worker->name }}</div>
                                        <div class="text-sm text-gray-500 break-all">{{ $worker->email }} · {{ $worker->dorm?->name ?? 'No dorm' }} · {{ $worker->phone ?? 'No phone' }}</div>
                                        @unless ($worker->hasAvatar())
                                            <div class="mt-1 text-xs text-amber-700">No profile photo yet. A photo is needed before approval.</div>
                                        @endunless
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('admin.workers.approve', $worker) }}">
                                        @csrf
                                        <button class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">Approve</button>
                                    </form>
                                    <button type="button" @click="declining = !declining" class="px-4 py-2 text-sm border border-red-300 text-red-700 rounded-md hover:bg-red-50">Decline</button>
                                </div>
                            </div>
                            <form x-show="declining" x-cloak method="POST" action="{{ route('admin.workers.decline', $worker) }}" class="mt-3 flex flex-col sm:flex-row gap-2">
                                @csrf
                                <input type="text" name="reason" maxlength="500" placeholder="Reason (optional, shown to the applicant)" class="flex-1 border-gray-300 rounded-md text-sm">
                                <button class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">Confirm decline</button>
                            </form>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">No agents awaiting approval.</div>
                    @endforelse
                </div>
            </section>

            <!-- Approved -->
            <section>
                <h3 class="font-semibold text-gray-900 mb-3">Approved agents
                    <span class="ms-1 text-xs bg-green-100 text-green-800 rounded-full px-2 py-0.5">{{ $approved->count() }}</span>
                </h3>
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    @forelse ($approved as $worker)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 sm:px-6 py-4 border-b border-gray-50">
                            <div class="flex items-center gap-3">
                                <x-avatar :user="$worker" />
                                <div>
                                <div class="font-medium text-gray-900">{{ $worker->name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $worker->dorm?->name ?? '—' }} ·
                                    <x-stars :value="$worker->workerProfile->rating_avg" :count="$worker->workerProfile->ratings_count" class="align-middle" /> ·
                                    {{ $worker->workerProfile->orders_completed }} completed
                                </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.workers.revoke', $worker) }}" onsubmit="return confirm('Revoke approval for {{ $worker->name }}?')">
                                @csrf
                                <button class="px-3 py-2 text-sm border border-red-300 text-red-700 rounded-md hover:bg-red-50">Revoke</button>
                            </form>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">No approved agents yet.</div>
                    @endforelse
                </div>
            </section>

            <!-- Declined -->
            @if ($declined->isNotEmpty())
                <section>
                    <h3 class="font-semibold text-gray-900 mb-3">Declined applications
                        <span class="ms-1 text-xs bg-red-100 text-red-800 rounded-full px-2 py-0.5">{{ $declined->count() }}</span>
                    </h3>
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        @foreach ($declined as $worker)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 px-4 sm:px-6 py-4 border-b border-gray-50">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $worker->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $worker->email }} · {{ $worker->dorm?->name ?? 'No dorm' }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Declined {{ $worker->workerProfile->declined_at->format('d M Y') }}@if ($worker->workerProfile->decliner) by {{ $worker->workerProfile->decliner->name }}@endif
                                        @if ($worker->workerProfile->decline_reason). Reason: “{{ $worker->workerProfile->decline_reason }}”@endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.workers.approve', $worker) }}" onsubmit="return confirm('Approve {{ $worker->name }} after all?')">
                                    @csrf
                                    <button class="px-3 py-2 text-sm border border-green-300 text-green-700 rounded-md hover:bg-green-50">Approve instead</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
