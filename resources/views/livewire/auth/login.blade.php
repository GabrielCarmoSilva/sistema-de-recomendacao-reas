<div>
    <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

    <form wire:submit.prevent="login" class="space-y-4">
        <div>
            <input type="email" wire:model="email" placeholder="E-mail" class="w-full border px-4 py-2 rounded" />
            @error('email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <input type="password" wire:model="password" placeholder="Senha" class="w-full border px-4 py-2 rounded" />
            @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Entrar
        </button>
    </form>

    <p class="text-center text-sm mt-4">
        Não tem conta? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Cadastre-se</a>
    </p>
</div>