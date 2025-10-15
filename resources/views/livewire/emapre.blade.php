
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-center mb-6">Questionário EMAPRE-U</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="submit">
        @foreach ($questions as $group => $items)
            <h2 class="text-xl font-semibold mt-8 mb-4">{{ $group }}</h2>

            @foreach ($items as $key => $text)
                <div class="mb-4">
                    <p class="mb-2 font-medium">{{ $text }}</p>
                    <div class="flex space-x-4">
                        <label>
                            <input type="radio" wire:model="responses.{{ $key }}" value="1">
                            Discordo
                        </label>
                        <label>
                            <input type="radio" wire:model="responses.{{ $key }}" value="2">
                            Não sei
                        </label>
                        <label>
                            <input type="radio" wire:model="responses.{{ $key }}" value="3">
                            Concordo
                        </label>
                    </div>
                    @error('responses.' . $key)
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach
        @endforeach

        <div class="text-center mt-8">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                Enviar Respostas
            </button>
        </div>
    </form>
</div>