<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Data;
//LlaMa
//slm - small language model
class ProcessAquarela implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $search;
    public $time;
    public array $types;
    public string $profile;
    public ?string $meta;

    public function __construct($search, $types, $profile, $time, $meta = null)
    {
        $this->search = $search;
        $this->types = $types;
        $this->profile = $profile;
        $this->time = $time;
        $this->meta = $meta;
    }

    public function handle()
    {   

        $start_time = microtime(true); 
        $page = 0;
        $data = [];

        $model = Data::query()->where('searched_at', $this->time)->first();

        while (true) {
            $search = Http::withOptions(['verify' => false])
                ->get(config('app.aquarela.api') . '?string=' . $this->search . '&page=' . $page)
                ->json()['reas'];

            $page++;
            if (count($search) === 0) {
                break;
            }

            foreach ($search as $rea) {
                $reachedLevel = false;

                if ($rea['dtype'] === 'T') {
                    $interactivity = 'Ativo';
                    $interactivity_level = 'Alto / Muito alto';
                    $learning_style = 'Intuitivo / Ativo / Auditivo/Visual';
                    $strategy = 'Ativa / Abstrata / Visual/Verbal';
                } elseif ($rea['dtype'] === 'D') {
                    $interactivity = 'Expositivo';
                    $interactivity_level = 'Baixo / Muito baixo';
                    $learning_style = 'Sensorial / Reflexivo / Auditivo/Visual';
                    $strategy = 'Passiva / Concreta / Visual/Verbal';
                } else {
                    $interactivity = 'Não especificado';
                    $interactivity_level = 'Não especificado';
                    $learning_style = 'Não especificado';
                    $strategy = 'Não especificado';
                }

                $interactivityData = [
                    'interatividade' => $interactivity,
                    'nivel_interatividade' => $interactivity_level,
                    'estilo_aprendizagem' => $learning_style,
                    'estrategia' => $strategy,
                ];

                // Seu filtro original para níveis educacionais e recomendação
                if (
                    (stripos($rea['descricao'], 'criança') !== false || 
                    stripos($rea['descricao'], 'infantil') !== false ||
                    stripos($rea['titulo'], 'criança') !== false ||
                    stripos($rea['titulo'], 'infantil') !== false)) {
                        $reachedLevel = true;
                        $recommended = $this->definirRecomendacao('educacao infantil', $rea);
                }
                elseif (
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
                    str_contains($rea['titulo'], 'EF'))) {
                        $reachedLevel = true;
                        $recommended = $this->definirRecomendacao('ensino fundamental', $rea);
                }
                elseif (
                    (stripos($rea['descricao'], 'médio') !== false ||
                    stripos($rea['titulo'], 'médio') !== false)) {
                        $reachedLevel = true;
                        $recommended = $this->definirRecomendacao('ensino medio', $rea);
                }
                else {
                    $recommended = $this->definirRecomendacao('ensino superior', $rea);
                }

                $data[] = array_merge([
                    'title' => $rea['titulo'],
                    'link'  => $rea['links'][0]['href'],
                    'type'  => $rea['tipoConteudo'],
                    'repositorio' => 'Aquarela',
                    'recommended' => $recommended,
                    'titulo' => $rea['titulo'],
                    'descricao' => $rea['descricao'],
                    'tipoConteudo' => $rea['tipoConteudo'],
                    'dtype' => $rea['dtype'],
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

        $end_time = microtime(true); 
        $execution_time = ($end_time - $start_time); 
        $model->update(['time' => $execution_time]);

        $model->update(['finished' => true, 'time' => $execution_time]);
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

    private function definirRecomendacao(string $nivel, array $rea): string
    {
        $perfilSanitizado = $this->sanitizeSearch($this->profile);
        $tipoSanitizado = $this->sanitizeSearch($rea['tipoConteudo']);
        $response = $this->classificarMetaComLLM($rea);

        if ($this->meta && $this->analisarMeta($response, $this->meta)) {
            return 'meta';
        } elseif ($perfilSanitizado === $nivel && in_array($tipoSanitizado, $this->types)) {
            return 'both';
        } elseif ($perfilSanitizado === $nivel) {
            return 'profile';
        } else {
            return 'interest';
        }
    }

    private function analisarMeta(string $response, string $meta = null): bool {
        if (!$meta) {
            return false;
        }

        if ($meta === 'ma' && str_contains($response, 'Aprendizagem')) {
            return true;
        }

        if ($meta === 'mpa' && str_contains($response, 'Performance') && str_contains($response, 'Aproximação')) {
            return true;
        }

        if ($meta === 'mpe' && str_contains($response, 'Performance') && str_contains($response, 'Evitação')) {
            return true;
        }

        return false;
    }

    private function classificarMetaComLLM(array $rea): string
    {
        $prompt = <<<PROMPT
Classifique o seguinte recurso educacional segundo as metas:

Título: {$rea['titulo']}
Descrição: {$rea['descricao']}
Tipo: {$rea['tipoConteudo']}
DType: {$rea['dtype']}

Metas:
1. Aprendizagem: foco em domínio de conteúdo, compreensão profunda, estratégias cognitivas/metacognitivas.
2. Performance Aproximação: foco em boas notas, reconhecimento, resultado.
3. Performance Evitação: foco em evitar erros, tarefas simples, linguagem acessível.

Responda com uma única palavra: "Aprendizagem", "Performance Aproximação", "Performance Evitação". Caso seja mais de uma, especifique a que mais se aproxima.
PROMPT;

        $response = Http::timeout(60)->post("http://127.0.0.1:11434/api/generate", [
            'model' => 'llama3.2',
            'prompt' => $prompt,
            'stream' => false, // important: false to get a single JSON response
        ]);


        $result = $response->json();
        
        return trim($result['response'] ?? 'Não classificado');
    }
}
