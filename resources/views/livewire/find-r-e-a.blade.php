<div class="flex flex-col gap-y-4 mt-16 w-full justify-center items-center pb-96">
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
            <div class="flex gap-x-2">
                @foreach($header as $row)
                    <span class="text-gray-400 w-32 font-semibold">
                        {{ $row }}
                    </span>
                @endforeach
            </div>
            @forelse($this->sheet as $column)
                <div class="flex gap-x-2">
                    @foreach($column as $row)
                        <span class="text-gray-400 w-32 font-semibold">
                            {{ $row }}
                        </span>
                    @endforeach
                </div>
            @empty
                @if (!empty($this->header))
                    <span class="text-gray-400 font-semibold">
                        @lang('Nenhum resultado encontrado.')
                    </span>
                @endif
            @endforelse
        </div>
        @if ($this->interestApiSearch)
            <div class="flex flex-col gap-y-4 w-full px-8 lg:px-64">
                <div wire:poll.keep-alive>
                    <h1 class="text-gray-400 font-bold">
                        @lang('REAs:')
                    </h1>
                    @if (\App\Models\Data::count() > 0)
                        @forelse(json_decode(\App\Models\Data::orderBy('id', 'desc')->first()->data) as $rea)
                            <div class="flex flex-row gap-x-4">
                                <span class="text-gray-400 font-semibold">
                                    {{ $rea->title }}
                                </span>
                                <span class="text-gray-400 font-semibold">
                                    {{ $rea->type }}
                                </span>
                                <span class="text-gray-400 font-semibold">
                                    {{ $rea->repositorio }}
                                </span>
                            </div>
                        @empty
                            <span class="text-gray-400 font-semibold">
                                @lang('Nenhum REA encontrado.')
                            </span>
                        @endforelse
                    @endif
                </div>
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
            <input 
                type="text" 
                id="institution" 
                wire:model="institution"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Instituição" 
            />
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
