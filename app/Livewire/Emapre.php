<?php

namespace App\Livewire;

use Livewire\Component;

class Emapre extends Component
{
    public $responses = [];

    public function submit()
    {
        $rules = [];
        for ($i = 1; $i <= 28; $i++) {
            $rules["responses.$i"] = 'required|in:1,2,3';
        }

        $this->validate($rules);

        $fatores = [
            'ma'  => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            'mpa' => [13, 14, 15, 16, 17, 18, 19, 20, 21],
            'mpe' => [22, 23, 24, 25, 26, 27, 28],
        ];

        $resultados = [];

        foreach ($fatores as $chave => $itens) {
            $soma = 0;
            foreach ($itens as $i) {
                $soma += $this->responses[$i] ?? 0;
            }
            $media = count($itens) ? $soma / count($itens) : 0;
            $resultados[$chave] = round($media, 2);
        }

        $dominante = array_keys($resultados, max($resultados))[0];

        auth()->user()->questionnaire()->create([
            'ma' => $resultados['ma'],
            'mpa' => $resultados['mpa'],
            'mpe' => $resultados['mpe'],
            'dominant' => $dominante,
        ]);

        return redirect('/');
    }

    public function render()
    {
        $questions = [
            'Meta Aprender' => [
                1 => 'Quando vou mal em uma prova, estudo mais para a próxima.',
                2 => 'Eu não desisto facilmente diante de uma tarefa difícil.',
                3 => 'Faço minhas tarefas escolares porque estou interessado(a) nelas.',
                4 => 'Gosto dos trabalhos escolares com os quais aprendo algo, mesmo que cometa algum erro.',
                5 => 'Uma razão pela qual eu faço minhas tarefas escolares é por gostar delas.',
                6 => 'Uma razão importante pela qual faço minhas tarefas escolares é porque eu gosto de aprender coisas novas.',
                7 => 'Quanto mais difícil a matéria, mais eu gosto de tentar compreender.',
                8 => 'Eu gosto mais das tarefas quando elas me fazem pensar.',
                9 => 'Gosto quando uma matéria me faz sentir vontade de aprender mais.',
                10 => 'Uma importante razão pela qual eu estudo pra valer é porque eu quero aumentar meus conhecimentos.',
                11 => 'Gosto de tarefas difíceis e desafiadoras.',
                12 => 'Sou perseverante, mesmo quando uma tarefa me frustra.',
            ],
            'Meta Performance-Aproximação' => [
                13 => 'Para mim, é importante fazer as coisas melhor que os demais.',
                14 => 'É importante, para mim, fazer as coisas melhor que os meus colegas.',
                15 => 'Na minha turma, eu quero me sair melhor que os demais.',
                16 => 'Sinto-me bem-sucedido na aula quando sei que meu trabalho foi melhor que o dos meus colegas.',
                17 => 'Gosto de mostrar aos meus colegas que sei as respostas.',
                18 => 'Para mim, é importante conseguir concluir tarefas que meus colegas não conseguem.',
                19 => 'Sucesso na escola é fazer as coisas melhor que os outros.',
                20 => 'Gosto de participar dos trabalhos em grupo sempre que eu posso ser o líder.',
                21 => 'Ser o primeiro da classe é o que me leva a estudar.',
            ],
            'Meta Performance-Evitação' => [
                22 => 'Não respondo aos questionamentos feitos pelo professor por medo de falar alguma besteira.',
                23 => 'Não participo dos debates em sala de aula porque não quero que os colegas riam de mim.',
                24 => 'Não me posiciono nas discussões em sala de aula, pois acho que vou falar alguma coisa que os outros vão achar ruim.',
                25 => 'Não participo das aulas quando tenho dúvidas no conteúdo que está sendo trabalhado.',
                26 => 'Uma razão pela qual eu não participo da aula é evitar parecer ignorante.',
                27 => 'Não questiono o professor quando tenho dúvidas na matéria para não correr o risco de parecer menos inteligente que meus colegas.',
                28 => 'Não participo das aulas para evitar que meus colegas e professores me achem pouco inteligente.',
            ]
        ];

        return view('livewire.emapre', compact('questions'))->layout('layouts.app', ['title' => 'Questionário']);
    }
}