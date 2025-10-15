<?php

if (!function_exists('getMecRedURL')) {
    function getMecRedURL($query, $offset)
    {
        return "https://api.mecred.c3sl.ufpr.br/public/elastic/search?indexes=resources&query={$query}&state=accepted&sortBy=created_at&limit=30&offset={$offset}&filters=%7B%22pesquisa%22%3A%22{$query}%22%2C%22formato%22%3A%5B%5D%2C%22nivel%22%3A%5B%5D%2C%22idioma%22%3A%5B%5D%2C%22materias%22%3A%5B%5D%2C%22entidade%22%3A%22resources%22%2C%22categoria%22%3A%22created_at%22%7D";
    }
}