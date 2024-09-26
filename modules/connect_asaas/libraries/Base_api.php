<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Base_api extends App_gateway {

    const AMBIENTE_URL_PRODUCAO = 'https://api.asaas.com';
    const AMBIENTE_URL_SANDBOX  = 'https://sandbox.asaas.com/api';

    protected $apiKey;
    protected $apiUrl;
    protected $ci;
    protected $isSandbox;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->isSandbox = get_option('paymentmethod_connect_asaas_sandbox');
    }

    public function getUrlBase()
    {
        return $this->isSandbox ? self::AMBIENTE_URL_SANDBOX : self::AMBIENTE_URL_PRODUCAO;
    }

    public function getApiKey()
    {
        return $this->isSandbox
            ? trim($this->ci->encryption->decrypt(get_option('paymentmethod_connect_asaas_api_key_sandbox')))
            : trim($this->ci->encryption->decrypt(get_option('paymentmethod_connect_asaas_api_key')));
    }

}
