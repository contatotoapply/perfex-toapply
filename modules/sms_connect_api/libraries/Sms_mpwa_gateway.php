<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sms_mpwa_gateway extends App_sms
{
    private $urlBase;
    private $apiKey;
    private $sender;

    public function __construct()
    {
        parent::__construct();
        $this->sender  = $this->get_option('mpwa_gateway', 'mpwa_gateway_api_sender');
        $this->apiKey  = $this->get_option('mpwa_gateway', 'mpwa_gateway_api_key');
        $this->urlBase = $this->get_option('mpwa_gateway', 'mpwa_gateway_url_base');
    }


    /**
     *
     * Formata um número de telefone para o formato (11) 11111-1111
     *
     * @author Kennedy Tedesco
     * @param string $numero
     * @return string
     */

    public function formatar_telefone($numero)
    {
        // Remove o código do país, quaisquer caracteres não numéricos e espaços
        $numero = preg_replace('/^[+]\d{2}|[^\d]| /', '', $numero);

        // Extrai o DDD e o restante do número
        preg_match('/^(\d{2})(\d{1,9})$/', $numero, $matches);

        $ddd   = $matches[1];
        $resto = $matches[2];

        // Se o DDD é menor ou igual a 30 e o resto do número tem menos de 9 dígitos, adiciona o "9"
        if (intval($ddd) <= 30 && strlen($resto) == 8) {
            $numero = $ddd . '9' . $resto;
        }

        // Se já tem o 9 e o DDD é menor ou igual a 30, remove o "9"
        else if (intval($ddd) > 30 && strlen($resto) == 9 && $resto[0] == '9') {
            $numero = $ddd . substr($resto, 1);
        }

        // Retorna o número de telefone formatado numericamente
        return '55' . $numero;
    }


    /**
     * @param mixed $number
     * @param mixed $message
     *
     * @return [type]
     */
    public function send($number, $message)
    {
        if (!$this->urlBase || !$this->apiKey) {
            log_activity('Sms_mpwa_gateway: URL Base ou API Key não configurados');
            return false;
        };

        if (!$number || !$message) {
            log_activity('Sms_mpwa_gateway: Número ou Mensagem não informados');
            return false;
        };

        $formatnumber = $this->formatar_telefone($number);

        $postdata = [
            "api_key" => $this->apiKey,
            "sender"  => $this->sender,
            "number"  => $formatnumber,
            "message" => $message
        ];

        $curl = curl_init();

        $sender = $this->sender;

        if (!$sender) {
            log_activity('Sms_mpwa_gateway: Sender não configurado.');
            return false;
        }

        log_activity('[SMS_CONNECT_API - MPWA]'. json_encode($postdata));

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlBase . "/send-message",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        // Verifica se houve algum erro durante a execução
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);

            curl_close($curl);
            log_activity("Erro na API ao enviar mensagem para o número: " .  $formatnumber . ", mensagem: " . $message . ", Erro: " . $error_msg);

            $this->set_error($error_msg);
            return false;
        }

        // Fecha a sessão cURL
        curl_close($curl);

        if (!$response) {
            log_activity("Nenhuma resposta da API ao enviar mensagem para o número: " . $formatnumber . ", mensagem: " . $message);
            $this->set_error('No response from the API');
            return false;
        }

        $response = json_decode($response);

        if(isset($response->status) && $response->status == true){
            return true;
        }
        return false;
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

                $trigger_value = get_option('sms_trigger_'. $trigger);

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
