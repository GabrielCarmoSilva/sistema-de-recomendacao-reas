<?php

if (!function_exists('getMecRedURL')) {
    function getMecRedURL($query, $offset, $profile, $meta = null)
    {
        $educational_stages = '';
        $object_type = '';

        if ($profile === 'Educação infantil') {
            $educational_stages = '&educational_stages=1';
        }

        if ($profile === 'Ensino fundamental') {
            $educational_stages = '&educational_stages=2&educational_stages=3';
        }

        if ($profile === 'Ensino médio') {
            $educational_stages = '&educational_stages=4';
        }

        if ($profile === 'Ensino superior') {
            $educational_stages = '&educational_stages=5';
        }

        if ($meta && $meta === 'ma') {
            $object_type = '&object_type=2&object_type=3&object_type=4&object_type=5&object_type=7&object_type=8&object_type=9&object_type=12&object_type=13&object_type=16';
        }

        if ($meta && $meta === 'mpa') {
            $object_type = '&object_type=3&object_type=7&object_type=10&object_type=12&object_type=14';
        }

        if ($meta && $meta === 'mpe') {
            $object_type = '&obkect_type=1&object_type=2&object_type=3&object_type=5&object_type=13&object_type=16';
        }

        return "https://api.mecred.c3sl.ufpr.br/public/elastic/search?indexes=resources&query={$query}{$educational_stages}{$object_type}&state=accepted&sortBy=created_at&limit=30&offset={$offset}&filters=%7B%22pesquisa%22%3A%22{$query}%22%2C%22formato%22%3A%5B%5D%2C%22nivel%22%3A%5B%5D%2C%22idioma%22%3A%5B%5D%2C%22materias%22%3A%5B%5D%2C%22entidade%22%3A%22resources%22%2C%22categoria%22%3A%22created_at%22%7D";
    }
}