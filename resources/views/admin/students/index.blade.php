<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Students') }}</h2>
            <span class="text-sm text-gray-500">{{ $total }} {{ \Illuminate\Support\Str::plural('student', $total) }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Filter -->
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 grid grid-cols-1 sm:grid-cols-[1fr_220px_auto] gap-3 items-end">
                <div>
                    <label for="q" class="block text-sm text-gray-600 mb-1">Search by name or email</label>
                    <input id="q" type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="e.g. David" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label for="dorm" class="block text-sm text-gray-600 mb-1">Residence hall</label>
                    <select id="dorm" name="dorm" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All halls</option>
                        @foreach ($dorms as $dorm)
                            <option value="{{ $dorm->id }}" @selected(($filters['dorm'] ?? null) == $dorm->id)>{{ $dorm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-aun-navy text-white text-sm rounded-md hover:bg-aun-navy-light">Filter</button>
                    <a href="{{ route('admin.students.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline">Reset</a>
                </div>
            </form>

            @forelse ($groups as $group)
                <section class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-4 sm:px-6 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-aun-navy">{{ $group['dorm']?->name ?? 'No residence hall' }}</h3>
                        <span class="text-xs bg-white border border-gray-200 rounded-full px-2 py-0.5 text-gray-600">{{ $group['students']->count() }}</span>
                    </div>
                    @foreach ($group['students'] as $student)
                        <div class="px-4 sm:px-6 py-3 border-b border-gray-50 last:border-b-0 flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <x-avatar :user="$student" size="w-9 h-9" text="text-xs" />
                                <div class="min-w-0">
                                <div class="font-medium text-gray-900 {{ $student->is_active ? '' : 'line-through text-gray-400' }}">{{ $student->name }}</div>
                                <div class="text-sm text-gray-500 break-all">{{ $student->email }} · {{ $student->phone ?? 'No phone' }}</div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500 shrink-0">
                                {{ $student->orders_as_student_count }} {{ \Illuminate\Support\Str::plural('order', $student->orders_as_student_count) }}
                                · joined {{ $student->created_at->format('d M Y') }}
                            </div>
                        </div>
                    @endforeach
                </section>
            @empty
                <div class="bg-white shadow-sm rounded-lg px-6 py-10 text-center text-gray-500">No students match this search.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
