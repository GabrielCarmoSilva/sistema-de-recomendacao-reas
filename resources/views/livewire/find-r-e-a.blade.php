<div class="flex flex-col gap-y-4 mt-16 w-full justify-center items-center">
    @if($this->userType === 'usuario')
        <div class="flex flex-col items-center w-full px-8">
            <label for="profile" class="block mb-2 text-sm font-medium text-gray-900">@lang('Perfil')</label>
            <input 
                type="text" 
                id="profile" 
                wire:model="profile"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Ensino superior"
            />
            <div class="text-red-500">@error('profile') {{ $message }} @enderror</div>
        </div>
        <div class="flex flex-col items-center w-full px-8">
            <label for="interest" class="block mb-2 text-sm font-medium text-gray-900">@lang('Interesse')</label>
            <input 
                type="text" 
                id="interest" 
                wire:model="interest"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Pensamento computacional" 
            />
            <div class="text-red-500">@error('interest') {{ $message }} @enderror</div>
        </div>
        <div class="flex gap-x-4">
            <button 
                wire:click="selectUserType"
                type="button" 
                class="text-white max-w-36 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
                @lang('Voltar')
            </button>
            <button 
                wire:click='search'
                type="button" 
                class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                @lang('Buscar')
            </button>
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
                                        <th scope="col" class="px-6 py-3">@lang('Tipo de Interatividade')</th>
                                        <th scope="col" class="px-6 py-3">@lang('Nível de Interatividade')</th>
                                        <th scope="col" class="px-6 py-3">@lang('Estilo de Aprendizagem')</th>
                                        <th scope="col" class="px-6 py-3">@lang('Estratégia')</th>
                                        <th scope="col" class="px-6 py-3">@lang('Meta')
                                        <th scope="col" class="px-6 py-3">@lang('Link')</th>
                                    </tr>
                                </thead>
                                @php
                                    $reaIndex = 0;
                                @endphp
                                <tbody>
                                    @if ($data->data)
                                        @foreach ($this->paginate($data) as $rea)
                                            @php
                                                $reaIndex++;
                                            @endphp
                                            <tr @class([
                                                "bg-white border-b",
                                                "bg-yellow-50" => auth()->user() ? $rea->recommended === 'meta' : $rea->recommended === 'both'
                                            ])>
                                                <td class="px-6 py-4">
                                                    {{ $rea->title }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $rea->type }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $rea->repositorio }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $rea->interatividade }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $rea->nivel_interatividade }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $rea->estilo_aprendizagem }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $rea->estrategia }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if ($rea->recommended === 'meta')
                                                        Indicado para a sua meta de aprendizagem
                                                    @endif
                                                </td>
                                                @if (isset($rea->link) || isset($rea->id))
                                                    <td class="px-6 py-4">
                                                        @if (isset($rea->link))
                                                            <a href="{{ $rea->link }}">
                                                                <span class="text-blue-500 underline">{{ $rea->link }}</span>
                                                            </a>
                                                        @endif
                                                        @if (isset($rea->id) && $rea->repositorio === 'MECRED')
                                                            <a href="https://plataformaintegrada.mec.gov.br/recurso/{{ $rea->id }}">
                                                                <span class="text-blue-500 underline">
                                                                    https://plataformaintegrada.mec.gov.br/recurso/{{ $rea->id }}
                                                                </span>
                                                            </a>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @if ($this->paginate($data))
                                <div class="mt-4 flex justify-between">
                                    @if ($this->paginate($data)->currentPage() !== 1)
                                        <span wire:click='prevPage' class="font-medium text-blue-600 dark:text-blue-500 hover:underline cursor-pointer">Página Anterior</span>
                                    @endif
                                    @if ($this->paginate($data)->currentPage() < $this->paginate($data)->lastPage())
                                        <span wire:click='nextPage' class="font-medium text-blue-600 dark:text-blue-500 hover:underline cursor-pointer">Próxima Página</span>
                                    @endif
                                </div>
                            @endif
                            @if (!$data->finished)
                                <div class="flex flex-col items-center justify-center mx-auto mt-4">
                                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                    </svg>
                                    <span>Carregando...</span>
                                </div>
                            @elseif (json_decode($data->data) === [])
                                <span class="text-gray-900">@lang('Nenhum resultado encontrado.')</span>
                            @else
                                <div class="flex flex-col gap-y-4 mt-4">
                                    <span class="font-semibold text-gray-700">
                                        Número de REA retornados na busca: {{ $this->paginate($data)->total() }}
                                    </span>
                                    <span class="font-semibold text-gray-700">
                                        Eficiência: {{ $data->time === 0 ? 0 : round($this->paginate($data)->total() / ($data->time), 2) }}
                                    </span>
                                    <span class="text-gray-700">
                                        O tempo de pesquisa foi de {{ $data->time === 0 ? 0 : round($data->time, 2) }} segundos.
                                    </span>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        @endif
    @elseif($this->userType === 'colaborador')
        <span class="text-gray-700 text-center max-w-96">@lang('A sua inserção melhorará a base de referência para a busca, que não retornará os REAs inseridos aqui, mas os usará como referência para a busca nos repositórios.')</span>
        <div class="flex flex-col items-center w-full px-8">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">@lang('Nome completo')</label>
            <input 
                type="text" 
                id="name" 
                wire:model="name"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Nome completo"
            />
            <div class="text-red-500">@error('name') {{ $message }} @enderror</div>
        </div>
        <div class="flex flex-col items-center w-full px-8">
            <label for="role" class="block mb-2 text-sm font-medium text-gray-900">@lang('Função')</label>
            <input 
                type="text" 
                id="role" 
                wire:model="role"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Professor" 
            />
            <div class="text-red-500">@error('role') {{ $message }} @enderror</div>
        </div>
        <div class="flex flex-col items-center w-full px-8">
            <label for="institution" class="block mb-2 text-sm font-medium text-gray-900">@lang('Instituição')</label>
            <input 
                type="text" 
                id="institution" 
                wire:model="institution"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Instituição" 
            />
            <div class="text-red-500">@error('institution') {{ $message }} @enderror</div>
        </div>
        <div class="flex flex-col items-center w-full px-8">
            <label for="reaTitle" class="block mb-2 text-sm font-medium text-gray-900">@lang('Título do REA')</label>
            <input 
                type="text" 
                id="reaTitle" 
                wire:model="reaTitle"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Introdução ao Pensamento computacional" 
            />
            <div class="text-red-500">@error('reaTitle') {{ $message }} @enderror</div>
        </div>
        <div class="flex flex-col items-center w-full px-8">
            <label for="reaTitle" class="block mb-2 text-sm font-medium text-gray-900">@lang('Referência')</label>
            <input 
                type="text" 
                id="reference" 
                wire:model="reference"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Autor" 
            />
            <div class="text-red-500">@error('reference') {{ $message }} @enderror</div>
        </div>
        <div class="flex flex-col items-center w-full px-8">
            <label for="profile" class="block mb-2 text-sm font-medium text-gray-900">@lang('Perfil')</label>
            <input 
                type="text" 
                id="profile" 
                wire:model="profile"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Ensino superior"
            />
            <div class="text-red-500">@error('profile') {{ $message }} @enderror</div>
        </div>
        <div class="flex flex-col items-center w-full px-8">
            <label for="interest" class="block mb-2 text-sm font-medium text-gray-900">@lang('Interesse')</label>
            <input 
                type="text" 
                id="interest" 
                wire:model="interest"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Pensamento computacional" 
            />
            <div class="text-red-500">@error('interest') {{ $message }} @enderror</div>
        </div>
        <div class="flex flex-col items-center w-full px-8">
            <label for="item" class="block mb-2 text-sm font-medium text-gray-900">@lang('Item')</label>
            <input 
                type="text" 
                id="item" 
                wire:model="item"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-96 p-2" 
                placeholder="Trilha de aprendizagem" 
            />
            <div class="text-red-500">@error('item') {{ $message }} @enderror</div>
        </div>
        <div class="flex gap-x-2">
            <button 
                wire:click="selectUserType"
                type="button" 
                class="text-white max-w-36 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
                @lang('Voltar')
            </button>
            <button 
                wire:click='insert'
                type="button" 
                class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
                @lang('Inserir')
            </button>
        </div>
        @if($this->showMessage)
            <span class="block mb-2 text-md font-medium text-gray-900">@lang('Registro inserido com sucesso!')</label>
        @endif
    @elseif($this->userType === 'feedback')
        <div class="flex flex-col items-center w-full px-8">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">@lang('Deixe seu feedback')</label>
            <textarea 
                id="message" 
                maxlength="4096"
                wire:model.live="message"
                rows="8"
                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-2/3 p-2" 
            ></textarea>
            <div class="text-red-500">@error('message') {{ $message }} @enderror</div>
            <div class="mt-2 flex justify-end text-sm text-gray-600">
                <span>{{ $charCount }}</span> @lang('/4096')
            </div>
        </div>
        <div class="flex gap-x-2">
            <button 
                wire:click="selectUserType"
                type="button" 
                class="text-white max-w-36 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
                @lang('Voltar')
            </button>
            <button 
                wire:click='sendFeedback'
                type="button" 
                class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"">
                @lang('Enviar')
            </button>
        </div>
        @if($this->showMessage)
            <span class="block mb-2 text-md font-medium text-gray-900">@lang('Registro inserido com sucesso!')</label>
        @endif
    @else
        <div class="w-full px-8">
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
            <button 
                wire:click="selectUserType('feedback')"
                type="button" 
                class="text-white mt-4 bg-black hover:bg-zinc-700 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                @lang('Deixe seu feedback')
            </button>
        </div>
        @if(!auth()->check())
            <div class="flex items-center flex-col justify-center w-full px-8">
                <a href="{{ route('login') }}">
                    <button 
                        type="button" 
                        class="text-white max-w-36 bg-emerald-600 hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                        @lang('Login')
                    </button>
                </a>
                <a href="{{ route('register') }}">
                    <button
                        type="button"
                        class="text-white max-w-36 bg-emerald-600 hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                        @lang('Registrar')
                    </button>
                </a>
            </div>
        @else
            <div class="flex items-center flex-col justify-center w-full px-8">
                <span class="font-semibold text-lg">
                    {{ auth()->user()->name }}
                </span>
                @if(!auth()->user()->questionnaire)
                    <a href="{{ route('emapre') }}">
                        <button 
                            type="button" 
                            class="text-white max-w-36 bg-emerald-600 hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                            @lang('Responda o questionário')
                        </button>
                    </a>
                @else
                    @if(auth()->user()->questionnaire->dominant === 'mpe')
                        <span class="font-semibold text-lg">
                            Meta Performance-Evitação
                        </span>
                    @elseif(auth()->user()->questionnaire->dominant === 'ma')
                        <span class="font-semibold text-lg">
                            Meta Aprender
                        </span>
                    @elseif(auth()->user()->questionnaire->dominant === 'mpa')
                        <span class="font-semibold text-lg">
                            Meta Performance-Aproximação
                        </span>
                    @else
                        <span class="font-semibold text-lg">
                            Nenhuma meta determinada
                        </span> 
                    @endif
                @endif
            </div>
        @endif
    @endif
</div>
