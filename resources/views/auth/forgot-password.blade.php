<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just enter your email address and we will reset your password.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Show New Password if Reset Was Successful -->
    @if(session('new_password'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-semibold text-green-800">Password Reset Successful!</span>
        </div>
        <p class="text-sm text-green-700 mb-3">A password reset email has been sent to <strong>{{ session('reset_email') }}</strong></p>
        <div class="bg-white p-3 rounded border border-green-300">
            <p class="text-sm text-gray-600 mb-1">Your new password is:</p>
            <div class="flex items-center gap-2">
                <code id="new-password" class="text-lg font-mono font-bold text-green-700 bg-green-100 px-3 py-1 rounded">{{ session('new_password') }}</code>
                <button type="button" onclick="copyPassword()" class="p-2 text-green-600 hover:bg-green-100 rounded transition" title="Copy password">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-2">Please save this password and change it after logging in.</p>
        </div>
        <div class="mt-4">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                Go to Login
            </a>
        </div>
    </div>
    <script>
        function copyPassword() {
            const password = document.getElementById('new-password').textContent;
            navigator.clipboard.writeText(password).then(() => {
                alert('Password copied to clipboard!');
            });
        }
    </script>
    @else
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
    @endif
</x-guest-layout>
