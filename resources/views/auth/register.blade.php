<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- I am a... (role) -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('I am a')" />
            <select id="role" name="role" required
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-aun-navy dark:focus:border-indigo-600 focus:ring-aun-orange dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="student" @selected(old('role') === 'student')>{{ __('Student — I need laundry done') }}</option>
                <option value="worker" @selected(old('role') === 'worker')>{{ __('Laundry worker — I provide the service') }}</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500">Worker accounts require admin approval before receiving orders.</p>
        </div>

        <!-- Residence hall -->
        <div class="mt-4">
            <x-input-label for="dorm_id" :value="__('Residence hall')" />
            <select id="dorm_id" name="dorm_id" required
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-aun-navy dark:focus:border-indigo-600 focus:ring-aun-orange dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="" disabled @selected(! old('dorm_id'))>{{ __('Select your dorm') }}</option>
                @foreach ($dorms as $dorm)
                    <option value="{{ $dorm->id }}" @selected((int) old('dorm_id') === $dorm->id)>{{ $dorm->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('dorm_id')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone number (optional)')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-aun-orange" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
