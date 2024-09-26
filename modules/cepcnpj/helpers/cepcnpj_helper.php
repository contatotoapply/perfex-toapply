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

/**hook add footer js */
hooks()->add_action('app_admin_footer', 'cepcnpj_add_footer_components');
hooks()->add_action('app_customers_footer', 'cepcnpj_add_footer_components');
function cepcnpj_add_footer_components()
{
    // loaded files js and css
    echo '<script type="text/javascript" src="' . base_url('modules/cepcnpj/assets/js/cepcnpj.js') . '" /></script>';
}
//app_customers_footer