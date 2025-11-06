<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Data;
use Illuminate\Support\Facades\Http;

class ProcessEduplay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $search;

    public $time;

    public ?string $meta;

    /**
     * Create a new job instance.
     */
    public function __construct($search, $time, $meta)
    {
        $this->search = $search;

        $this->time = $time;

        $this->meta = $meta;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start_time = microtime(true); 

        $page = 1;

        $data = [];

        $model = Data::query()->where('searched_at', $this->time)->first();

        while ($page < 10) {
            $search = Http::withOptions(['verify' => false])
                ->get("https://eduplay.rnp.br/api/v1/search?term={$this->search}&page={$page}&quantity=10&type=0&order=0")->json();

            $page++;

            if (count($search) === 0) {
                break;
            }

            $interactivity = 'Ativo';
            $interactivity_level = 'Alto / Muito alto';
            $learning_style = 'Intuitivo / Ativo / Auditivo/Visual';
            $strategy = 'Ativa / Abstrata / Visual/Verbal';

            $interactivityData = [
                'interatividade' => $interactivity,
                'nivel_interatividade' => $interactivity_level,
                'estilo_aprendizagem' => $learning_style,
                'estrategia' => $strategy,
            ];

            $recommended = 'interest';

            if ($this->meta && ($this->meta === 'ma' || $this->meta === 'mpa')) {
                $recommended = 'meta';
            }

            foreach ($search['contents'] as $rea) {
                $data[] = array_merge([
                    'title' => $rea['name'],
                    'link' => $rea['contentUrl'],
                    'type' => 'Vídeo',
                    'repositorio' => 'Eduplay',
                    'recommended' => $recommended,
                    'titulo' => $rea['name'],
                    'descricao' => $rea['metatagDescription'] ?? '',
                    'tipoConteudo' => 'Vídeo',
                    'dtype' => 'T',
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
}
