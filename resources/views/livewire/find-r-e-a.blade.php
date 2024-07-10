<div class="flex flex-col gap-y-4 mt-16 w-full justify-center items-center pb-96">
    @if($this->userType === 'usuario')
        <button 
            wire:click="selectUserType"
            type="button" 
            class="text-white max-w-36 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
            @lang('Voltar')
        </button>
        <div class="w-full px-8 lg:px-64">
            <label for="profile" class="block mb-2 text-sm font-medium text-gray-900">@lang('Perfil')</label>
            <input 
                type="text" 
                id="profile" 
                wire:model="profile"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Ensino superior"
            />
            <div class="text-red-500">@error('profile') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="interest" class="block mb-2 text-sm font-medium text-gray-900">@lang('Interesse')</label>
            <input 
                type="text" 
                id="interest" 
                wire:model="interest"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Pensamento computacional" 
            />
            <div class="text-red-500">@error('interest') {{ $message }} @enderror</div>
        </div>
        <button 
            wire:click='search'
            type="button" 
            class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
            @lang('Buscar')
        </button>
        <div class="flex flex-col gap-y-4 items-center w-full px-8">
            @if ($header && $this->sheet)
                <span class="font-semibold text-lg text-gray-900">@lang('Tabela de REAs encontrados na nossa planilha')</span>
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            @foreach ($header as $row)
                                <th scope="col" class="px-6 py-3">{{ $row }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->sheet as $column)
                            <tr class="bg-white border-b">
                                @foreach ($column as $row)
                                    @if ($loop->first)
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $row }}
                                        </th>
                                    @else
                                        <td class="px-6 py-4">
                                            {{ $row }}
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif ($this->timestampSession)
                <span class="text-gray-900">@lang('Nenhum resultado encontrado.')</span>
            @endif
        </div>
        @if ($this->interestApiSearch)
            <div class="flex flex-col gap-y-4 items-center justify-center w-full px-8 mt-8">
                <div wire:poll.keep-alive>
                    @if (\App\Models\Data::count() > 0)
                        @php
                            $data = \App\Models\Data::query()->where('searched_at', $this->timestampSession)->first();
                        @endphp
                        @if ($data)
                            <span class="font-semibold text-lg text-gray-900">@lang('Tabela de REAs encontrados nos repositórios: ')</span>
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">@lang('Título')</th>
                                        <th scope="col" class="px-6 py-3">@lang('Item')</th>
                                        <th scope="col" class="px-6 py-3">@lang('Repositório')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data->data)
                                        @foreach (json_decode($data->data) as $rea)
                                            <tr class="bg-white border-b">
                                                <td class="px-6 py-4">
                                                    {{ $rea->title }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $rea->type }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $rea->repositorio }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @if (!$data->finished)
                                <div class="flex items-center justify-center mx-auto mt-4">
                                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                    </svg>
                                    <span class="sr-only">Loading...</span>
                                </div>
                            @elseif (json_decode($data->data) === [])
                                <span class="text-gray-900">@lang('Nenhum resultado encontrado.')</span>
                            @endif
                        @endif
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
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">@lang('Nome completo')</label>
            <input 
                type="text" 
                id="name" 
                wire:model="name"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Nome completo"
            />
            <div class="text-red-500">@error('name') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="role" class="block mb-2 text-sm font-medium text-gray-900">@lang('Função')</label>
            <input 
                type="text" 
                id="role" 
                wire:model="role"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Professor" 
            />
            <div class="text-red-500">@error('role') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="institution" class="block mb-2 text-sm font-medium text-gray-900">@lang('Instituição')</label>
            <input 
                type="text" 
                id="institution" 
                wire:model="institution"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Instituição" 
            />
            <div class="text-red-500">@error('institution') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="reaTitle" class="block mb-2 text-sm font-medium text-gray-900">@lang('Título do REA')</label>
            <input 
                type="text" 
                id="reaTitle" 
                wire:model="reaTitle"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Introdução ao Pensamento computacional" 
            />
            <div class="text-red-500">@error('reaTitle') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="reaTitle" class="block mb-2 text-sm font-medium text-gray-900">@lang('Referência')</label>
            <input 
                type="text" 
                id="reference" 
                wire:model="reference"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Autor" 
            />
            <div class="text-red-500">@error('reference') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="profile" class="block mb-2 text-sm font-medium text-gray-900">@lang('Perfil')</label>
            <input 
                type="text" 
                id="profile" 
                wire:model="profile"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Ensino superior"
            />
            <div class="text-red-500">@error('profile') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="interest" class="block mb-2 text-sm font-medium text-gray-900">@lang('Interesse')</label>
            <input 
                type="text" 
                id="interest" 
                wire:model="interest"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Pensamento computacional" 
            />
            <div class="text-red-500">@error('interest') {{ $message }} @enderror</div>
        </div>
        <div class="w-full px-8 lg:px-64">
            <label for="item" class="block mb-2 text-sm font-medium text-gray-900">@lang('Item')</label>
            <input 
                type="text" 
                id="item" 
                wire:model="item"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" 
                placeholder="Trilha de aprendizagem" 
            />
            <div class="text-red-500">@error('item') {{ $message }} @enderror</div>
        </div>
        <button 
            wire:click='insert'
            type="button" 
            class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
            @lang('Inserir')
        </button>
        @if($this->showMessage)
            <span class="block mb-2 text-md font-medium text-gray-900">@lang('Registro inserido com sucesso!')</label>
        @endif
    @else
        <div class="w-full px-8 lg:px-64">
            <span class="block mb-2 text-xl font-medium text-gray-900 text-center">@lang('Você é usuário ou colaborador?')</label>
            <div class="flex gap-x-4 mt-4 justify-center">
                <button 
                    wire:click="selectUserType('colaborador')"
                    type="button" 
                    class="text-white max-w-36 bg-emerald-600 hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                    @lang('Colaborador')
                </button>
                <button 
                    wire:click="selectUserType('usuario')"
                    type="button" 
                    class="text-white max-w-36 bg-emerald-600 hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                    @lang('Usuário')
                </button>
            </div>
        </div>
    @endif
</div>
