<?php

defined('BASEPATH') or exit('No direct script access allowed');

// {
//     "empresa": "",// nome da empresa
//     "e-mail": "", // email do contato master
//     "nome": "", //nome do contato master
//     "telefone": "",
//     "fim_assinatura": "",// data de vencimento da próxima fatura + 3 dias ex: vence em 11/09/2023 o campo vai com a data 14/09/2023 e o horário padrão 10:00:00
//     "status_assinura": "0", // 0 ativa a conta e 1 suspende, essa informação vai de acordo com  campo Enviar lembrete e suspender serviços após X dias. SOFTPOWER , se lá estiver 4 conta 4 dias após a data de vencimento da fatura e troca de 0 para 1, suspendendo e quando enviar um envio manual de suspensão atualiza para 1
//     "id_conta_chat": "",//customfildes CHAT: ID da Conta
//     "qunt_user": "", //customfildes CHAT: Quant User
//     "qunt_inbox": "", //customfildes CHAT: Quant inbox
//     "id_cliente_envios": "", //customfildes ENVIOS: ID Cliente
//     "id_assintura_envios": "", //customfildes ENVIOS: ID de Assinatura
//     "bot_id": "",//customfildes BOT: ID
//     "bot_name": "",//customfildes BOT: ID
//     "sg_url": "",//customfildes SG: URL
//     "sg_token": "",//customfildes SG: Token
//     "sg_app": ""//customfildes  SG: App
// "lembrete": "servico_pronto"
// }
class Send_post
{
    private $CI;

    public function __construct()
    {
        $this->CI = & get_instance();
    }

    /**
     * @param mixed $data
     *
     * @return [type]
     */
    private function send($data)
    {
        if (!isset($data['url'])) {
            log_activity('Url não definida');
            return;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $data['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data)
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        echo $response;

        if ($response === false) {
            log_activity("A requisição falhou.");
        } else {
            return true;
        }


    }



    /**
     * @param mixed $invoice_id
     * @param mixed $trigger_name 'sms_trigger_notifications_zap_engine_servico_suspenso_json'
     *
     * @return [type]
     */
    private function formatReminderPostJson($invoice_id, $trigger_name)
    {
        $invoice = (array) $this->CI->invoices_model->get($invoice_id);

        $fields = clear_textarea_breaks(get_option($trigger_name));

        if($fields) {

            $fiedls_formatted = [];

            $invoice = (array) $invoice;
            $client  = (array) $invoice['client'];

            unset($invoice['client']);
            unset($invoice['items']);
            unset($invoice['attachments']);
            unset($invoice['payments']);

            $fields = json_decode($fields);

            $userid = $client['userid'];

            $fiedls_formatted['invoice_id'] = $invoice['id'];

            $fiedls_formatted['userid']     = $client['userid'];

            $custom_fields = $this->CI->db->select('slug,name,value')->from('tblcustomfieldsvalues t1')
            ->join('tblcustomfields t2', 't2.id = t1.fieldid')->where('relid', $userid)
            ->get()->result_array();

            $originalContactsArray = $this->CI->db->select('id,firstname,lastname,email')
                ->from(db_prefix() . 'contacts')->where('userid', $userid)
                ->where('active', 1)
                ->where('is_primary', 1)
                ->get()->result_array();

            $transformedArray = [];

            // Iterando sobre o array original
            foreach ($originalContactsArray as $index => $contact) {
                // Para cada contato, construímos as chaves e valores para o novo array
                $prefix = 'contact_primary_' . ($index + 1) . '_';
                foreach ($contact as $key => $value) {
                    $newKey = $prefix . $key;
                    $transformedArray[$newKey] = $value;
                }
            }

            $outputCustomFieldsArray = [];

            foreach ($custom_fields as $item) {
                $outputCustomFieldsArray[$item['slug']] = $item['value'];
            }

            $fiedls_formatted = array_merge($fiedls_formatted, $invoice, $client, $outputCustomFieldsArray, $transformedArray);

            $novos = [];

            foreach($fields as $field) {
                $novos[$field] = $fiedls_formatted[$field];
            }
            dd(1, $novos); // TODO - parei aqui na parte de testes
            return $fiedls_formatted;
        }

        return [];
    }


    public function lembrete($invoice_id, $trigger_name)
    {
        switch ($trigger_name) {
            case 'sms_trigger_notifications_zap_engine_lembrete_pagamento_json':
                return $this->formatReminderPostJson($invoice_id, $trigger_name);
                break;

            default:
                # code...
                break;
        }

    }

    // public function lembreteNoDiaDoVencimento($invoice_id, $trigger_name)
    // {
    //     $this->formatReminderPostJson($invoice_id, $trigger_name);
    // }
    // public function lembreteDeFaturaPaga()
    // {

    // }
    // public function lembreteDeFaturasVencidas()
    // {

    // }

    // public function lembreteDeSuspensãoDeServicos()
    // {

    // }

    // public function enviarNoDiaDaSuspensao()
    // {

    // }

}
