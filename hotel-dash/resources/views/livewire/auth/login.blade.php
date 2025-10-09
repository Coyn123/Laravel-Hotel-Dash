<div class="p-6 bg-gray-800 rounded shadow-md">
    @if ($mode === 'login')
        <h2 class="mb-4 text-xl font-bold text-white">Login</h2>
        <form wire:submit.prevent="login" class="space-y-4">
            <div>
                <label class="block text-sm text-gray-300">Email</label>
                <input type="email" wire:model.defer="login_email"
                       class="w-full px-3 py-2 rounded text-gray-900" required>
                @error('login_email') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-300">Password</label>
                <input type="password" wire:model.defer="login_password"
                       class="w-full px-3 py-2 rounded text-gray-900" required>
                @error('login_password') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" wire:model="remember" class="mr-2">
                <span class="text-sm text-gray-300">Remember me</span>
            </div>

            <button type="submit"
                    class="w-full py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Log In
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-400">
            Don’t have an account?
            <button wire:click="switchMode('register')" class="underline">Register</button>
        </p>
    @else
        <h2 class="mb-4 text-xl font-bold text-white">Register</h2>
        <form wire:submit.prevent="register" class="space-y-4">
            <div>
                <label class="block text-sm text-gray-300">Name</label>
                <input type="text" wire:model.defer="register_name"
                       class="w-full px-3 py-2 rounded text-gray-900" required>
                @error('register_name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-300">Email</label>
                <input type="email" wire:model.defer="register_email"
                       class="w-full px-3 py-2 rounded text-gray-900" required>
                @error('register_email') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-300">Password</label>
                <input type="password" wire:model.defer="register_password"
                       class="w-full px-3 py-2 rounded text-gray-900" required>
                @error('register_password') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-300">Confirm Password</label>
                <input type="password" wire:model.defer="register_password_confirmation"
                       class="w-full px-3 py-2 rounded text-gray-900" required>
            </div>

            <button type="submit"
                    class="w-full py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Register
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-400">
            Already have an account?
            <button wire:click="switchMode('login')" class="underline">Login</button>
        </p>
    @endif
</div>
