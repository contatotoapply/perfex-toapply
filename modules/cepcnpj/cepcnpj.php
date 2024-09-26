<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Automação de CEP e CNPJ
Description: Modulo de integração de CEP e CNPJ
Version: 1.0.0
Requires at least: 3.0.0
Author: Nando Cardoso - Connect Designers
Author URI: https://connectdesigners.com.br
*/

define('CEPCNPJ_MODULE_NAME', 'cepcnpj');
$CI = &get_instance();

/**helpers */
$CI->load->helper(CEPCNPJ_MODULE_NAME . '/cepcnpj');