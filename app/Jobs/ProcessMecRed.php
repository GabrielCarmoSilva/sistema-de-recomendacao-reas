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

        $page = 0;

        $data = [];

        $model = Data::query()->where('searched_at', $this->time)->first();

        while (true) {
            $search = Http::withOptions(['verify' => false])->get(config('app.mecred.api') . '?page=' . $page . '&query=' . $this->search . '&search_class=LearningObject&order=score')->json();

            if (count($search) === 0) {
                break;
            }

            array_map(function ($rea) {
                array_map(function ($stage) use ($rea) {
                    $model = Data::query()->where('searched_at', $this->time)->first();

                    $data = json_decode($model->data);

                    $recommended = '';

                    if ($this->sanitizeSearch($stage['name']) === $this->sanitizeSearch($this->profile) && in_array($this->sanitizeSearch($rea['object_type']), $this->types)) {
                        $recommended = 'both';
                    }
                    else if ($this->sanitizeSearch($stage['name']) === $this->sanitizeSearch($this->profile)) {
                        $recommended = 'profile';
                    }
                    else {
                        $recommended = 'interest';
                    }

                    $data[] = [
                        'title' => $rea['name'],
                        'type'  => $rea['object_type'],
                        'link'  => $rea['link'],
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
