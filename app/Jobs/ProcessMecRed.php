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

    public ?string $meta;

    /**
     * Create a new job instance.
     */
    public function __construct($search, $types, $profile, $interest, $time, $meta)
    {
        $this->search = $search;

        $this->types = $types;

        $this->profile = $profile;

        $this->interest = $interest;

        $this->time = $time;

        $this->meta = $meta;
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
            $search = Http::withOptions(['verify' => false])->get(getMecRedURL(str_replace(" ", "+", $this->search), $offset, $this->profile, $this->meta))->json();

            $offset += 30;

            if (count($search) === 0) {
                break;
            }

            $recommended = 'both';

            if ($this->meta) {
                $recommended = 'meta';
            }

            $interactivity = 'Não especificado';
            $interactivity_level = 'Não especificado';
            $learning_style = 'Não especificado';
            $strategy = 'Não especificado';

            if ($this->meta && ($this->meta === 'ma' || $this->meta === 'mpa')) {
                $interactivity = 'Ativo';
                $interactivity_level = 'Alto / Muito alto';
                $learning_style = 'Intuitivo / Ativo / Auditivo/Visual';
                $strategy = 'Ativa / Abstrata / Visual/Verbal';
            } elseif ($this->meta && $this->meta === 'mpe') {
                $interactivity = 'Expositivo';
                $interactivity_level = 'Baixo / Muito baixo';
                $learning_style = 'Sensorial / Reflexivo / Auditivo/Visual';
                $strategy = 'Passiva / Concreta / Visual/Verbal';
            }

            $interactivityData = [
                'interatividade' => $interactivity,
                'nivel_interatividade' => $interactivity_level,
                'estilo_aprendizagem' => $learning_style,
                'estrategia' => $strategy,
            ];

            foreach ($search as $rea) {
                $data[] = array_merge([
                    'title' => $rea['name'],
                    'link' => '',
                    'type' => '',
                    'repositorio' => 'MECRED',
                    'recommended' => $recommended,
                    'titulo' => $rea['name'],
                    'descricao' => '',
                    'tipoConteudo' => '',
                    'dtype' => '',
                ], $interactivityData);
            }

            if ($model->data !== null) {
                $decodedData = json_decode($model->data);
                $decodedData = array_merge($decodedData, $data);
                $model->update(['data' => json_encode($decodedData)]);
            } else {
                $model->update(['data' => json_encode($data)]);
            }
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
