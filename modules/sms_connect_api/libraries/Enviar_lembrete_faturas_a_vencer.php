<?php

class Enviar_lembrete_faturas_a_vencer extends Abstract_lembrete
{
    public $ci;
    public $column_name_qtd_dias = 'cn_aviso_fatura_a_vencer_qtd_tentativas';
    public $reminder_type        = 'pagamento_a_vencer';

    private $total_dias_lembrete;

    public function __construct()
    {
        $this->ci      = &get_instance();
        $this->ci->load->model('invoices_model');
        $this->total_dias_lembrete = get_option('sms_notifications_zap_engine_library_qtd_dias_para_avisar_lembrete_vencimento');
        if(!$this->total_dias_lembrete) {
            return;
        }
    }

    public function enviar_lembrete()
    {

        if (date('H') >= 7) {

            // log_activity('Lembrete | Iniciando envio de lembrete de pagamento a vencer em ' . date('Y-m-d H:i:s'));

            $select = 't1.id,t1.status,t1.hash, t1.total, t1.recurring,t1.cn_aviso_fatura_a_vencer_enviado_at,
            t1.cn_aviso_fatura_a_vencer_qtd_tentativas, t1.recurring_type,custom_recurring,t1.currency,
            t1.is_recurring_from,t1.duedate,t1.clientid, t2.company, DATEDIFF(t1.duedate, CURDATE()) as total_dias_ate_vencimento';

            $total_dias_lembrete = explode(',', $this->total_dias_lembrete);

            $hoje = date('Y-m-d');

            $this->ci->db->select($select)
                ->from(db_prefix().'invoices as t1')
                ->join(db_prefix() . 'clients t2', 't1.clientid = t2.userid')
                ->join(db_prefix() . 'central_notificacoes_lembretes t3', 't1.id = t3.rel_id AND t3.reminder_type = "pagamento_a_vencer" AND t3.date = "'.$hoje.'"', 'left')
                ->where('t1.duedate IS NOT NULL', null, null)
                ->where('t1.duedate !=', Invoices_Model::STATUS_OVERDUE)
                ->where('t1.cn_aviso_fatura_a_vencer_qtd_tentativas <=', count($total_dias_lembrete))
                ->where('t3.date IS NULL');

            $this->ci->db->group_start();
            foreach($total_dias_lembrete as $dia) {
                $this->ci->db->or_where('DATEDIFF(t1.duedate, CURDATE()) = '. $dia, null, false);
            }
            $this->ci->db->group_end();

            $invoices = $this->ci->db
                ->where_not_in('t1.status', [Invoices_model::STATUS_PAID, Invoices_model::STATUS_PARTIALLY])
                ->order_by('t1.duedate', 'asc')
                ->get()
                ->result();

            $description = "Enviou whatsapp sobre lembrete de pagamento para ";

           $gateway = $this->get_gateway_active();

            foreach ($invoices as $invoice) {

                $invoice_id = $invoice->id;

                $this->atualizar_qtd_tentativas($invoice_id);

                $this->criar_lembrete($invoice_id);

                $contacts = $this->get_contacts($invoice->clientid);

                if (!empty($contacts)) {

                    $invoice_number = format_invoice_number($invoice->id);

                    foreach ($contacts as $contact) {

                        $phonenumber = $contact['phonenumber'];

                        if (!empty($phonenumber)) {

                            $merge_fields = [
                                'invoice_id'                => $invoice->id,
                                'contact_firstname'         => $contact['firstname'],
                                'invoice_link'              => base_url('invoice/' . $invoice->id . '/' . $invoice->hash),
                                'invoice_number'            => $invoice_number,
                                'invoice_duedate'           => _d($invoice->duedate),
                                'invoice_total'             => app_format_money($invoice->total, $invoice->currency),
                                'phonenumber'               => $phonenumber,
                                'companyname'               => get_option('companyname'),
                                'empresa'                   => $invoice->company,
                                'cliente_email'             => $contact['email'],
                                'total_dias_ate_vencimento' => $invoice->total_dias_ate_vencimento
                            ];

                            $res = $gateway->trigger('notifications_zap_engine_lembrete_pagamento', $phonenumber, $merge_fields);

                            if ($res) {
                                $this->ci->invoices_model->log_invoice_activity($invoice->id, $description . " " . $phonenumber. " Contato: ". $contact['firstname']);
                                log_activity('Lembrete | Mensagem enviada para '. $phonenumber. " em ". date('Y-m-d H:i:s') . ' [Fatura ID ' . $invoice->id . ']. Lembrete de pagamento a vencer');
                            } else {
                                log_activity('Lembrete | Mensagem não enviada para '. $phonenumber. " em ". date('Y-m-d H:i:s') . ' [Fatura ID ' . $invoice->id . ']. Lembrete de pagamento a vencer'. json_encode(['res' => $res]));
                            }

                        } else {
                            log_activity("Lembrete | MENSAGEM NÃO ENVIADA, pois o cliente não tem um telefone válido. [Fatura ID {$invoice->id}]. Lembrete de pagamento a vencer | Email do Contato: " . $contact['email']);
                        }
                    }
                } else {
                    log_activity("Lembrete | MENSAGEM NÃO ENVIADA, pois o cliente não tem um telefone válido. [Fatura ID {$invoice->id}]. Lembrete de pagamento a vencer.");
                }
            }
        }
    }
}
