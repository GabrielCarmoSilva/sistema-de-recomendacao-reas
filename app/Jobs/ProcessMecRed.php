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
    
    public Data $data;

    public array $types;

    public string $profile;

    public string $interest;

    /**
     * Create a new job instance.
     */
    public function __construct($search, $types, $profile, $interest)
    {
        $this->search = $search;

        $this->types = $types;

        $this->profile = $profile;

        $this->interest = $interest;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $page = 0;

        while (true) {
            $search = Http::get(config('app.mecred.api') . '?page=' . $page . '&results_per_page=250&query=' . $this->search . '&search_class=LearningObject&order=score')->json();

            if (count($search) === 0) {
                break;
            }

            array_map(function ($rea) {
                array_map(function ($stage) use ($rea) {
                    if ($this->sanitizeSearch($stage['name']) === $this->sanitizeSearch($this->profile) && 
                        in_array($this->sanitizeSearch($rea['object_type']), $this->types)) {
                        $model = Data::orderBy('id', 'desc')->first();

                        $data = json_decode($model->data);

                        $data[] = [
                            'title' => $rea['name'],
                            'type'  => $rea['object_type'],
                            'repositorio' => 'MECRED',
                        ];

                        $model->update(['data' => $data]);
                    }
                }, $rea['educational_stages']);
            }, $search);

            $page++;
        }

        $model = Data::orderBy('id', 'desc')->first();

        $model->update(['finished' => true]);
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
