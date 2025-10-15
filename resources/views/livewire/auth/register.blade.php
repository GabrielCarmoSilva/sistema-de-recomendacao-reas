<div>
    <h1 class="text-2xl font-bold mb-6 text-center">Cadastro</h1>

    <form wire:submit.prevent="register" class="space-y-4">
        <div>
            <input type="text" wire:model="name" placeholder="Nome" class="w-full border px-4 py-2 rounded" />
            @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <input type="email" wire:model="email" placeholder="E-mail" class="w-full border px-4 py-2 rounded" />
            @error('email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <input type="password" wire:model="password" placeholder="Senha" class="w-full border px-4 py-2 rounded" />
            @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <input type="password" wire:model="password_confirmation" placeholder="Confirmar senha" class="w-full border px-4 py-2 rounded" />
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
            Cadastrar
        </button>
    </form>

    <p class="text-center text-sm mt-4">
        Já tem conta? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Entrar</a>
    </p>
</div>