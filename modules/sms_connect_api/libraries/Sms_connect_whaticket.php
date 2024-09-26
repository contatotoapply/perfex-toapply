<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sms_connect_whaticket extends App_sms
{

    public function __construct()
    {
        parent::__construct();
    }


    public function send($number, $message)
    {

        $endpoint   = $this->get_option('connect_whaticket', 'endpoint');

        $authorized = $this->get_option('connect_whaticket', 'authorized');

        $number     = preg_replace('/[^0-9,.]+/', '', $number);

        $send       = $this->send_to_api($endpoint, $authorized, $number,  $message);

        if ($send) {
            log_activity('Notificação enviada via Whatsapp para: ' . $number . ', mensagem: ' . $message);
            return true;
        } else {
            $this->set_error('O envio falhou : <BR> Erro: ' . $send->message);
            return false;
        }
    }


    public function send_to_api($endpoint, $authorized, $number, $text)
    {

        $curl = curl_init();
        $body = [
            "number" => $number,
            "body" => $text
        ];

        log_activity('[SMS_CONNECT_API - WHATICKET]'. json_encode($body));

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $authorized",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        return $response;
    }


    public function trigger($trigger, $phone, $merge_fields = [])
    {
        if (empty($phone)) {
            return false;
        }

        $gateway = $this->get_active_gateway();

        if ($gateway !== false) {

            $className = 'sms_' . $gateway['id'];

            if ($this->is_trigger_active($trigger)) {

                $trigger_value = get_option('sms_trigger_' . $trigger);

                $message = $this->parse_merge_fields(
                    $merge_fields,
                    $trigger_value
                );

                $message = clear_textarea_breaks($message);

                $message = str_replace(array("{", "}"), "", $message);

                static::$trigger_being_sent = $trigger;

                $retval = $this->ci->{$className}->send($phone, $message, $trigger);

                hooks()->do_action('sms_trigger_triggered', ['message' => $message, 'trigger' => $trigger, 'phone' => $phone]);

                static::$trigger_being_sent = null;

                return $retval;
            }
        }

        return false;
    }


    public function request($method, $uri)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlBase . $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                'apikey: ' . $this->apiKey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);

        return $response;
    }
}
