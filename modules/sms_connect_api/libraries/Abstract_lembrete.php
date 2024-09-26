<?php

defined('BASEPATH') or exit('No direct script access allowed');

abstract class Abstract_lembrete
{
    public $ci;
    public $column_enviado_at;
    public $column_name_qtd_dias;
    public $reminder_type;
    public $gateway_id;

    /**
     * @param mixed $clientid
     *
     * @return [type]
     */
    public function get_contacts($clientid)
    {
        $contacts = $this->ci->db
            ->select('id,userid,phonenumber,firstname,lastname,email,central_notificacao_contact_whatsapp,active')
            ->where('userid', $clientid)
            ->where('(central_notificacao_contact_whatsapp = 1)')
            ->where('active', 1)
            ->get(db_prefix() . 'contacts')->result_array();
        return $contacts;
    }

    /**
     * @param mixed $invoice_id
     *
     * @return [type]
     */
    public function atualizar_qtd_tentativas($invoice_id)
    {
        $this->ci->db->where('id', $invoice_id);
        $this->ci->db->set($this->column_name_qtd_dias, $this->column_name_qtd_dias . "+1", false);
        $this->ci->db->update(db_prefix() . 'invoices');
    }

    /**
     * @param mixed $invoice_id
     *
     * @return [type]
     */
    public function criar_lembrete($invoice_id)
    {
        $data = [
            'date'                => date('Y-m-d'),
            'created_by_staff_id' => function_exists('get_staff_user_id') ? get_staff_user_id() : null,
            'rel_id'              => $invoice_id,
            'rel_type'            => 'invoice',
            'reminder_type'       => $this->reminder_type
        ];
        $this->ci->db->insert(db_prefix() . 'central_notificacoes_lembretes', $data);
    }

    /**
     * @param mixed $invoice_id
     *
     * @return [type]
     */
    public function atualizar_data_envio($invoice_id)
    {
        $data_atual = date('Y-m-d H:i:s');

        $this->ci->db->where('id', $invoice_id)
            ->update(
                db_prefix() . 'invoices',
                [
                    $this->column_enviado_at => $data_atual,
                ]
            );
    }

    /**
     * @return [type]
     */
    public function get_gateway_id()
    {
        $gateway_id = $this->ci->app_sms->get_active_gateway()['id'];
        return  $gateway_id;
    }

    /**
     * @return [type]
     */
    public function get_gateway_active()
    {
        $gateway_id = $this->get_gateway_id();
        $gateway = $this->ci->{'sms_' . $gateway_id};
        return $gateway;
    }


    public function my_trigger($trigger, $phone, $merge_fields = [])
    {

        if (empty($phone)) {
            return false;
        }


        $gateway = $this->ci->app_sms->get_active_gateway();

        if ($gateway !== false) {

            $className = 'sms_' . $gateway['id'];

            if ($this->ci->app_sms->is_trigger_active($trigger)) {

                $trigger_value = get_option('sms_trigger_' . $trigger);

                $message = $this->ci->app_sms->parse_merge_fields(
                    $merge_fields,
                    $trigger_value
                );

                $message = clear_textarea_breaks($message);

                $message = str_replace(array("{", "}"), "", $message);

                $this->ci->app_sms::$trigger_being_sent = $trigger;

                $retval = $this->ci->{$className}->send($phone, $message, $trigger);

                hooks()->do_action('sms_trigger_triggered', ['message' => $message, 'trigger' => $trigger, 'phone' => $phone]);

                $this->ci->app_sms::$trigger_being_sent = null;

                return $retval;
            }
        }

        return false;
    }
}
