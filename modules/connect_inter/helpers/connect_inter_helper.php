<?php

use Carbon\Carbon;

defined('BASEPATH') or exit('No direct script access allowed');


if (function_exists('get_instance')) {
    $CI = &get_instance();
    $CI->load->helper('url');
}

/**
 * A posta do arquivo. Exemplo: /home/perfex/media/documentacao_eleitoral/timbres
 */
if (!defined('BASE_PATH_STORE_TIMBRES')) {
    $ci = &get_instance();

    $ci->load->helper('path');

    $media_folder = $ci->app->get_media_folder();

    $mediaPath = FCPATH . $media_folder .
        '/central_documentos_estaduais/timbres/';

    define('BASE_PATH_STORE_TIMBRES', $mediaPath);
}

if (!function_exists('inter_seu_numero_format')) {
    function inter_seu_numero_format($number, $format, $applied_prefix, $date)
    {
        $prefixPadding  = 7;
        if ($format == 1) {
            $number = $applied_prefix . str_pad($number, $prefixPadding, '0', STR_PAD_LEFT);
        } elseif ($format == 2) {
            $number = $applied_prefix . date('Y', strtotime($date)) . '' . str_pad($number, $prefixPadding, '0', STR_PAD_LEFT);
        } elseif ($format == 3) {
            $number = $applied_prefix . str_pad($number, $prefixPadding, '0', STR_PAD_LEFT);
        } elseif ($format == 4) {
            $number = $applied_prefix . str_pad($number, $prefixPadding, '0', STR_PAD_LEFT);
        }
        return  $number;
    }
}


/**
 * A url do arquivo. Exemplo: http://meusite.com/media/documentacao_eleitoral
 */
if (!defined('URL_MIDIA_DOC_ELEITORAL')) {
    define('URL_MIDIA_DOC_ELEITORAL', base_url('media/documentacao_eleitoral/'));
}

if (!function_exists('uniqidReal')) {

    function uniqidReal($lenght = 13)
    {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }
}


if (!function_exists('arrayCastRecursive')) {

    /**
     *  Converte stdClass para array
     * @param type $array
     * @return \stdClass
     */
    function arrayCastRecursive($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = arrayCastRecursive($value);
                }
                if ($value instanceof stdClass) {
                    $array[$key] = arrayCastRecursive((array) $value);
                }
            }
        }
        if ($array instanceof stdClass) {
            return arrayCastRecursive((array) $array);
        }
        return $array;
    }
}

if (!function_exists('pluck_array_by_keys')) {

    /**
     *
     * @param array $array
     * @param array $chaves
     * @return array
     */
    function pluck_array_by_keys(array $array, array $chaves)
    {
        $new_array = [];
        foreach ($array as $key => $value) {
            foreach (array_keys($value) as $i) {
                if (in_array($i, $chaves)) {
                    $new_array[$key][$i] = $value[$i];
                }
            }
        }
        return $new_array;
    }
}

if (!function_exists('ukDate')) {

    /**
     *
     * @param type $datetime
     * @param type $timestamp
     * @return type
     */
    function ukDate($datetime = null, $timestamp = false)
    {
        $datetime = $datetime ? $datetime : Carbon::now();
        $format = $timestamp ? 'd/m/Y H:i' : 'd/m/Y';
        $timestamp = $timestamp ? 'Y-m-d H:i:s' : 'Y-m-d';
        return Carbon::createFromFormat($format, $datetime)->format($timestamp);
    }
}

if (!function_exists('numeroProtocoloAleatorio')) {

    /**
     *
     * @param type $form_id
     * @param type $sequenceAnswerId
     */
    function numeroProtocoloAleatorio($form_id, $sequenceAnswerId)
    {
        $random_certificado = mt_rand(10000000, 99999999);
        $protocolo_certificado = '' . $sequenceAnswerId . sprintf('%02d', $form_id) . $random_certificado;
        return $protocolo_certificado;
    }
}

if (!function_exists('formatarNumeroComZeroAntes')) {

    /**
     *
     * @param type $numero
     * @param type $format
     * @return type
     */
    function formatarNumeroComZeroAntes($numero, $format = 4)
    {
        return sprintf("%0$format" . 'd', $numero);
    }
}


//  Tirar esse comentário
if (!function_exists('brDate')) {

    /**
     *
     * @param type $datetime
     * @param type $timestamp
     * @return type
     */
    function brDate($datetime = null, $timestamp = false)
    {
        $datetime = $datetime ? $datetime : Carbon::now();

        $timestamp = $timestamp ? 'd/m/Y H:i' : 'd/m/Y';

        return Carbon::parse($datetime)->format($timestamp);
    }
}

if (!function_exists('moeda2float')) {

    function moeda2float($value)
    {
        if (empty($value)) {
            return null;
        }

        $new = str_replace('.', '', $value);

        return str_replace(',', '.', $new);
    }
}

if (!function_exists('moeda')) {

    /**
     *
     * @param type $get_valor
     * @return int
     */
    function moeda($get_valor)
    {
        if (!$get_valor) {
            return 0;
        }

        $source = array('.', ',');
        $replace = array('', '.');
        $valor = str_replace($source, $replace, $get_valor);
        return $valor;
    }
}

if (!function_exists('soNumeros')) {

    function soNumeros($str)
    {
        return preg_replace('/\D/', '', $str);
    }
}

if (!function_exists('money')) {

    /**
     *
     * @param type $get_valor
     * @return type
     */
    function money($get_valor)
    {
        $valor = number_format($get_valor, 2, ',', '.');
        return $valor;
    }
}

if (!function_exists('startsWith')) {

    /**
     * String starts with
     * @param  string $haystack
     * @param  string $needle
     * @return boolean
     */
    function startsWith($haystack, $needle)
    {
        return \app\services\utilities\Str::startsWith($haystack, $needle);
    }
}

if (!function_exists('endsWith')) {

    /**
     * String ends with
     * @param  string $haystack
     * @param  string $needle
     * @return boolean
     */
    function endsWith($haystack, $needle)
    {
        return \app\services\utilities\Str::endsWith($haystack, $needle);
    }
}

if (!function_exists('createSlug')) {

    function createSlug($string)
    {
        return strtolower(url_title(convert_accented_characters(
            $string,
            'dash',
            true
        )));
    }
}

if (!function_exists('brDataPorExtenso')) {

    /**
     *
     * @example  $data = 2021-08-04 00:00:00;
     * @param type $data
     * @return type
     */
    function brDataPorExtenso($data)
    {
        $arr = [1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
        $dia = date('d', strtotime($data));
        $mes = $arr[date('n', strtotime($data))];
        $ano = date('Y', strtotime($data));
        return "$dia de $mes de $ano";
    }
}

if (!function_exists('arrayCastRecursive')) {

    function arrayCastRecursive($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = arrayCastRecursive($value);
                }
                if ($value instanceof stdClass) {
                    $array[$key] = arrayCastRecursive((array) $value);
                }
            }
        }
        if ($array instanceof stdClass) {
            return arrayCastRecursive((array) $array);
        }
        return $array;
    }
}

if (!function_exists('tcpdf')) {

    function tcpdf()
    {
        require_once('tcpdf/config/lang/eng.php');
        require_once('tcpdf/tcpdf.php');
    }
}

if (!function_exists('formatResponse')) {

    /**
     *
     * @param type $data Resultado
     * @param type $message A mensagem a ser mostrada
     * @param type $verb POST|GET|PUT|DELETE
     * @param type $type success|error|info
     */
    function formatResponse(
        $data,
        $message,
        $verb = "insert",
        $type = 'success'
    ) {
        header('Content-Type: application/json');

        $arr = [
            'type' => $type,
            'data' => $data,
            'verb' => $verb,
            'user_message' => $message
        ];

        echo json_encode($arr);
    }
}

if (!function_exists('docsEleitoraisGabinetes')) {

    function docsEleitoraisGabinetes($id = null)
    {

        $gabinetes = [
            ['id' => 1, 'nome' => 'Gabinete do GME'],
            ['id' => 2, 'nome' => 'Gabinete do MCE'],
            ['id' => 3, 'nome' => 'Gabinete do ICCE'],
            ['id' => 4, 'nome' => 'Outros'],
        ];
        // Verifica se a variável é nula,se for, então retorna o array.
        if (is_null($id)) {
            return $gabinetes;
        }
        // Se a variável $id não for nula, então, faz o filtro.
        $data = array_filter($gabinetes, function ($item) use ($id) {
            return $item['id'] == $id;
        });

        // Verifica se o array '$data' tem mais do que zero elementos.
        if (count($data) > 0) {
            // Converte para stdClass do php
            return (object) array_values($data)[0];
        }
        // Retorna um stdClass do php vazio.
        return (object) ['nome' => null, 'id' => null];
    }
}

if (!function_exists('getOrientacao')) {

    function getOrientacao($index = null)
    {

        $orientacoes = [
            ['tipo' => 'P', 'nome' => 'RETRATO'],
            ['tipo' => 'L', 'nome' => 'PAISAGEM']
        ];

        if (is_null($index)) {
            return $orientacoes;
        }

        $data = array_filter($orientacoes, function ($item) use ($index) {
            return $item['tipo'] == $index;
        });

        // Verifica se o array '$data' tem mais do que zero elementos.
        if (count($data) > 0) {
            // Converte para stdClass do php
            return (object) array_values($data)[0];
        }
        // Retorna um stdClass do php vazio.
        return (object) ['nome' => null, 'tipo' => null];
    }
}

if (!function_exists('scannerDocsFiles')) {


    function scannerDocsFiles($mediaPath = null)
    {

        if (is_null($mediaPath)) {

            $mediaPath = BASEPATH_STORE_TIMBRES;
        }

        $dh = opendir($mediaPath);

        while (false !== ($filename = readdir($dh))) {
            if ($filename != '.' && $filename != '..') {
                $files[] = $filename;
            }
        }
        return $files;
    }
}




if (!function_exists('fazer_upload')) {

    /**
     *
     * @param type $mediaPath O caminho para salvar os arquivos
     * @param type $input_name O nome do input do cliente
     * @param type $column_table O nome da coluna no banco de dados
     * @return type
     */
    function fazer_upload($mediaPath, $input_name, $column_table)
    {

        $total = count($_FILES);

        if ($total > 0) {

            $files = [];

            if (!is_dir($mediaPath)) {
                mkdir($mediaPath, 0755, true);
            }

            $config['upload_path'] = $mediaPath;
            $config['allowed_types'] = 'jpg|jpeg|png|svg|pdf|docx|doc|xlsx|xls';
            $config['max_size'] = 20480;
            $config['max_size'] = 20480;
            $config['encrypt_name'] = TRUE;
            $config['max_width'] = 0;
            $config['max_height'] = 0;
            $config['file_ext_tolower'] = TRUE;

            $ci = &get_instance();

            $ci->load->library('upload', $config);

            $data = [];

            $count = count($_FILES[$input_name]['name']);

            for ($i = 0; $i < $count; $i++) {

                if (!empty($_FILES[$input_name]['name'][$i])) {

                    $_FILES['file']['name'] = $_FILES[$input_name]['name'][$i];
                    $_FILES['file']['type'] = $_FILES[$input_name]['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES[$input_name]['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES[$input_name]['error'][$i];
                    $_FILES['file']['size'] = $_FILES[$input_name]['size'][$i];

                    $ci->upload->initialize($config);

                    if ($ci->upload->do_upload('file')) {

                        $uploadData = $ci->upload->data();

                        // $filename = $uploadData['file_name'];

                        $data[$column_table][] = $uploadData;
                    }
                }
            }

            return $data;
        }
    }
}

if (!function_exists('parseString')) {

    /**
     *
     * @param type $map $map = array(
     * 'form_id' => 'something'
     * );
     * @param type $string  A String de entrada
     * @param type $tag_initical
     * @param type $tag_close
     * @return type
     */
    function parseString(
        $map,
        $string,
        $tag_initical = '<strong>',
        $tag_close = '</strong>'
    ) {


        preg_match_all('/\{(.*?)\}/', $string, $matches);

        foreach ($matches[1] as $value) {
            $valueReplaced = $map[$value];
            if ($value == "form_id") {
                $valueReplaced = sprintf('%06d', $valueReplaced);
            }
            $string = str_replace('{' . $value . '}', $tag_initical .
                $valueReplaced . $tag_close, $string);
        }

        return $string;
    }
}

if (!function_exists('enviarEmail')) {

    /**
     *
     * @param type $message A mensagem para ser enviada.
     * @param type $Email O email para o qual a mensagem será enviada.
     */
    function enviarEmail($message, $email = null)
    {
        $ci = &get_instance();

        if (is_null($email)) {
            $email = get_option("smtp_email");
        }

        $template = new StdClass();

        $template->message = get_option('email_header') . $message .
            get_option('email_footer');

        $template->fromname = get_option('companyname') != '' ?
            get_option('companyname') : 'TESTE';

        // $template = parse_email_template($template);


        $config = array(
            'protocol' => $ci->encryption->decrypt(get_option("smtp_password")),
            'smtp_host' => get_option("smtp_host"),
            'smtp_port' => get_option("smtp_port"),
            'smtp_user' => get_option("smtp_user"),
            'smtp_pass' => get_option("smtp_pass"),
            'crlf' => "\r\n",
            'newline' => "\r\n"
        );

        $config['mailtype'] = "html";

        $ci->load->library('email', $config);

        $ci->email->clear(true);
        $ci->email->set_newline("\r\n");
        $ci->email->from(get_option("smtp_email"), get_option('companyname'));
        $ci->email->to($email);
        $ci->email->subject($template->fromname);
        $ci->email->message($template->message);
        $ci->email->send();
    }
}

if (!function_exists('allowedTypes')) {

    function allowedTypes()
    {
        return ['text', 'textarea', 'number', 'select', 'file', 'date'];
    }
}

if (!function_exists('statuses')) {

    function statuses($_status)
    {
        $status = [1 => 'AGUARDANDO', 'APROVADO', 'RECUSADO'];

        if (!$_status)
            return $status;

        return $status[$_status];
    }
}

if (!function_exists('labelStatuses')) {

    function labelStatuses($label)
    {

        $labeles = [1 => 'warning', 'success', 'danger'];

        if (!$label)
            return $labeles;

        return $labeles[$label];
    }
}


if (!function_exists('inAllowedTypes')) {

    function inAllowedTypes($needle)
    {
        return in_array($needle, allowedTypes());
    }
}

if (!function_exists('sliceArrayByKeyHeader')) {

    function sliceArrayByKeyHeader($array)
    {
        $output = [];

        $outputPointer = 0;

        foreach (json_decode(json_encode($array)) as $input) {
            if ($input->className === "doc-eleitoral-step") {
                $outputPointer++;
            } else {
                $output[$outputPointer][] = $input;
            }
        }

        return $output;
    }
}

if (!function_exists('dd')) {

    function dd($die)
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();

        for ($i = 0; $i < $numargs; $i++) {
            if ($i > 0) {
                $value = $arg_list[$i];

                $CI = &get_instance();
                $CI->load->library('unit_test');

                $bt = debug_backtrace();

                $src = file($bt[0]["file"]);

                $line = $src[$bt[0]['line'] - 1];

                preg_match('#' . __FUNCTION__ . '\((.+)\)#', $line, $match);

                $max = strlen($match[1]);

                $varname = null;

                $arr_values = explode(',', $match[1]);

                if (is_object($value)) {
                    $message = '<span class="vayes-debug-badge vayes-debug-badge-object">OBJECT</span>';
                } elseif (is_array($value)) {
                    $message = '<span class="vayes-debug-badge vayes-debug-badge-array">ARRAY</span>';
                } elseif (is_string($value)) {
                    $message = '<span class="vayes-debug-badge vayes-debug-badge-string">STRING</span>';
                } elseif (is_int($value)) {
                    $message = '<span class="vayes-debug-badge vayes-debug-badge-integer">INTEGER</span>';
                } elseif (is_true($value)) {
                    $message = '<span class="vayes-debug-badge vayes-debug-badge-true">TRUE [BOOLEAN]</span>';
                } elseif (is_false($value)) {
                    $message = '<span class="vayes-debug-badge vayes-debug-badge-false">FALSE [BOOLEAN]</span>';
                } elseif (is_null($value)) {
                    $message = '<span class="vayes-debug-badge vayes-debug-badge-null">NULL</span>';
                } elseif (is_float($value)) {
                    $message = '<span class="vayes-debug-badge vayes-debug-badge-float">FLOAT</span>';
                } else {
                    $message = 'N/A';
                }

                $output = '<div style="clear:both;"></div>';
                $output .= '<meta charset="UTF-8" />';
                $output .= '<style>body{margin:0}::selection{background-color:#E13300!important;color:#fff}::moz-selection{background-color:#E13300!important;color:#fff}::webkit-selection{background-color:#E13300!important;color:#fff}div.debugbody{background-color:#fff;margin:0px;font:9px/12px normal;font-family:Arial,Helvetica,sans-serif;color:#4F5155;min-width:500px;padding:10px;margin-bottom:0px;}a.debughref{color:#039;background-color:transparent;font-weight:400}h1.debugheader{color:#444;background-color:transparent;border-bottom:1px solid #D0D0D0;font-size:12px;line-height:14px;font-weight:700;margin:0 0 14px;padding:14px 15px 10px;font-family:\'Ubuntu Mono\',Consolas}code.debugcode{font-family:\'Ubuntu Mono\',Consolas,Monaco,Courier New,Courier,monospace;font-size:12px;background-color:#f9f9f9;border:1px solid #D0D0D0;color:#002166;display:block;margin:10px 0;padding:5px 10px 15px}code.debugcode.debug-last-query{display:none}pre.debugpre{display:block;padding:0;margin:0;color:#002166;font:12px/14px normal;font-family:\'Ubuntu Mono\',Consolas,Monaco,Courier New,Courier,monospace;background:0;border:0}div.debugcontent{margin:0 15px}p.debugp{margin:0;padding:0}.debugitalic{font-style:italic}.debutextR{text-align:right;margin-bottom:0;margin-top:0}.debugbold{font-weight:700}p.debugfooter{text-align:right;font-size:11px;border-top:1px solid #D0D0D0;line-height:32px;padding:0 10px;margin:20px 0 0}div.debugcontainer{margin:0px;border:1px solid #D0D0D0;-webkit-box-shadow:0 0 8px #D0D0D0}code.debug p{padding:0;margin:0;width:100%;text-align:right;font-weight:700;text-transform:uppercase;border-bottom:1px dotted #CCC;clear:right}code.debug span{float:left;font-style:italic;color:#CCC}.vayes-debug-badge{background:#285AA5;border:1px solid rgba(0,0,0,0);border-radius:4px;color:#FFF;padding:2px 4px}.vayes-debug-badge-object{background:#A53C89}.vayes-debug-badge-array{background:#037B5A}.vayes-debug-badge-string{background:#037B5A}.vayes-debug-badge-integer{background:#552EF3}.vayes-debug-badge-true{background:#126F0B}.vayes-debug-badge-false{background:#DE0303}.vayes-debug-badge-null{background:#383838}.vayes-debug-badge-float{background:#9E4E09}p.debugp.debugbold.debutextR.lq-trigger:hover + code{display:block}</style>';

                $output .= '<div class="debugbody"><div class="debugcontainer">';
                $output .= '<h1 class="debugheader">' . $arr_values[$i] . '</h1>';
                $output .= '<div class="debugcontent">';
                $output .= '<code class="debugcode"><p class="debugp debugbold debutextR">:: print_r</p><pre class="debugpre">' . $message;
                ob_start();
                print_r($value);
                $output .= "\n\n" . trim(ob_get_clean());
                $output .= '</pre></code>';

                if ($CI->db->last_query()) {
                    $output .= '<code class="debugcode debug-last-query"><p class="debugp debugbold debutextR">:: $CI->db->last_query()</p>';
                    $output .= $CI->db->last_query();
                    $output .= '</code>';
                }


                $output .= '</div><p class="debugfooter">Vayes Debug Helper © Yahya A. Erturan (melhoria por Taffarel Xavier)</p></div></div>';
                $output .= '<div style="clear:both;"></div>';

                if (PHP_SAPI == 'cli') {
                    echo $varname . ' = ' . PHP_EOL . $output . PHP_EOL . PHP_EOL;
                    return;
                }

                echo $output;
            }
        }

        if ($die) {
            exit;
        }
    }
}

// ------------------------------------------------------------------------

/**
 * v_echo()
 *
 * @param mixed $var
 * @param string $custom_style
 * @return void
 */
if (!function_exists('v_echo')) {

    function v_echo($var, $bgcolor = '#3377CC', $custom_style = '')
    {
        $style = 'font-family:\'Ubuntu Mono\';font-size:11pt;background:' . $bgcolor . ';color:#FFF;border-radius:5px;padding:3px 6px;min-width:100px; max-width: 600px;word-wrap: break-word;';
        if ($custom_style) {
            $style = $custom_style;
        }
        if ((is_array($var)) or (is_object($var))) {
            echo '<pre style="' . $style . 'font-size:10pt;line-height:11pt;">' . json_encode($var, JSON_PRETTY_PRINT) . '</pre>';
        } else {
            echo '<pre style="' . $style . '">' . $var . '</pre>';
        }
    }
}
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------


/**
 * Laravel Helpers File
 * Extracted by Anthony Rappa
 * rappa819@gmail.com
 */
if (!function_exists('append_config')) {

    /**
     * Assign high numeric IDs to a config item to force appending.
     *
     * @param  array $array
     * @return array
     */
    function append_config(array $array)
    {
        $start = 9999;

        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $start++;

                $array[$start] = array_pull($array, $key);
            }
        }

        return $array;
    }
}

if (!function_exists('array_add')) {

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    function array_add($array, $key, $value)
    {
        if (is_null(get($array, $key))) {
            set($array, $key, $value);
        }

        return $array;
    }
}

if (!function_exists('array_build')) {

    /**
     * Build a new array using a callback.
     *
     * @param  array $array
     * @param  \Closure $callback
     * @return array
     */
    function array_build($array, Closure $callback)
    {
        $results = array();

        foreach ($array as $key => $value) {
            list($innerKey, $innerValue) = call_user_func($callback, $key, $value);

            $results[$innerKey] = $innerValue;
        }

        return $results;
    }
}

if (!function_exists('array_divide')) {

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array $array
     * @return array
     */
    function array_divide($array)
    {
        return array(array_keys($array), array_values($array));
    }
}

if (!function_exists('array_dot')) {

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array $array
     * @param  string $prepend
     * @return array
     */
    function array_dot($array, $prepend = '')
    {
        $results = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}

if (!function_exists('array_except')) {

    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array $array
     * @param  array|string $keys
     * @return array
     */
    function array_except($array, $keys)
    {
        return array_diff_key($array, array_flip((array) $keys));
    }
}

if (!function_exists('array_fetch')) {

    /**
     * Fetch a flattened array of a nested array element.
     *
     * @param  array $array
     * @param  string $key
     * @return array
     */
    function array_fetch($array, $key)
    {
        $results = array();

        foreach (explode('.', $key) as $segment) {
            foreach ($array as $value) {
                if (array_key_exists($segment, $value = (array) $value)) {
                    $results[] = $value[$segment];
                }
            }

            $array = array_values($results);
        }

        return array_values($results);
    }
}

if (!function_exists('array_first')) {

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array $array
     * @param  \Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    function array_first($array, $callback, $default = null)
    {
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return value($default);
    }
}

if (!function_exists('array_last')) {

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array $array
     * @param  \Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    function array_last($array, $callback, $default = null)
    {
        return first(array_reverse($array), $callback, $default);
    }
}

if (!function_exists('array_flatten')) {

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array $array
     * @return array
     */
    function array_flatten($array)
    {
        $return = array();

        array_walk_recursive($array, function ($x) use (&$return) {
            $return[] = $x;
        });

        return $return;
    }
}

if (!function_exists('array_forget')) {

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array $array
     * @param  array|string $keys
     * @return void
     */
    function array_forget(&$array, $keys)
    {
        $original = &$array;

        foreach ((array) $keys as $key) {
            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                }
            }

            unset($array[array_shift($parts)]);

            // clean up after each pass
            $array = &$original;
        }
    }
}

if (!function_exists('array_get')) {

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('array_has')) {

    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     * @return bool
     */
    function array_has($array, $key)
    {
        if (empty($array) || is_null($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }
}

if (!function_exists('array_only')) {

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array $array
     * @param  array|string $keys
     * @return array
     */
    function array_only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}

if (!function_exists('array_pluck')) {

    /**
     * Pluck an array of values from an array.
     *
     * @param  array $array
     * @param  string $value
     * @param  string $key
     * @return array
     */
    function array_pluck($array, $value, $key = null)
    {
        $results = array();

        foreach ($array as $item) {
            $itemValue = data_get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = data_get($item, $key);

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }
}

if (!function_exists('array_pull')) {

    /**
     * Get a value from the array, and remove it.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function array_pull(&$array, $key, $default = null)
    {
        $value = get($array, $key, $default);

        forget($array, $key);

        return $value;
    }
}

if (!function_exists('array_set')) {

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (!function_exists('array_where')) {

    /**
     * Filter the array using the given Closure.
     *
     * @param  array $array
     * @param  \Closure $callback
     * @return array
     */
    function array_where($array, Closure $callback)
    {
        $filtered = array();

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }
}

if (!function_exists('camel_case')) {

    /**
     * Convert a value to camel case.
     *
     * @param  string $value
     * @return string
     */
    function camel_case($value)
    {
        static $camelCache = [];

        if (isset($camelCache[$value])) {
            return $camelCache[$value];
        }

        return $camelCache[$value] = lcfirst(studly($value));
    }
}

if (!function_exists('class_basename')) {

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object $class
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

if (!function_exists('class_uses_recursive')) {

    /**
     * Returns all traits used by a class, it's subclasses and trait of their traits
     *
     * @param  string $class
     * @return array
     */
    function class_uses_recursive($class)
    {
        $results = [];

        foreach (array_merge([$class => $class], class_parents($class)) as $class) {
            $results += trait_uses_recursive($class);
        }

        return array_unique($results);
    }
}

if (!function_exists('data_get')) {

    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed $target
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($target)) {
                if (!array_key_exists($segment, $target)) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif ($target instanceof ArrayAccess) {
                if (!isset($target[$segment])) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif (is_object($target)) {
                if (!isset($target->{$segment})) {
                    return value($default);
                }

                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (!function_exists('e')) {

    /**
     * Escape HTML entities in a string.
     *
     * @param  string $value
     * @return string
     */
    function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('ends_with')) {

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    function ends_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('head')) {

    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param  array $array
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}

if (!function_exists('last')) {

    /**
     * Get the last element from an array.
     *
     * @param  array $array
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if (!function_exists('object_get')) {

    /**
     * Get an item from an object using "dot" notation.
     *
     * @param  object $object
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function object_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') {
            return $object;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_object($object) || !isset($object->{$segment})) {
                return value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}

if (!function_exists('preg_replace_sub')) {

    /**
     * Replace a given pattern with each value in the array in sequentially.
     *
     * @param  string $pattern
     * @param  array $replacements
     * @param  string $subject
     * @return string
     */
    function preg_replace_sub($pattern, &$replacements, $subject)
    {
        return preg_replace_callback($pattern, function () use (&$replacements) {
            return array_shift($replacements);
        }, $subject);
    }
}

if (!function_exists('snake_case')) {

    /**
     * Convert a string to snake case.
     *
     * @param  string $value
     * @param  string $delimiter
     * @return string
     */
    function snake_case($value, $delimiter = '_')
    {
        static $snakeCache = [];
        $key = $value . $delimiter;

        if (isset($snakeCache[$key])) {
            return $snakeCache[$key];
        }

        if (!ctype_lower($value)) {
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/', '$1' . $delimiter, $value));
        }

        return $snakeCache[$key] = $value;
    }
}

if (!function_exists('starts_with')) {

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    function starts_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('str_contains')) {

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    function str_contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('str_finish')) {

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param  string $value
     * @param  string $cap
     * @return string
     */
    function str_finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:' . $quoted . ')+$/', '', $value) . $cap;
    }
}

if (!function_exists('str_is')) {

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string $pattern
     * @param  string $value
     * @return bool
     */
    function str_is($pattern, $value)
    {
        if ($pattern == $value) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern) . '\z';

        return (bool) preg_match('#^' . $pattern . '#', $value);
    }
}

if (!function_exists('str_limit')) {

    /**
     * Limit the number of characters in a string.
     *
     * @param  string $value
     * @param  int $limit
     * @param  string $end
     * @return string
     */
    function str_limit($value, $limit = 100, $end = '...')
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')) . $end;
    }
}

if (!function_exists('str_random')) {

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int $length
     * @return string
     *
     * @throws \RuntimeException
     */
    function str_random($length = 16)
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new RuntimeException('OpenSSL extension is required.');
        }

        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false) {
            throw new RuntimeException('Unable to generate random string.');
        }

        return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
    }
}

if (!function_exists('str_replace_array')) {

    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string $search
     * @param  array $replace
     * @param  string $subject
     * @return string
     */
    function str_replace_array($search, array $replace, $subject)
    {
        foreach ($replace as $value) {
            $subject = preg_replace('/' . $search . '/', $value, $subject, 1);
        }

        return $subject;
    }
}

if (!function_exists('str_slug')) {

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string $title
     * @param  string $separator
     * @return string
     */
    function str_slug($title, $separator = '-')
    {
        $title = ascii($title);

        // Convert all dashes/underscores into separator
        $flip = $separator == '-' ? '_' : '-';

        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}

if (!function_exists('ascii')) {

    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param  string $value
     * @return string
     */
    function ascii($value)
    {
        foreach (charsArray() as $key => $val) {
            $value = str_replace($val, $key, $value);
        }

        return preg_replace('/[^\x20-\x7E]/u', '', $value);
    }
}

if (!function_exists('charsArray')) {

    /**
     * Returns the replacements for the ascii method.
     *
     * Note: Adapted from Stringy\Stringy.
     *
     * @see https://github.com/danielstjules/Stringy/blob/2.3.1/LICENSE.txt
     *
     * @return array
     */
    function charsArray()
    {
        static $charsArray;

        if (isset($charsArray)) {
            return $charsArray;
        }

        return $charsArray = [
            '0' => ['°', '₀', '۰'],
            '1' => ['¹', '₁', '۱'],
            '2' => ['²', '₂', '۲'],
            '3' => ['³', '₃', '۳'],
            '4' => ['⁴', '₄', '۴', '٤'],
            '5' => ['⁵', '₅', '۵', '٥'],
            '6' => ['⁶', '₆', '۶', '٦'],
            '7' => ['⁷', '₇', '۷'],
            '8' => ['⁸', '₈', '۸'],
            '9' => ['⁹', '₉', '۹'],
            'a' => [
                'à',
                'á',
                'ả',
                'ã',
                'ạ',
                'ă',
                'ắ',
                'ằ',
                'ẳ',
                'ẵ',
                'ặ',
                'â',
                'ấ',
                'ầ',
                'ẩ',
                'ẫ',
                'ậ',
                'ā',
                'ą',
                'å',
                'α',
                'ά',
                'ἀ',
                'ἁ',
                'ἂ',
                'ἃ',
                'ἄ',
                'ἅ',
                'ἆ',
                'ἇ',
                'ᾀ',
                'ᾁ',
                'ᾂ',
                'ᾃ',
                'ᾄ',
                'ᾅ',
                'ᾆ',
                'ᾇ',
                'ὰ',
                'ά',
                'ᾰ',
                'ᾱ',
                'ᾲ',
                'ᾳ',
                'ᾴ',
                'ᾶ',
                'ᾷ',
                'а',
                'أ',
                'အ',
                'ာ',
                'ါ',
                'ǻ',
                'ǎ',
                'ª',
                'ა',
                'अ',
                'ا'
            ],
            'b' => ['б', 'β', 'Ъ', 'Ь', 'ب', 'ဗ', 'ბ'],
            'c' => ['ç', 'ć', 'č', 'ĉ', 'ċ'],
            'd' => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ', 'д', 'δ', 'د', 'ض', 'ဍ', 'ဒ', 'დ'],
            'e' => [
                'é',
                'è',
                'ẻ',
                'ẽ',
                'ẹ',
                'ê',
                'ế',
                'ề',
                'ể',
                'ễ',
                'ệ',
                'ë',
                'ē',
                'ę',
                'ě',
                'ĕ',
                'ė',
                'ε',
                'έ',
                'ἐ',
                'ἑ',
                'ἒ',
                'ἓ',
                'ἔ',
                'ἕ',
                'ὲ',
                'έ',
                'е',
                'ё',
                'э',
                'є',
                'ə',
                'ဧ',
                'ေ',
                'ဲ',
                'ე',
                'ए',
                'إ',
                'ئ'
            ],
            'f' => ['ф', 'φ', 'ف', 'ƒ', 'ფ'],
            'g' => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ဂ', 'გ', 'گ'],
            'h' => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه', 'ဟ', 'ှ', 'ჰ'],
            'i' => [
                'í',
                'ì',
                'ỉ',
                'ĩ',
                'ị',
                'î',
                'ï',
                'ī',
                'ĭ',
                'į',
                'ı',
                'ι',
                'ί',
                'ϊ',
                'ΐ',
                'ἰ',
                'ἱ',
                'ἲ',
                'ἳ',
                'ἴ',
                'ἵ',
                'ἶ',
                'ἷ',
                'ὶ',
                'ί',
                'ῐ',
                'ῑ',
                'ῒ',
                'ΐ',
                'ῖ',
                'ῗ',
                'і',
                'ї',
                'и',
                'ဣ',
                'ိ',
                'ီ',
                'ည်',
                'ǐ',
                'ი',
                'इ'
            ],
            'j' => ['ĵ', 'ј', 'Ј', 'ჯ', 'ج'],
            'k' => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك', 'က', 'კ', 'ქ', 'ک'],
            'l' => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل', 'လ', 'ლ'],
            'm' => ['м', 'μ', 'م', 'မ', 'მ'],
            'n' => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن', 'န', 'ნ'],
            'o' => [
                'ó',
                'ò',
                'ỏ',
                'õ',
                'ọ',
                'ô',
                'ố',
                'ồ',
                'ổ',
                'ỗ',
                'ộ',
                'ơ',
                'ớ',
                'ờ',
                'ở',
                'ỡ',
                'ợ',
                'ø',
                'ō',
                'ő',
                'ŏ',
                'ο',
                'ὀ',
                'ὁ',
                'ὂ',
                'ὃ',
                'ὄ',
                'ὅ',
                'ὸ',
                'ό',
                'о',
                'و',
                'θ',
                'ို',
                'ǒ',
                'ǿ',
                'º',
                'ო',
                'ओ'
            ],
            'p' => ['п', 'π', 'ပ', 'პ', 'پ'],
            'q' => ['ყ'],
            'r' => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر', 'რ'],
            's' => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص', 'စ', 'ſ', 'ს'],
            't' => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط', 'ဋ', 'တ', 'ŧ', 'თ', 'ტ'],
            'u' => [
                'ú',
                'ù',
                'ủ',
                'ũ',
                'ụ',
                'ư',
                'ứ',
                'ừ',
                'ử',
                'ữ',
                'ự',
                'û',
                'ū',
                'ů',
                'ű',
                'ŭ',
                'ų',
                'µ',
                'у',
                'ဉ',
                'ု',
                'ူ',
                'ǔ',
                'ǖ',
                'ǘ',
                'ǚ',
                'ǜ',
                'უ',
                'उ'
            ],
            'v' => ['в', 'ვ', 'ϐ'],
            'w' => ['ŵ', 'ω', 'ώ', 'ဝ', 'ွ'],
            'x' => ['χ', 'ξ'],
            'y' => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ', 'ϋ', 'ύ', 'ΰ', 'ي', 'ယ'],
            'z' => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز', 'ဇ', 'ზ'],
            'aa' => ['ع', 'आ', 'آ'],
            'ae' => ['ä', 'æ', 'ǽ'],
            'ai' => ['ऐ'],
            'at' => ['@'],
            'ch' => ['ч', 'ჩ', 'ჭ', 'چ'],
            'dj' => ['ђ', 'đ'],
            'dz' => ['џ', 'ძ'],
            'ei' => ['ऍ'],
            'gh' => ['غ', 'ღ'],
            'ii' => ['ई'],
            'ij' => ['ĳ'],
            'kh' => ['х', 'خ', 'ხ'],
            'lj' => ['љ'],
            'nj' => ['њ'],
            'oe' => ['ö', 'œ', 'ؤ'],
            'oi' => ['ऑ'],
            'oii' => ['ऒ'],
            'ps' => ['ψ'],
            'sh' => ['ш', 'შ', 'ش'],
            'shch' => ['щ'],
            'ss' => ['ß'],
            'sx' => ['ŝ'],
            'th' => ['þ', 'ϑ', 'ث', 'ذ', 'ظ'],
            'ts' => ['ц', 'ც', 'წ'],
            'ue' => ['ü'],
            'uu' => ['ऊ'],
            'ya' => ['я'],
            'yu' => ['ю'],
            'zh' => ['ж', 'ჟ', 'ژ'],
            '(c)' => ['©'],
            'A' => [
                'Á',
                'À',
                'Ả',
                'Ã',
                'Ạ',
                'Ă',
                'Ắ',
                'Ằ',
                'Ẳ',
                'Ẵ',
                'Ặ',
                'Â',
                'Ấ',
                'Ầ',
                'Ẩ',
                'Ẫ',
                'Ậ',
                'Å',
                'Ā',
                'Ą',
                'Α',
                'Ά',
                'Ἀ',
                'Ἁ',
                'Ἂ',
                'Ἃ',
                'Ἄ',
                'Ἅ',
                'Ἆ',
                'Ἇ',
                'ᾈ',
                'ᾉ',
                'ᾊ',
                'ᾋ',
                'ᾌ',
                'ᾍ',
                'ᾎ',
                'ᾏ',
                'Ᾰ',
                'Ᾱ',
                'Ὰ',
                'Ά',
                'ᾼ',
                'А',
                'Ǻ',
                'Ǎ'
            ],
            'B' => ['Б', 'Β', 'ब'],
            'C' => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ'],
            'D' => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
            'E' => [
                'É',
                'È',
                'Ẻ',
                'Ẽ',
                'Ẹ',
                'Ê',
                'Ế',
                'Ề',
                'Ể',
                'Ễ',
                'Ệ',
                'Ë',
                'Ē',
                'Ę',
                'Ě',
                'Ĕ',
                'Ė',
                'Ε',
                'Έ',
                'Ἐ',
                'Ἑ',
                'Ἒ',
                'Ἓ',
                'Ἔ',
                'Ἕ',
                'Έ',
                'Ὲ',
                'Е',
                'Ё',
                'Э',
                'Є',
                'Ə'
            ],
            'F' => ['Ф', 'Φ'],
            'G' => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'],
            'H' => ['Η', 'Ή', 'Ħ'],
            'I' => [
                'Í',
                'Ì',
                'Ỉ',
                'Ĩ',
                'Ị',
                'Î',
                'Ï',
                'Ī',
                'Ĭ',
                'Į',
                'İ',
                'Ι',
                'Ί',
                'Ϊ',
                'Ἰ',
                'Ἱ',
                'Ἳ',
                'Ἴ',
                'Ἵ',
                'Ἶ',
                'Ἷ',
                'Ῐ',
                'Ῑ',
                'Ὶ',
                'Ί',
                'И',
                'І',
                'Ї',
                'Ǐ',
                'ϒ'
            ],
            'K' => ['К', 'Κ'],
            'L' => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ', 'Ľ', 'Ŀ', 'ल'],
            'M' => ['М', 'Μ'],
            'N' => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'],
            'O' => [
                'Ó',
                'Ò',
                'Ỏ',
                'Õ',
                'Ọ',
                'Ô',
                'Ố',
                'Ồ',
                'Ổ',
                'Ỗ',
                'Ộ',
                'Ơ',
                'Ớ',
                'Ờ',
                'Ở',
                'Ỡ',
                'Ợ',
                'Ø',
                'Ō',
                'Ő',
                'Ŏ',
                'Ο',
                'Ό',
                'Ὀ',
                'Ὁ',
                'Ὂ',
                'Ὃ',
                'Ὄ',
                'Ὅ',
                'Ὸ',
                'Ό',
                'О',
                'Θ',
                'Ө',
                'Ǒ',
                'Ǿ'
            ],
            'P' => ['П', 'Π'],
            'R' => ['Ř', 'Ŕ', 'Р', 'Ρ', 'Ŗ'],
            'S' => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
            'T' => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'],
            'U' => [
                'Ú',
                'Ù',
                'Ủ',
                'Ũ',
                'Ụ',
                'Ư',
                'Ứ',
                'Ừ',
                'Ử',
                'Ữ',
                'Ự',
                'Û',
                'Ū',
                'Ů',
                'Ű',
                'Ŭ',
                'Ų',
                'У',
                'Ǔ',
                'Ǖ',
                'Ǘ',
                'Ǚ',
                'Ǜ'
            ],
            'V' => ['В'],
            'W' => ['Ω', 'Ώ', 'Ŵ'],
            'X' => ['Χ', 'Ξ'],
            'Y' => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ', 'Ы', 'Й', 'Υ', 'Ϋ', 'Ŷ'],
            'Z' => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'],
            'AE' => ['Ä', 'Æ', 'Ǽ'],
            'CH' => ['Ч'],
            'DJ' => ['Ђ'],
            'DZ' => ['Џ'],
            'GX' => ['Ĝ'],
            'HX' => ['Ĥ'],
            'IJ' => ['Ĳ'],
            'JX' => ['Ĵ'],
            'KH' => ['Х'],
            'LJ' => ['Љ'],
            'NJ' => ['Њ'],
            'OE' => ['Ö', 'Œ'],
            'PS' => ['Ψ'],
            'SH' => ['Ш'],
            'SHCH' => ['Щ'],
            'SS' => ['ẞ'],
            'TH' => ['Þ'],
            'TS' => ['Ц'],
            'UE' => ['Ü'],
            'YA' => ['Я'],
            'YU' => ['Ю'],
            'ZH' => ['Ж'],
            ' ' => [
                "\xC2\xA0",
                "\xE2\x80\x80",
                "\xE2\x80\x81",
                "\xE2\x80\x82",
                "\xE2\x80\x83",
                "\xE2\x80\x84",
                "\xE2\x80\x85",
                "\xE2\x80\x86",
                "\xE2\x80\x87",
                "\xE2\x80\x88",
                "\xE2\x80\x89",
                "\xE2\x80\x8A",
                "\xE2\x80\xAF",
                "\xE2\x81\x9F",
                "\xE3\x80\x80"
            ],
        ];
    }
}

if (!function_exists('studly_case')) {

    /**
     * Convert a value to studly caps case.
     *
     * @param  string $value
     * @return string
     */
    function studly_case($value)
    {
        static $studlyCache = [];
        $key = $value;

        if (isset($studlyCache[$key])) {
            return $studlyCache[$key];
        }

        $value = ucwords(str_replace(array('-', '_'), ' ', $value));

        return $studlyCache[$key] = str_replace(' ', '', $value);
    }
}

if (!function_exists('title_case')) {

    /**
     * Convert a value to title case.
     *
     * @param  string $value
     * @return string
     */
    function title_case($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
}

if (!function_exists('trait_uses_recursive')) {

    /**
     * Returns all traits used by a trait and its traits
     *
     * @param  string $trait
     * @return array
     */
    function trait_uses_recursive($trait)
    {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}

if (!function_exists('value')) {

    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('with')) {

    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed $object
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }
}

/**
 * Helper functions for the helper functions, that can still be used standalone
 */
if (!function_exists('studly')) {

    /**
     * Convert a value to studly caps case.
     *
     * @param  string $value
     * @return string
     */
    function studly($value)
    {
        static $studlyCache = [];
        $key = $value;

        if (isset($studlyCache[$key])) {
            return $studlyCache[$key];
        }

        $value = ucwords(str_replace(array('-', '_'), ' ', $value));

        return $studlyCache[$key] = str_replace(' ', '', $value);
    }
}

if (!function_exists('get')) {

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('set')) {

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (!function_exists('dot')) {

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array $array
     * @param  string $prepend
     * @return array
     */
    function dot($array, $prepend = '')
    {
        $results = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}

if (!function_exists('first')) {

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array $array
     * @param  \Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    function first($array, $callback, $default = null)
    {
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return value($default);
    }
}

if (!function_exists('forget')) {

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array $array
     * @param  array|string $keys
     * @return void
     */
    function forget(&$array, $keys)
    {
        $original = &$array;

        foreach ((array) $keys as $key) {
            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                }
            }

            unset($array[array_shift($parts)]);

            // clean up after each pass
            $array = &$original;
        }
    }
}

if (!function_exists('bcrypt')) {

    /**
     * Password hash the given value.
     *
     * @param  string $value
     * @param  array $options
     * @return string
     *
     * @throws \RuntimeException
     */
    function bcrypt($value, $options = [])
    {
        $cost = isset($options['rounds']) ? $options['rounds'] : 10;

        $hashedValue = password_hash($value, PASSWORD_BCRYPT, ['cost' => $cost]);

        if ($hashedValue === false) {
            throw new RuntimeException('Bcrypt hashing not supported.');
        }

        return $hashedValue;
    }
}

if (!function_exists('tap')) {

    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param  mixed $value
     * @param  callable $callback
     * @return mixed
     */
    function tap($value, $callback)
    {
        $callback($value);

        return $value;
    }
}

if (!function_exists('dd')) {

    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($x) {
            var_dump($x);
        }, func_get_args());

        die(1);
    }
}

if (!function_exists('data_fill')) {

    /**
     * Fill in data where it's missing.
     *
     * @param  mixed $target
     * @param  string|array $key
     * @param  mixed $value
     * @return mixed
     */
    function data_fill(&$target, $key, $value)
    {
        return data_set($target, $key, $value, false);
    }
}

if (!function_exists('data_set')) {

    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    function data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);
        if (($segment = array_shift($segments)) === '*') {
            if (!accessible($target)) {
                $target = [];
            }
            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (accessible($target)) {
            if ($segments) {
                if (!exists($target, $segment)) {
                    $target[$segment] = [];
                }
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }
                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];
            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }
        return $target;
    }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
     * @return bool
     */
    function exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }
}

if (!function_exists('increse_field_by_1')) {

    function increse_field_by_1($table_name, $fieldToIncrease, $whereCondition)
    {
        $CI = &get_instance();
        $CI->db->where($whereCondition);
        $CI->db->set($fieldToIncrease, $fieldToIncrease . "+1", FALSE);
        $CI->db->update($table_name);
    }
}

if (!function_exists('decrease_field_by_1')) {

    function decrease_field_by_1($table_name, $fieldToIncrease, $whileCondition)
    {
        $CI = &get_instance();
        $CI->db->where($whileCondition);
        $CI->db->set($fieldToIncrease, $fieldToIncrease . "-1", FALSE);
        $CI->db->update($table_name);
    }
}

if (!function_exists('invoice_exists')) {

    function invoice_exists($id, $hash = null)
    {
        $CI = &get_instance();

        $CI->load->model("invoices_model");

        $where = [];

        if ($hash) {
            $where = ['hash' => $hash];
        }
        $invoice = $CI->invoices_model->get($id, $where);
        return $invoice ? true : false;
    }
}

if (!function_exists("get_media_path_banco_inter_invoices")) {

    /**
     *
     * @return string
     */
    function get_media_path_banco_inter_invoices()
    {
        $ci = &get_instance();
        return FCPATH . $ci->app->get_media_folder();
    }
}

if (!function_exists('formatDescription')) {

    function formatDescription($items): array
    {

        $descs = [];

        foreach ($items as $item) {
            $descs[] = substr(strip_tags($item['description']), 0, 78);
        }

        return $descs;
    }
}


if (!function_exists('banco_inter_emitir_cobranca')) {

    /**
     * @param mixed $invoice
     * @param string $cobranca_tipo
     *
     * @return [type]
     */
    function connect_inter_emitir_cobranca($invoice, $cobranca_tipo = 'criacao')
    {
        $CI = &get_instance();

        if (!$invoice) return;

        $CI->load->model("invoices_model");

        $CI->load->library("connect_inter/banco_inter_v3_library");

        $CI        = &get_instance();

        $id        = $invoice->id;

        $hash      = $invoice->hash;

        $vat = '' . soNumeros($invoice->client->vat);

        $tipo = strlen($vat) == 11 ? 'FISICA' : 'JURIDICA';

        $logradouro = preg_replace("/\s{2}/", " ", str_replace([':', ',', '-', '–'], '', $invoice->client->address));

        $logradouro = mb_strtoupper(ascii($logradouro));

        $logradouro = substr($logradouro, 0, 50);

        $client = $invoice->client;

        $messages = [];

        if (get_option('paymentmethod_connect_inter_b_inter_mostrar_linhas') === '1') {
            $desc = formatDescription($invoice->items);

            if (isset($desc[0])) {
                $messages['linha1'] = $desc[0];
            }
            if (isset($desc[1])) {
                $messages[] = $desc[1];
            }
            if (isset($desc[2])) {
                $messages[] = $desc[2];
            }
            if (isset($desc[3])) {
                $messages[] = $desc[3];
            }
            if (isset($desc[4])) {
                $messages[] = $desc[4];
            }
        }

        $data = [
            "seuNumero"      => sales_number_format($invoice->number, 1, $invoice->prefix, $invoice->date),
            "valorNominal"   => $invoice->total_left_to_pay,
            "dataVencimento" => $invoice->duedate,
            "numDiasAgenda" => 5,
            "pagador" => [
                "cpfCnpj"    => $vat,
                "tipoPessoa" => $tipo,
                "nome"       => $client->company,
                "endereco"   => $logradouro,
                "cidade"     => $client->city,
                "uf"         => $client->state,
                "cep"        => soNumeros($client->zip)
            ],
            "multa"    => [
                "taxa"  => sprintf('%.2f', get_option('paymentmethod_connect_inter_b_inter_multa')),
                "codigo" => get_option('paymentmethod_connect_inter_multa_modalidade') // 'PERCENTUAL',
            ],
            "juros"    => [
                "taxa" => sprintf('%.2f', get_option('paymentmethod_connect_inter_b_inter_juros')),
                "codigo"  => get_option('paymentmethod_connect_inter_juros_modalidade') // 'TAXAMENSAL',
            ],
            'mensagem' => $messages
        ];

        log_activity('[BANCO INTER V3] - PAYLOAD ENVIADO: ' . json_encode($data));

        try {
            if ($cobranca_tipo == 'criacao') {

                $response = $CI->banco_inter_v3_library->emitirCobranca($data, $invoice);

                $response = json_decode($response);

                if (isset($response->codigoSolicitacao)) {

                    $codigoSolicitacao = $response->codigoSolicitacao;

                    $response = $CI->banco_inter_v3_library->getCobranca($codigoSolicitacao);

                    $response->codigoSolicitacao = $codigoSolicitacao;

                    $CI->db->where('id', $invoice->id)->update(
                        db_prefix() . 'invoices',
                        [
                            'banco_inter_codigo_solicitacao' => $codigoSolicitacao,
                            'banco_inter_dados_cobranca'     => json_encode($response)
                        ]
                    );

                    $log_description = '[BANCO INTER V3] - ' . _l('banco_inter_cobranca_emitida_sucesso', $codigoSolicitacao);

                    $CI->invoices_model->log_invoice_activity($invoice->id, $log_description);

                    $pix = $response->pix;

                    $directory = get_media_path_banco_inter_invoices();

                    if (is_object($pix) && isset($pix->pixCopiaECola)) {
                        $hash              = $invoice->hash;
                        // TODO
                        // $cora_image_qrcode = $directory . "/banco_inter/invoices/invoice_{$id}_{$hash}_qrcode.png";
                        // QRCode::png($pix->pixCopiaECola,  $cora_image_qrcode);
                    }

                    return $pix;
                }
            }
        } catch (\Exception $th) {
            $CI->invoices_model->log_invoice_activity($invoice->id, '[BANCO INTER V3] - Error | Message: ' . $th->getMessage());
        }
    }
}


if (!function_exists('mapShippingColumns')) {

    function mapShippingColumns($data, $expense = false)
    {
        $shipping_fields = [
            'shipping_street',
            'shipping_city',
            'shipping_city',
            'shipping_state',
            'shipping_zip',
            'shipping_country',
        ];

        if (!isset($data['include_shipping'])) {
            foreach ($shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = null;
                }
            }
            $data['show_shipping_on_invoice'] = 1;
            $data['include_shipping']         = 0;
        } else {
            // We dont need to overwrite to 1 unless its coming from the main function add
            if (!DEFINED('CRON') && $expense == false) {
                $data['include_shipping'] = 1;
                // set by default for the next time to be checked
                if (isset($data['show_shipping_on_invoice']) && ($data['show_shipping_on_invoice'] == 1 || $data['show_shipping_on_invoice'] == 'on')) {
                    $data['show_shipping_on_invoice'] = 1;
                } else {
                    $data['show_shipping_on_invoice'] = 0;
                }
            }
            // else its just like they are passed
        }

        return $data;
    }
}


if (!function_exists('connectInterProcessUpload')) {

    // Função para validar e mover o arquivo
    function connectInterProcessUpload($file, $allowedExtensions, $uploadDir, $hash)
    {

        $fileTmpPath   = $file['tmp_name'];

        $fileName      = $file['name'];

        $fileNameCmps  = pathinfo($fileName);

        $fileExtension = strtolower($fileNameCmps['extension']);

        // Valida a extensão do arquivo
        if (in_array($fileExtension, $allowedExtensions)) {

            $newFileName = $hash . '.' . $fileExtension;

            if ($fileExtension == 'crt') {
                $newFileName = 'crt_' . $newFileName;
            }
            if ($fileExtension == 'key') {
                $newFileName = 'key_' . $newFileName;
            }

            // Caminho completo para o arquivo
            $dest_path = $uploadDir . $newFileName;

            // Move o arquivo para o diretório de destino
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                return ['status' => true, 'message' => "Arquivo $fileName enviado com sucesso.", 'file_path' => $dest_path];
            } else {
                return ['status' => false, 'message' => "Erro ao mover o arquivo $fileName para o destino."];
            }
        } else {
            return ['status' => false, 'message' => "Tipo de arquivo não permitido. Somente arquivos ." . implode(', .', $allowedExtensions) . " são aceitos."];
        }
    }
}


if (!function_exists('connect_inter_certs_exists')) {

    function connect_inter_certs_exists()
    {
        $directory = CONNECT_INTER_MODULE_NAME_UPLOADS_FOLDER . '/ssl_files/';

        // Verifica se o diretório existe
        if (is_dir($directory)) {
            // Obtém todos os arquivos e diretórios
            $files = scandir($directory);

            // Remove '.' e '..' da lista (são referências ao diretório atual e ao anterior)
            $files = array_diff($files, array('.', '..'));

            // Exibe a lista de arquivos
            if (!empty($files)) {
                $crt_exists = false;
                $key_exists = false;
                $start_with_crt = false;
                $start_with_key = false;

                foreach ($files as $file) {
                    $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                    // Verifica se é um arquivo .crt ou .key
                    if ($fileExtension === 'crt' || $fileExtension === 'key') {
                        // Verifica se o arquivo começa com o prefixo correto
                        if (strpos($file, 'crt_') === 0) {
                            $crt_exists = true;
                            $start_with_crt = true;
                        } elseif (strpos($file, 'key_') === 0) {
                            $key_exists = true;
                            $start_with_key = true;
                        }
                    }
                }

                // check
                if ($crt_exists && $key_exists && $start_with_crt && $start_with_key) {
                    echo '<div class="alert alert-success">' . _l('connect_inter_settings_certs_exists') . '</div>';
                } else {
                    echo '<div class="alert alert-danger">' . _l('connect_inter_settings_certs_not_exists') . ' <a href="' .
                        admin_url('connect_inter/v3/settings') . '">Configurar</a></div>';
                }
            } else {
                echo '<div class="alert alert-danger">' . _l('connect_inter_settings_certs_not_exists') . ' <a href="' .
                    admin_url('connect_inter/v3/settings') . '">Configurar</a></div>';
            }
        } else {
            echo '<div class="alert alert-danger">' . _l('connect_inter_dir_not_exists', $directory) .
                '  <a href="' . admin_url('connect_inter/v3/settings') . '">Configurar</a></div>';
        }
    }
}

if (!function_exists('bancoInterAddFatura')) {

    function bancoInterAddFatura($data, $expense = false)
    {
        $CI = &get_instance();

        $CI->load->model('invoices_model');

        $data['prefix'] = get_option('invoice_prefix');

        $data['number_format'] = get_option('invoice_number_format');

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = !DEFINED('CRON') ? get_staff_user_id() : 0;

        $data['cancel_overdue_reminders'] = isset($data['cancel_overdue_reminders']) ? 1 : 0;

        $data['allowed_payment_modes'] = isset($data['allowed_payment_modes']) ? serialize($data['allowed_payment_modes']) : serialize([]);

        $billed_tasks = isset($data['billed_tasks']) ? array_map('unserialize', array_unique(array_map('serialize', $data['billed_tasks']))) : [];

        $billed_expenses = isset($data['billed_expenses']) ? array_map('unserialize', array_unique(array_map('serialize', $data['billed_expenses']))) : [];

        $tags = isset($data['tags']) ? $data['tags'] : '';

        if (isset($data['save_as_draft'])) {
            $data['status'] = $CI->invoices_model::STATUS_DRAFT;
            unset($data['save_as_draft']);
        } elseif (isset($data['save_and_send_later'])) {
            $data['status'] = $CI->invoices_model::STATUS_DRAFT;
            unset($data['save_and_send_later']);
        }

        if (isset($data['recurring'])) {
            if ($data['recurring'] == 'custom') {
                $data['recurring_type']   = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
                $data['recurring']        = $data['repeat_every_custom'];
            }
        } else {
            $data['custom_recurring'] = 0;
            $data['recurring']        = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['hash'] = app_generate_hash();

        $items = [];

        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }

        $data = mapShippingColumns($data, $expense);

        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        if (isset($data['status']) && $data['status'] == $CI->invoices_model::STATUS_DRAFT) {
            $data['number'] = $CI->invoices_model::STATUS_DRAFT_NUMBER;
        }

        $data['duedate'] = isset($data['duedate']) && empty($data['duedate']) ? null : $data['duedate'];

        $hook = hooks()->apply_filters('before_invoice_added', [
            'data'  => $data,
            'items' => $items,
        ]);

        $data  = $hook['data'];
        $items = $hook['items'];

        $CI->db->insert(db_prefix() . 'invoices', $data);
        $insert_id = $CI->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            handle_tags_save($tags, $insert_id, 'invoice');

            foreach ($billed_tasks as $key => $tasks) {
                foreach ($tasks as $t) {
                    $CI->db->select('status')->where('id', $t);

                    $_task = $CI->db->get(db_prefix() . 'tasks')->row();

                    $taskUpdateData = [
                        'billed'     => 1,
                        'invoice_id' => $insert_id,
                    ];

                    if ($_task->status != Tasks_model::STATUS_COMPLETE) {
                        $taskUpdateData['status']       = Tasks_model::STATUS_COMPLETE;
                        $taskUpdateData['datefinished'] = date('Y-m-d H:i:s');
                    }

                    $CI->db->where('id', $t);
                    $CI->db->update(db_prefix() . 'tasks', $taskUpdateData);
                }
            }

            foreach ($billed_expenses as $key => $val) {
                foreach ($val as $expense_id) {
                    $CI->db->where('id', $expense_id);
                    $CI->db->update(db_prefix() . 'expenses', [
                        'invoiceid' => $insert_id,
                    ]);
                }
            }

            update_invoice_status($insert_id);

            // Update next invoice number in settings if status is not draft
            if (!$CI->invoices_model->is_draft($insert_id)) {
                $CI->invoices_model->increment_next_number();
            }

            foreach ($items as $key => $item) {
                if ($itemid = add_new_sales_item_post($item, $insert_id, 'invoice')) {
                    if (isset($billed_tasks[$key])) {
                        foreach ($billed_tasks[$key] as $_task_id) {
                            $CI->db->insert(db_prefix() . 'related_items', [
                                'item_id'  => $itemid,
                                'rel_id'   => $_task_id,
                                'rel_type' => 'task',
                            ]);
                        }
                    } elseif (isset($billed_expenses[$key])) {
                        foreach ($billed_expenses[$key] as $_expense_id) {
                            $CI->db->insert(db_prefix() . 'related_items', [
                                'item_id'  => $itemid,
                                'rel_id'   => $_expense_id,
                                'rel_type' => 'expense',
                            ]);
                        }
                    }
                    _maybe_insert_post_item_tax($itemid, $item, $insert_id, 'invoice');
                }
            }

            update_sales_total_tax_column($insert_id, 'invoice', db_prefix() . 'invoices');

            if (!DEFINED('CRON') && $expense == false) {
                $lang_key = 'invoice_activity_created';
            } elseif (!DEFINED('CRON') && $expense == true) {
                $lang_key = 'invoice_activity_from_expense';
            } elseif (DEFINED('CRON') && $expense == false) {
                $lang_key = 'invoice_activity_recurring_created';
            } else {
                $lang_key = 'invoice_activity_recurring_from_expense_created';
            }

            $CI->invoices_model->log_invoice_activity($insert_id, $lang_key);

            hooks()->do_action('after_invoice_added', $insert_id);

            return $insert_id;
        }

        return false;
    }
}

if (!function_exists('bancoInterPrepararFatura')) {

    function bancoInterPrepararFatura($invoice_id)
    {
        $CI = &get_instance();

        $new_recurring_invoice_action = get_option('new_recurring_invoice_action');

        $CI->load->model('invoices_model');

        $_invoice                             = $CI->invoices_model->get($invoice_id);

        $qtd_faturas_para_gerar                 = get_custom_field_value($invoice_id, 'invoice_quantidade_de_parcelas_para_gerar', 'invoice');

        if ($qtd_faturas_para_gerar && $qtd_faturas_para_gerar > 1) {

            $qtd_para_gerar = $qtd_faturas_para_gerar;

            $data_inicial   = $_invoice->duedate;

            $data = new \DateTime($data_inicial);

            $total = $_invoice->total;

            $parcela_valor = $total / $qtd_para_gerar;

            $CI->db->where('id', $invoice_id)
                ->update(db_prefix() . 'invoices', ['subtotal' => $parcela_valor, 'total' => $parcela_valor]);

            $tags      = get_tags_in($invoice_id, 'invoice');
            $prefix    = get_option('invoice_prefix');
            $tags[]    = $prefix . 'MÃE';

            handle_tags_save($tags, $invoice_id, 'invoice');
            $invoice_number = format_invoice_number($invoice_id);

            for ($i = 1; $i <= $qtd_para_gerar - 1; $i++) {
                $data->modify('+1 month');
                $last_duedate = $data->format('Y-m-d');
                $new_invoice_data                     = [];
                $new_invoice_data['tags']             = [$prefix . 'FILHA-' . $invoice_number]; // TODO
                $new_invoice_data['clientid']         = $_invoice->clientid;
                $new_invoice_data['number']           = get_option('next_invoice_number');
                $new_invoice_data['date']             = $_invoice->date;
                $new_invoice_data['duedate']          = $last_duedate;

                $new_invoice_data['project_id']       = $_invoice->project_id;
                $new_invoice_data['show_quantity_as'] = $_invoice->show_quantity_as;
                $new_invoice_data['currency']         = $_invoice->currency;
                $new_invoice_data['subtotal']         = $parcela_valor;
                $new_invoice_data['total']            = $parcela_valor;
                $new_invoice_data['adjustment']       = $_invoice->adjustment;
                $new_invoice_data['discount_percent'] = $_invoice->discount_percent;
                $new_invoice_data['discount_total']   = $_invoice->discount_total;
                $new_invoice_data['discount_type']    = $_invoice->discount_type;
                $new_invoice_data['terms']            = clear_textarea_breaks($_invoice->terms);
                $new_invoice_data['sale_agent']       = $_invoice->sale_agent;
                // Since version 1.0.6
                $new_invoice_data['billing_street']                     = clear_textarea_breaks($_invoice->billing_street);
                $new_invoice_data['billing_city']                       = $_invoice->billing_city;
                $new_invoice_data['billing_state']                      = $_invoice->billing_state;
                $new_invoice_data['billing_zip']                        = $_invoice->billing_zip;
                $new_invoice_data['billing_country']                    = $_invoice->billing_country;
                $new_invoice_data['shipping_street']                    = clear_textarea_breaks($_invoice->shipping_street);
                $new_invoice_data['shipping_city']                      = $_invoice->shipping_city;
                $new_invoice_data['shipping_state']                     = $_invoice->shipping_state;
                $new_invoice_data['shipping_zip']                       = $_invoice->shipping_zip;
                $new_invoice_data['shipping_country']                   = $_invoice->shipping_country;
                $new_invoice_data['banco_inter_slip_invoice_parent_id'] = $invoice_id;
                if ($_invoice->include_shipping == 1) {
                    $new_invoice_data['include_shipping'] = $_invoice->include_shipping;
                }
                $new_invoice_data['include_shipping']         = $_invoice->include_shipping;
                $new_invoice_data['show_shipping_on_invoice'] = $_invoice->show_shipping_on_invoice;
                // Determine status based on settings
                if ($new_recurring_invoice_action == 'generate_and_send' || $new_recurring_invoice_action == 'generate_unpaid') {
                    $new_invoice_data['status'] = 1;
                } elseif ($new_recurring_invoice_action == 'generate_draft') {
                    $new_invoice_data['save_as_draft'] = true;
                }
                $new_invoice_data['clientnote']            = clear_textarea_breaks($_invoice->clientnote);
                $new_invoice_data['adminnote']             = '';
                $new_invoice_data['allowed_payment_modes'] = unserialize($_invoice->allowed_payment_modes);
                $new_invoice_data['is_recurring_from']     = $_invoice->id;
                $new_invoice_data['newitems']              = [];
                $key                                       = 1;
                $custom_fields_items                       = get_custom_fields('items');
                foreach ($_invoice->items as $item) {
                    $new_invoice_data['newitems'][$key]['description']      = $item['description'];
                    $new_invoice_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
                    $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
                    $new_invoice_data['newitems'][$key]['unit']             = $item['unit'];
                    $new_invoice_data['newitems'][$key]['taxname']          = [];
                    $taxes                                                  = get_invoice_item_taxes($item['id']);
                    foreach ($taxes as $tax) {
                        // tax name is in format TAX1|10.00
                        array_push($new_invoice_data['newitems'][$key]['taxname'], $tax['taxname']);
                    }
                    $new_invoice_data['newitems'][$key]['rate']  = $item['rate'];
                    $new_invoice_data['newitems'][$key]['order'] = $item['item_order'];

                    foreach ($custom_fields_items as $cf) {
                        $new_invoice_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                        if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                            define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                        }
                    }
                    $key++;
                }
                bancoInterAddFatura($new_invoice_data);
            }
        }
    }
}
