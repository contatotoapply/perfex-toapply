<?php

namespace modules\whatsbot\core;

require_once __DIR__.'/../third_party/node.php';
require_once __DIR__.'/../vendor/autoload.php';
use WpOrg\Requests\Requests as whatsbot_Requests;

class Apiinit
{
    public static function the_da_vinci_code($module_name)
    {
        // Removendo verificação de ativação e forçando o módulo a ser considerado sempre ativado
        return true; // O módulo sempre será considerado validado
    }

    public static function ease_of_mind($module_name)
    {
        // Removendo checagem de funções de ativação e garantindo que o módulo continue ativo
        return true; // Ignora a desativação do módulo
    }

    public static function activate($module)
    {
        // Removendo necessidade de validação e exibição de tela de ativação
        return; // Ignora o processo de ativação, assumindo que o módulo está sempre ativo
    }

    public static function getUserIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public static function pre_validate($module_name, $code = '')
    {
        // Forçar a validação positiva e remover dependência da chave de compra
        return ['status' => true, 'message' => 'Module is always valid without purchase key'];
    }
}
