<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sms_notifications_zap_engine_library extends App_sms
{
    private $urlBase;
    private $apiKey;
    private $globalApikey;
    private $instanceName;

    public function __construct()
    {
        parent::__construct();
        $this->instanceName = get_option('sms_notifications_zap_engine_library_whatsapp_api_instance_name_selected');
        $this->urlBase      = get_option('sms_notifications_zap_engine_library_zap_engine_url');
        $this->apiKey       = get_option('sms_notifications_zap_engine_library_api_key');
        $this->globalApikey = get_option('sms_notifications_zap_engine_library_zap_engine_token');
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
            log_activity('Central de Notificações SoftpowerNE: URL Base ou API Key não configurados');
            return false;
        };

        if (!$number || !$message) {
            log_activity('Central de Notificações SoftpowerNE: Número ou Mensagem não informados');
            return false;
        };

        $formatnumber = $this->formatar_telefone($number);

        $postdata = [
            "number" => $formatnumber,
            "options" => [
                "delay" => 1200,
                "presence" => "composing",
                "linkPreview" => true
            ],
            "textMessage" => [
                "text" => $message
            ]
        ];

        log_activity('[SMS_CONNECT_API - NOTIFICATION PAYLOAD]'. json_encode($postdata));

        $curl = curl_init();

        $instance_name = $this->instanceName;

        if (!$instance_name) {
            log_activity('Central de Notificações Zap Engine EVO API: Instância não configurada');
            return false;
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlBase . "/message/sendText/{$instance_name}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => array(
                'apikey: ' . $this->apiKey,
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

        if ($response->status == 401) {
            log_activity("Erro na API ao enviar mensagem para o número: " .  $formatnumber . ", mensagem: " . $message . ", Erro: " . $response->message);
            throw new Exception($response->message, 200);
            return false;
        }

        return $response; // Retorna verdadeiro indicando que tudo correu bem

    }

    public function trigger($trigger, $phone, $merge_fields = [])
    {
        if (empty($phone)) {
            return false;
        }

        $gateway = $this->get_active_gateway();

        if ($gateway !== false) {

            $className = 'sms_' . $gateway['id'];

            // TODO: parei aqui 2024-01-22 10:01:04
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

    /**
     * @param mixed $postdata
     * '{
     *     "instanceName": "Instância Nome 3",
     *     "token": "",
     *     "qrcode": true
     * }'
     * @return [type]
     */
    public function create_instance($postdata)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlBase . '/instance/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'apikey: B6D711FCDE4D4FD5936544120E713974'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);

        return $response;
    }

    public function list_instances()
    {
        $isTest = true;

        if ($isTest) {
            $curl = curl_init();

            // Somente Global Consegue listar

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->urlBase . '/instance/fetchInstances',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'apikey: ' . $this->globalApikey,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $response = json_decode($response);

            return $response;
        }
    }

    /**
     * @param mixed $instance
     *
     * @return [type]
     */
    public function delete_instance($instance)
    {
        // Somente Global Consegue listar
        // $this->apiKey = get_option("sms_notifications_zap_engine_library_zap_engine_token");
        $this->apiKey = $this->globalApikey;
        $response = $this->request('DELETE', '/instance/delete/' . $instance);
        return $response;
    }

    public function status_connection_instance($instance)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlBase . '/instance/connectionState/' . $instance,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'apikey: ' . $this->globalApikey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);

        if (isset($response->instance)) {
            return $response->instance;
        }
        return false;
    }

    /**
     * @param mixed $instance
     *
     * @return [type]
     */
    public function logout_instance($instance)
    {
        $response = $this->request('DELETE', '/instance/logout/' . $instance);

        return $response;
    }

    /**
     * @param mixed $instance
     *
     * @return [type]
     */
    public function restart_instance($instance)
    {
        $response = $this->request('PUT', '/instance/restart/' . $instance);

        return $response;
    }

    public function connect_instance($instance)
    {
        $this->apiKey = $this->globalApikey;

        $response = $this->request('GET', '/instance/connect/' . $instance);

        return $response;
    }
}
