<?php

if (!function_exists('removerAcentosECedilha')) {

    function removerAcentosECedilha($str)
    {
        // Remove acentos
        $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);

        // Substitui ç por c
        $str = str_replace('ç', 'c', $str);

        return $str;
    }
}

if (!function_exists('dd')) {

    function dd($data) {
        echo '<pre>';
        print_r($data);
        die();
    }

}
