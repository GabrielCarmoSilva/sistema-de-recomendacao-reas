<div class="flex flex-col gap-y-4 mt-16 w-full justify-center items-center">
    @if($this->userType === 'usuario')
        <button 
            wire:click="selectUserType"
            type="button" 
            class="text-white max-w-36 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
            @lang('Voltar')
        </button>
        <div class="w-full px-8 lg:px-64">
            <label for="profile" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Perfil')</label>
            <input 
                type="text" 
                id="profile" 
                wire:model="profile"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Ensino superior"
            />
            <div class="text-white">@error('profile') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="interest" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Interesse')</label>
            <input 
                type="text" 
                id="interest" 
                wire:model="interest"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Pensamento computacional" 
            />
            <div class="text-white">@error('interest') {{ $message }} @enderror</div>
        </div>
        <button 
            wire:click='search'
            type="button" 
            class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
            @lang('Buscar')
        </button>
        <div class="flex flex-col gap-y-4 w-full px-8 lg:px-64">
            <div class="flex mx-auto w-full justify-center" wire:loading> 
                <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                </svg>
            </div>
            <div wire:loading.remove class="flex gap-x-2">
                @foreach($header as $row)
                    <span class="text-gray-400 w-32 font-semibold">
                        {{ $row }}
                    </span>
                @endforeach
            </div>
            @forelse($this->sheet as $column)
                <div wire:loading.remove class="flex gap-x-2">
                    @foreach($column as $row)
                        <span wire:loading.remove class="text-gray-400 w-32 font-semibold">
                            {{ $row }}
                        </span>
                    @endforeach
                </div>
            @empty
                @if (!empty($this->header))
                    <span wire:loading.remove class="text-gray-400 font-semibold">
                        @lang('Nenhum resultado encontrado.')
                    </span>
                @endif
            @endforelse
        </div>
        @if ($this->interestApiSearch)
            <div wire:loading.remove class="flex flex-col gap-y-4 w-full px-8 lg:px-64">
                <h1 class="text-gray-400 font-bold">
                    @lang('REAs:')
                </h1>
                @forelse($this->reas as $rea)
                    <div class="flex flex-row gap-x-4">
                        <span class="text-gray-400 font-semibold">
                            {{ $rea['title'] }}
                        </span>
                        <span class="text-gray-400 font-semibold">
                            {{ $rea['type'] }}
                        </span>
                        <span class="text-gray-400 font-semibold">
                            {{ $rea['repositorio'] }}
                        </span>
                    </div>
                @empty
                    <span class="text-gray-400 font-semibold">
                        @lang('Nenhum REA encontrado.')
                    </span>
                @endforelse
            </div>
        @endif
    @elseif($this->userType === 'colaborador')
        <button 
            wire:click="selectUserType"
            type="button" 
            class="text-white max-w-36 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
            @lang('Voltar')
        </button>
        <div class="w-full px-8 lg:px-64">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Nome completo')</label>
            <input 
                type="text" 
                id="name" 
                wire:model="name"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Nome completo"
            />
            <div class="text-white">@error('name') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Função')</label>
            <input 
                type="text" 
                id="role" 
                wire:model="role"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Professor" 
            />
            <div class="text-white">@error('role') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="institution" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Instituição')</label>
            <select 
                id="institution" 
                wire:model="institution"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
            >
                <option value="">@lang('Escolha uma opção...')</option>
                @foreach($this->institutions as $index => $institution)
                    @if ($index > 0)
                        <option value="{{ $institution[0] }}">{{ $institution[0] }}</option>
                    @endif
                @endforeach
            </select>
            <div class="text-white">@error('institution') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="reaTitle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Título do REA')</label>
            <input 
                type="text" 
                id="reaTitle" 
                wire:model="reaTitle"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Introdução ao Pensamento computacional" 
            />
            <div class="text-white">@error('reaTitle') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="reaTitle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Referência')</label>
            <input 
                type="text" 
                id="reference" 
                wire:model="reference"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Autor" 
            />
            <div class="text-white">@error('reference') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="profile" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Perfil')</label>
            <input 
                type="text" 
                id="profile" 
                wire:model="profile"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Ensino superior"
            />
            <div class="text-white">@error('profile') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="interest" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Interesse')</label>
            <input 
                type="text" 
                id="interest" 
                wire:model="interest"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Pensamento computacional" 
            />
            <div class="text-white">@error('interest') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="item" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">@lang('Item')</label>
            <input 
                type="text" 
                id="item" 
                wire:model="item"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Trilha de aprendizagem" 
            />
            <div class="text-white">@error('item') {{ $message }} @enderror</div>
        </div>
        <button 
            wire:click='insert'
            type="button" 
            class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
            @lang('Inserir')
        </button>
        @if($this->showMessage)
            <span class="block mb-2 text-md font-medium text-gray-900 dark:text-white">@lang('Registro inserido com sucesso!')</label>
        @endif
    @else
        <div class="w-full px-8 lg:px-64">
            <span class="block mb-2 text-md font-medium text-gray-900 dark:text-white">@lang('Você é usuário ou colaborador?')</label>
            <div class="flex gap-x-4 mt-4 justify-center">
                <button 
                    wire:click="selectUserType('colaborador')"
                    type="button" 
                    class="text-white max-w-36 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
                    @lang('Colaborador')
                </button>
                <button 
                    wire:click="selectUserType('usuario')"
                    type="button" 
                    class="text-white max-w-36 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
                    @lang('Usuário')
                </button>
            </div>
        </div>
    @endif
</div>
