<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Data;

class ProcessAquarela implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $search;
    
    public $time;

    public array $types;

    public string $profile;

    /**
     * Create a new job instance.
     */
    public function __construct($search, $types, $profile, $time)
    {
        $this->search = $search;

        $this->types = $types;

        $this->profile = $profile;

        $this->time = $time;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $total = Http::get(config('app.aquarela.api') . '?string=' . $this->search)->json()['paginacao']['totalElements'];

        $search = Http::get(config('app.aquarela.api') . '?string=' . $this->search . '&size=' . $total)->json()['reas'];

        $data = [];

        foreach ($search as $rea) {
            $reachedLevel = false;

            if (
                (stripos($rea['descricao'], 'criança') !== false || 
                stripos($rea['descricao'], 'infantil') !== false ||
                stripos($rea['titulo'], 'criança') !== false ||
                stripos($rea['titulo'], 'infantil') !== false)) {
                    $reachedLevel = true;

                    if ($this->sanitizeSearch($this->profile) === 'educacao infantil' && 
                        in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types)) {
                        $data[] = [
                            'title' => $rea['titulo'],
                            'link'  => $rea['links'][0]['href'],
                            'type'  => $rea['tipoConteudo'],
                            'repositorio' => 'Aquarela'
                        ];
                    }
                }
            
            if (
                (stripos($rea['descricao'], 'fundamental') !== false ||
                stripos($rea['descricao'], 'sexto ano') !== false ||
                stripos($rea['descricao'], '6º') !== false ||
                stripos($rea['descricao'], 'sétimo ano') !== false ||
                stripos($rea['descricao'], '7º') !== false ||
                stripos($rea['descricao'], 'oitavo ano') !== false ||
                stripos($rea['descricao'], '8º') !== false ||
                stripos($rea['descricao'], 'nono ano') !== false ||
                stripos($rea['descricao'], '9º') !== false ||
                str_contains($rea['descricao'], 'EF') ||
                stripos($rea['titulo'], 'fundamental') !== false ||
                str_contains($rea['titulo'], 'EF')))  {
                    $reachedLevel = true;

                    if ($this->sanitizeSearch($this->profile) === 'ensino fundamental' && 
                        in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types)) {
                        $data[] = [
                            'title' => $rea['titulo'],
                            'link'  => $rea['links'][0]['href'],
                            'type'  => $rea['tipoConteudo'],
                            'repositorio' => 'Aquarela'
                        ];
                    }
                }
            
            if (
                (stripos($rea['descricao'], 'médio') !== false ||
                stripos($rea['titulo'], 'médio') !== false)) {
                    if ($this->sanitizeSearch($this->profile) === 'ensino medio' && 
                        in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types)) {
                        $reachedLevel = true;

                        $data[] = [
                            'title' => $rea['titulo'],
                            'link'  => $rea['links'][0]['href'],
                            'type'  => $rea['tipoConteudo'],
                            'repositorio' => 'Aquarela'
                        ];
                    }
                }
            
            if (!$reachedLevel) {
                if ($this->sanitizeSearch($this->profile) === 'ensino superior' && 
                    in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types)) {
                    $data[] = [
                        'title' => $rea['titulo'],
                        'link'  => $rea['links'][0]['href'],
                        'type'  => $rea['tipoConteudo'],
                        'repositorio' => 'Aquarela'
                    ];
                }
            }
        }

        $model = Data::query()->where('searched_at', $this->time)->first();

        $model->update(['data' => json_encode($data)]);
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
}
