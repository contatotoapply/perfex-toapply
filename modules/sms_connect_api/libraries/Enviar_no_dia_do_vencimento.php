<?php

class Enviar_no_dia_do_vencimento extends Abstract_lembrete
{
    public $ci;
    public $column_enviado_at    = 'cn_aviso_dia_do_vencimento_enviado_at';
    public $column_name_qtd_dias = 'cn_aviso_dia_do_vencimento_qtd_tentativas';
    public $reminder_type        = 'dia_do_vencimento';

    public function __construct()
    {
        $this->ci      = &get_instance();
        $this->ci->load->model('invoices_model');
    }

    public function enviar_lembrete()
    {
        if (date('H') >= 7) {

            $select = 't1.id,t1.status,t1.hash, t1.total, t1.recurring,
            t1.cn_aviso_suspensao_qtd_tentativas, t1.recurring_type,custom_recurring,t1.currency,
            t1.is_recurring_from,t1.duedate,t1.clientid, t2.company';

            $hoje = date('Y-m-d');

            $this->ci->db->select($select)
                ->from(db_prefix().'invoices as t1')
                ->join(db_prefix() . 'clients t2', 't1.clientid = t2.userid')
                ->join(db_prefix() . 'central_notificacoes_lembretes t3', 't1.id = t3.rel_id AND t3.reminder_type = "dia_do_vencimento" AND t3.date="'.$hoje.'"', 'left')
                ->where('t1.duedate IS NOT NULL', null, null)
                ->where('t1.duedate', $hoje, null)
                ->where('t3.date IS NULL')
                ->where('t1.cn_aviso_dia_do_vencimento_enviado_at IS NULL')
                ->where('t1.cn_aviso_dia_do_vencimento_qtd_tentativas <=', 2);

            $invoices = $this->ci->db
                ->where_not_in('t1.status', [Invoices_model::STATUS_PAID, Invoices_model::STATUS_PARTIALLY])
                ->order_by('t1.duedate', 'asc')
                ->get()
                ->result();

            $description = "Enviou whatsapp sobre lembrete do dia do vencimento para ";

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
                                'invoice_id'        => $invoice_id,
                                'contact_firstname' => $contact['firstname'],
                                'invoice_link'      => base_url('invoice/' . $invoice_id . '/' . $invoice->hash),
                                'invoice_number'    => $invoice_number,
                                'invoice_duedate'   => _d($invoice->duedate),
                                'invoice_total'     => app_format_money($invoice->total, $invoice->currency),
                                'phonenumber'       => $phonenumber,
                                'companyname'       => get_option('companyname'),
                                'empresa'           => $invoice->company,
                                'cliente_email'     => $contact['email'],
                            ];

                            $res = $gateway->trigger('notifications_zap_engine_enviar_no_dia_do_vencimento', $phonenumber, $merge_fields);

                            if ($res) {
                                $this->atualizar_data_envio($invoice_id);
                                $this->ci->invoices_model->log_invoice_activity($invoice_id, $description . " " . $phonenumber. " Contato: ". $contact['firstname']);
                                log_activity('Lembrete | Mensagem enviada para '. $phonenumber. " em ". date('Y-m-d H:i:s') . ' [Fatura ID ' . $invoice_id . ']. Lembrente no dia do vencimento');
                            } else {
                                log_activity('Lembrete | Mensagem não enviada para '. $phonenumber. " em ". date('Y-m-d H:i:s') . ' [Fatura ID ' . $invoice_id . ']. Lembrente no dia do vencimento'. json_encode(['res' => $res]));
                            }

                        } else {
                            log_activity("Lembrete | MENSAGEM NÃO ENVIADA, pois o cliente não tem um telefone válido. [Fatura ID {$invoice_id}]. Lembrente no dia do vencimento | Email do Contato: " . $contact['email']);
                        }
                    }
                } else {
                    log_activity("Lembrete | MENSAGEM NÃO ENVIADA, pois o cliente não tem um telefone válido. [Fatura ID {$invoice_id}]. Lembrente no dia do vencimento.");
                }
            }
        }
    }
}
