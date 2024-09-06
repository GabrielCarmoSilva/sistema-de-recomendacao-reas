<?php

namespace App\Livewire;

use App\Models\Data;
use Livewire\Component;
use App\Models\Feedback;
use App\Models\Searches;
use App\Jobs\ProcessMecRed;
use App\Models\Collaborator;
use Livewire\WithPagination;
use App\Jobs\ProcessAquarela;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Http;
use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Pagination\LengthAwarePaginator;

class FindREA extends Component
{
    use WithPagination;

    #[Validate('required', message: 'O perfil é obrigatório.')]
    public string $profile;

    #[Validate('required', message: 'O interesse é obrigatório.')]
    public string $interest;

    #[Validate('required_if:userType,==,colaborador', message: 'O nome é obrigatório.')]
    public string $name;

    #[Validate('required_if:userType,==,colaborador', message: 'A função é obrigatória.')]
    public string $role;

    #[Validate('required_if:userType,==,colaborador', message: 'A instituição é obrigatória.')]
    public string $institution;

    #[Validate('required_if:userType,==,colaborador', message: 'O título do REA é obrigatório.')]
    public string $reaTitle;

    #[Validate('required_if:userType,==,colaborador', message: 'A referência é obrigatória.')]
    public string $reference;

    #[Validate('required_if:userType,==,colaborador', message: 'O item é obrigatório.')]
    public string $item;

    #[Validate('max:4096', message: 'Por favor adicione uma mensagem de no máximo 4096 caracteres.')]
    public string $message;

    public bool $showMessage = false;

    public bool $loading = false;

    public array $sheet = [];

    public array $reas = [];

    public $timestampSession;

    public ?string $userType = null;

    private array $interestOptions = [
        'algoritmos',
        'decomposição',
        'reconhecimento de padrões',
        'abstração'
    ];

    public string $interestApiSearch = '';

    public $charCount = 0;

    public int $page = 1;

    public function updatedMessage($value)
    {
        $this->charCount = strlen($value);
    }

    public function mount()
    {
        $collaboratorsInterests = collect(Collaborator::lazyById(100, $column = 'id'))
            ->map(function ($collaborator) {
                return [
                    $collaborator->interest
                ];
            });

        $this->interestOptions = array_merge($this->interestOptions, $collaboratorsInterests->all());
    }

    public function selectUserType(?string $type = null)
    {
        $this->userType = $type;

        $this->showMessage = false;
    }

    public function prevPage()
    {
        if ($this->page === 1) {
            return;
        }

        $this->page--;
    }

    public function nextPage()
    {
        $this->page++;
    }

    public function paginate($data)
    {
        if (!isset($data->data)) {
            return;
        }

        $both = [];
        $profile = [];
        $interest = [];

        foreach (json_decode($data->data) as $rea) {
            if ($rea->recommended === 'both') {
                $both[] = $rea;
            } else if ($rea->recommended === 'profile') {
                $profile[] = $rea;
            } 
            else {
                $interest[] = $rea;
            }
        }

        $sortedData = array_merge($both, $profile, $interest);

        $items = collect($sortedData);
        $total = $items->count();
    
        return new LengthAwarePaginator(
            $items->forPage($this->page, 10),
            $total,
            10,
            $this->page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function sendFeedback()
    {
        $this->validate(
            ['message' => 'max:4096|required'],
            ['message.required' => 'O campo de mensagem é obrigatório.',
            'message.max' => 'A mensagem não pode ter mais que 4096 caracteres.']
        );

        $this->showMessage = false;

        Feedback::create([
            'feedback' => $this->message,
        ]);

        $this->reset('message');

        $this->showMessage = true;
    }

    public function insert()
    {
        $this->showMessage = false;

        $this->validate();

        Collaborator::create([
            'name'        => $this->name,
            'role'        => $this->role,
            'institution' => $this->institution,
            'reference'   => $this->reference,
            'rea_title'   => $this->reaTitle,
            'interest'    => $this->sanitizeSearch($this->interest),
            'profile'     => $this->sanitizeSearch($this->profile),
            'item'        => $this->sanitizeSearch($this->item),
        ]);

        // $getrange = 'Pagina1!A:F';

        // $collaboratorsrange = 'Folha1!A:C';

        // $values = Sheets::spreadsheet(config('google.post_spreadsheet_id'))
        //     ->sheet(config('google.post_sheet_id'))
        //     ->range($getrange)
        //     ->all();

        // $id = count($values);

        // Sheets::spreadsheet(config('google.collaborators_spreadsheet_id'))
        //     ->sheet(config('google.collaborators_sheet_id'))
        //     ->range($collaboratorsrange)
        //     ->append([
        //         [
        //             $this->name,
        //             $this->role,
        //             $this->institution,
        //         ],
        //     ]);

        // Sheets::spreadsheet(config('google.post_spreadsheet_id'))
        //     ->sheet(config('google.post_sheet_id'))
        //     ->range($getrange)
        //     ->append([
        //         [
        //             $id,
        //             $this->reference,
        //             $this->reaTitle,
        //             $this->sanitizeSearch($this->interest),
        //             $this->sanitizeSearch($this->profile),
        //             $this->sanitizeSearch($this->item),
        //         ],
        //     ], 'RAW');

        $this->reset('profile', 'interest', 'name', 'role', 'institution', 'reaTitle', 'reference', 'item');

        $this->showMessage = true;
    }

    public function search()
    {
        $this->validate();

        $this->timestampSession = now();

        Searches::create([
            'interest'    => $this->sanitizeSearch($this->interest),
            'profile'     => $this->sanitizeSearch($this->profile),
        ]);

        $getrange = 'Pagina1!A:F';

        $collaborators = collect(Collaborator::lazyById(100, $column = 'id'))
            ->map(function ($collaborator) {
                return [
                    $collaborator->id,
                    $collaborator->reference,
                    $collaborator->rea_title,
                    $collaborator->interest,
                    $collaborator->profile,
                    $collaborator->item
                ];
            });

        $values = $collaborators->all();

        $this->sheet = array_filter(
            $values, 
            fn ($rea) => $rea[3] === $this->sanitizeSearch($this->interest) && $rea[4] === $this->sanitizeSearch($this->profile)
        );

        $this->findInApi();

        $this->reset('profile', 'interest');
    }

    private function sanitizeSearch(string $search)
    {
        $search = mb_strtolower($search, 'UTF-8');
        $search = preg_replace('/[áàâã]/u', 'a', $search);
        $search = preg_replace('/[éèê]/u', 'e', $search);
        $search = preg_replace('/[íì]/u', 'i', $search);
        $search = preg_replace('/[óòôõ]/u', 'o', $search);
        $search = preg_replace('/[úùû]/u', 'u', $search);
        $search = preg_replace('/ç/u', 'c', $search);

        return $search;
    }

    private function findInApi()
    {
        $this->loading = true;

        $this->findAdequateTerm();

        $types = [];

        foreach ($this->sheet as $line) {
            $types[] = $line[5];

            if ($line[5] === 'e-book') {
                $types[] = 'livro digital';
            }
        }

        $collaboratorsTypes = collect(Collaborator::lazyById(100, $column = 'id'))
            ->map(function ($collaborator) {
                return [
                    $collaborator->item
                ];
            });

        $types = array_merge($types, $collaboratorsTypes->all());

        Data::create(['searched_at' => $this->timestampSession]);

        ProcessAquarela::dispatch($this->interestApiSearch, $types, $this->profile, $this->timestampSession);

        ProcessMecRed::dispatch($this->interestApiSearch, $types, $this->profile, $this->interest, $this->timestampSession);

        $this->loading = false;
    }

    private function findAdequateTerm()
    {
        foreach ($this->interestOptions as $option) {
            if ($this->sanitizeSearch($option) === $this->sanitizeSearch($this->interest)) {
                $this->interestApiSearch = $option;
            }
        }
    }

    public function render()
    {
        return view('livewire.find-r-e-a');
    }
}
