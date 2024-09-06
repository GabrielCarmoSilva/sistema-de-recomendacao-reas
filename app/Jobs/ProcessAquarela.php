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
        $start_time = microtime(true); 
        
        $page = 0;
        
        $data = [];

        $model = Data::query()->where('searched_at', $this->time)->first();

        while (true) {
            $search = Http::withOptions(['verify' => false])->get(config('app.aquarela.api') . '?string=' . $this->search . '&page=' . $page)->json()['reas'];

            $page++;

            if (count($search) === 0) {
                break;
            }

            foreach ($search as $rea) {
                $reachedLevel = false;
    
                if (
                    (stripos($rea['descricao'], 'criança') !== false || 
                    stripos($rea['descricao'], 'infantil') !== false ||
                    stripos($rea['titulo'], 'criança') !== false ||
                    stripos($rea['titulo'], 'infantil') !== false)) {
                        $reachedLevel = true;

                        $recommended = '';

                        if ($this->sanitizeSearch($this->profile) === 'educacao infantil' && in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types)) {
                            $recommended = 'both';
                        }
                        else if ($this->sanitizeSearch($this->profile) === 'educacao infantil') {
                            $recommended = 'profile';
                        }
                        else {
                            $recommended = 'interest';
                        }
    
                        $data[] = [
                            'title' => $rea['titulo'],
                            'link'  => $rea['links'][0]['href'],
                            'type'  => $rea['tipoConteudo'],
                            'repositorio' => 'Aquarela',
                            'recommended' => $recommended,
                        ];
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

                        $recommended = '';

                        if ($this->sanitizeSearch($this->profile) === 'ensino fundamental' && in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types)) {
                            $recommended = 'both';
                        }
                        else if ($this->sanitizeSearch($this->profile) === 'ensino fundamental') {
                            $recommended = 'profile';
                        }
                        else {
                            $recommended = 'interest';
                        }

                        $data[] = [
                            'title' => $rea['titulo'],
                            'link'  => $rea['links'][0]['href'],
                            'type'  => $rea['tipoConteudo'],
                            'repositorio' => 'Aquarela',
                            'recommended' => $this->sanitizeSearch($this->profile) === 'ensino fundamental' && in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types),
                        ];
                    }
                
                if (
                    (stripos($rea['descricao'], 'médio') !== false ||
                    stripos($rea['titulo'], 'médio') !== false)) {
                        $reachedLevel = true;

                        $recommended = '';

                        if ($this->sanitizeSearch($this->profile) === 'ensino medio' && in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types)) {
                            $recommended = 'both';
                        }
                        else if ($this->sanitizeSearch($this->profile) === 'ensino medio') {
                            $recommended = 'profile';
                        }
                        else {
                            $recommended = 'interest';
                        }
    
                        $data[] = [
                            'title' => $rea['titulo'],
                            'link'  => $rea['links'][0]['href'],
                            'type'  => $rea['tipoConteudo'],
                            'repositorio' => 'Aquarela',
                            'recommended' => $this->sanitizeSearch($this->profile) === 'ensino medio' && in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types),
                        ];
                    }
                
                if (!$reachedLevel) {
                    $recommended = '';

                    if ($this->sanitizeSearch($this->profile) === 'ensino superior' && in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types)) {
                        $recommended = 'both';
                    }
                    else if ($this->sanitizeSearch($this->profile) === 'ensino superior') {
                        $recommended = 'profile';
                    }
                    else {
                        $recommended = 'interest';
                    }

                    $data[] = [
                        'title' => $rea['titulo'],
                        'link'  => $rea['links'][0]['href'],
                        'type'  => $rea['tipoConteudo'],
                        'repositorio' => 'Aquarela',
                        'recommended' => $this->sanitizeSearch($this->profile) === 'ensino superior' && in_array($this->sanitizeSearch($rea['tipoConteudo']), $this->types)
                    ];
                }
            }

            if ($model->data !== null) {
                $decodedData = json_decode($model->data);
                $decodedData = array_merge($decodedData, $data);
            
                $model->update(['data' => json_encode($decodedData)]);
            } else {
                $model->update(['data' => json_encode($data)]);
            }
        }

        $end_time = microtime(true); 
  
        $execution_time = ($end_time - $start_time); 

        $model->update(['time' => $execution_time]);
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
