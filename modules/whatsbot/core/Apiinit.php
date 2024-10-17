<?php

namespace modules\whatsbot\core;

require_once __DIR__.'/../third_party/node.php';
require_once __DIR__.'/../vendor/autoload.php';
use WpOrg\Requests\Requests as whatsbot_Requests;

class Apiinit
{
    /**
     * A função "the_da_vinci_code" foi modificada para desativar a validação da licença e
     * garantir que o módulo funcione sempre como ativo, independentemente da chave de compra.
     */
    public static function the_da_vinci_code($module_name)
    {
        // Validação de chave de compra e ativação removida.
        return true;
    }

    /**
     * A função "ease_of_mind" foi modificada para garantir que o módulo continue ativo,
     * sem verificação de função ou ativação.
     */
    public static function ease_of_mind($module_name)
    {
        // Validação de funções removida.
    }

    /**
     * A função "activate" foi modificada para desativar a exibição da tela de ativação.
     * Agora, o módulo é sempre considerado ativo.
     */
    public static function activate($module)
    {
        // Ativação removida. O módulo será considerado sempre ativo.
    }

    /**
     * A função "getUserIP" permanece inalterada, pois não está diretamente ligada à ativação.
     */
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

    /**
     * A função "pre_validate" foi modificada para retornar sempre uma validação positiva,
     * sem a necessidade de chave de compra ou validação de licença.
     */
    public static function pre_validate($module_name, $code = '')
    {
        // Sempre retornar status como verdadeiro, ignorando a validação da chave de compra.
        return ['status' => true, 'message' => 'Módulo validado com sucesso'];
    }
}
