<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Data;

class ProcessMecRed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $search;
    
    public $time;

    public array $types;

    public string $profile;

    public string $interest;

    /**
     * Create a new job instance.
     */
    public function __construct($search, $types, $profile, $interest, $time)
    {
        $this->search = $search;

        $this->types = $types;

        $this->profile = $profile;

        $this->interest = $interest;

        $this->time = $time;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $start_time = microtime(true); 

        $offset = 0;

        $data = [];

        $model = Data::query()->where('searched_at', $this->time)->first();

        while ($offset <= 120) {
            $search = Http::withOptions(['verify' => false])->get(getMecRedURL(str_replace(" ", "+", $this->search), $offset))->json();

            $offset += 30;

            array_map(function ($rea) {
                array_map(function ($stage) use ($rea) {
                    $model = Data::query()->where('searched_at', $this->time)->first();

                    $data = json_decode($model->data);

                    $recommended = '';
                    $interactivity = '';
                    $interactivity_level = '';
                    $learning_style = '';
                    $strategy = '';

                    if ($this->sanitizeSearch($stage['name']) === $this->sanitizeSearch($this->profile) && in_array($this->sanitizeSearch($rea['object_type']), $this->types)) {
                        $recommended = 'both';
                    }
                    else if ($this->sanitizeSearch($stage['name']) === $this->sanitizeSearch($this->profile)) {
                        $recommended = 'profile';
                    }
                    else {
                        $recommended = 'interest';
                    }

                    if (in_array($rea['object_type'], ['Jogo', 'Experimento prático', 'Trilha de aprendizagem', 'Exercício', 'Simulação', 'Resolução de problemas'])) {
                        $interactivity = 'Ativo';
                        $interactivity_level = 'Alto / Muito alto';
                        $learning_style = 'Intuitivo / Ativo / Auditivo/Visual';
                        $strategy = 'Ativa / Abstrata / Visual/Verbal';
                    }
                    else if (in_array($rea['object_type'], ['Vídeo', 'Texto', 'Animação', 'Livro digital', 'Hipertexto', 'Áudio', 'Imagem', 'Slide'])) {
                        $interactivity = 'Expositivo';
                        $interactivity_level = 'Baixo / Muito baixo';
                        $learning_style = 'Sensorial / Reflexivo / Auditivo/Visual';
                        $strategy = 'Passiva / Concreta / Visual/Verbal';
                    }
                    else if ($rea['object_type'] === 'Hipermídia') {
                        $interactivity = 'Misto';
                        $interactivity_level = 'Médio';
                        $learning_style = 'Intuitivo/Sensorial / Ativo/Reflexivo / Auditivo/Visual';
                        $strategy = 'Passiva / Concreta / Visual/Verbal';
                    }
                    else {
                        $interactivity = 'Não especificado';
                        $interactivity_level = 'Não especificado';
                        $learning_style = 'Não especificado';
                        $strategy = 'Não especificado';
                    }
                    

                    $data[] = [
                        'title' => $rea['name'],
                        'type'  => $rea['object_type'],
                        'link'  => $rea['link'],
                        'interatividade' => $interactivity,
                        'nivel_interatividade' => $interactivity_level,
                        'estilo_aprendizagem' => $learning_style,
                        'estrategia' => $strategy,
                        'repositorio' => 'MECRED',
                        'id'          => $rea['id'],
                        'recommended' => $recommended,
                    ];

                    $model->update(['data' => $data]);
                }, $rea['educational_stages']);
            }, $search);

            $page++;
        }

        $time = $model->time;

        $end_time = microtime(true); 
  
        $execution_time = ($end_time - $start_time); 

        $model->update(['finished' => true, 'time' => $time + $execution_time]);
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
