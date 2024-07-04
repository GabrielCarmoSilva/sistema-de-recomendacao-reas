<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Revolution\Google\Sheets\Facades\Sheets;
use App\Jobs\ProcessAquarela;
use App\Jobs\ProcessMecRed;
use App\Models\Data;

class FindREA extends Component
{
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

    public bool $showMessage = false;

    public bool $loading = false;

    public array $sheet = [];

    public array $header = [];

    public array $reas = [];

    public ?string $userType = null;

    private array $interestOptions = [
        'algoritmos',
        'decomposição',
        'reconhecimento de padrões',
        'abstração'
    ];

    public string $interestApiSearch = '';

    public function selectUserType(?string $type = null)
    {
        $this->userType = $type;

        $this->showMessage = false;
    }

    public function insert()
    {
        $this->showMessage = false;

        $this->validate();

        $getrange = 'Pagina1!A:F';

        $collaboratorsrange = 'Folha1!A:C';

        $values = Sheets::spreadsheet(config('google.post_spreadsheet_id'))
            ->sheet(config('google.post_sheet_id'))
            ->range($getrange)
            ->all();

        $id = count($values);

        Sheets::spreadsheet(config('google.collaborators_spreadsheet_id'))
            ->sheet(config('google.collaborators_sheet_id'))
            ->range($collaboratorsrange)
            ->append([
                [
                    $this->name,
                    $this->role,
                    $this->institution,
                ],
            ]);

        Sheets::spreadsheet(config('google.post_spreadsheet_id'))
            ->sheet(config('google.post_sheet_id'))
            ->range($getrange)
            ->append([
                [
                    $id,
                    $this->reference,
                    $this->reaTitle,
                    $this->sanitizeSearch($this->interest),
                    $this->sanitizeSearch($this->profile),
                    $this->sanitizeSearch($this->item),
                ],
            ], 'RAW');

        $this->reset('profile', 'interest', 'name', 'role', 'institution', 'reaTitle', 'reference', 'item');

        $this->showMessage = true;
    }

    public function search()
    {
        $this->validate();

        $getrange = 'Pagina1!A:F';

        $values = Sheets::spreadsheet(config('google.post_spreadsheet_id'))
            ->sheet(config('google.post_sheet_id'))
            ->range($getrange)
            ->all();

        $this->header = $values[0];

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
        }

        $types[] = 'livro digital';

        Data::query()->delete();

        ProcessAquarela::dispatch($this->interestApiSearch, $types, $this->profile);

        ProcessMecRed::dispatch($this->interestApiSearch, $types, $this->profile, $this->interest);

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
