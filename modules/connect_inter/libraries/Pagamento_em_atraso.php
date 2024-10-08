<?php


defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/../vendor/autoload.php');

class Pagamento_em_atraso
{

    const HORA_ENVIO_EMAIL_COBRANCA_RECORRENTE = 1;

    const DOMINGO  = 0;
    const SEGUNDA  = 1;
    const TERCA    = 2;
    const QUARTA   = 3;
    const QUINTA   = 4;
    const SEXTA    = 5;
    const SABADO   = 6;

    protected $ci;
    protected $gt_inter;
    protected $hoje;
    protected $testEnviarSMTPEmail = true; // TODO Verificar

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->gt_inter = $this->ci->inter_gateway;
        $this->ci->load->helper('connect_inter/connect_inter');
        $this->ci->load->library(['parser', 'connect_inter/feriado_library', 'connect_inter/enviar_boleto_pdf_banco_inter']);
        $this->ci->load->library('connect_inter/business_day_calculator');
        $this->ci->load->model('invoices_model');

        if ($this->ci->session->userdata('hoje')) {
            $this->hoje = $this->ci->session->userdata('hoje');
        } else {
            $this->hoje = date('Y-m-d');
        }
    }

    private function enviarEmail($invoice)
    {
        $hoje = $this->hoje;

        if (date('H') >= self::HORA_ENVIO_EMAIL_COBRANCA_RECORRENTE) {

            $contacts = $this->ci->db
                ->select('id,userid,firstname,lastname,email,invoice_emails,active')
                ->where('userid', $invoice->clientid)
                ->where('invoice_emails', 1)
                ->where('active', 1)
                ->get(db_prefix() . 'contacts')->result_array();

            if (!empty($contacts)) {

                $enviou_email_arr = [];

                foreach ($contacts as $contact) {
                    $invoice_number = format_invoice_number($invoice->id);

                    $data = [
                        'invoice_id'        => $invoice->id,
                        'contact_firstname' => $contact['firstname'],
                        'invoice_link'      => base_url('invoice/' . $invoice->id . '/' . $invoice->hash),
                        'invoice_number'    => $invoice_number,
                        'invoice_duedate'   => _d($invoice->duedate),
                        'email'             => $contact['email']
                    ];

                    // ANEXOS
                    $invoice        = $this->ci->invoices_model->get($invoice->id);

                    $pdf = invoice_pdf($invoice);
                    $attach = $pdf->Output($invoice_number . '.pdf', 'S');

                    $emailtemplate = mail_template('email_pagamento_em_atraso', 'banco_inter', $data);
                    $emailtemplate->add_attachment([
                        'attachment' => $attach,
                        'filename'   => str_replace('/', '-', $invoice_number . '.pdf'),
                        'type'       => 'application/pdf',
                    ]);

                    // Se for do banco inter, enviar boleto
                    if (get_option('paymentmethod_banco_inter_enviar_boleto_email')) {
                        $emailtemplate->add_attachment($this->ci->enviar_boleto_pdf_banco_inter->getBoletoBancoInter($invoice));
                    }

                    if ($this->testEnviarSMTPEmail) {
                        if ($emailtemplate->send()) {
                            $enviou_email_arr[] = $invoice->id;
                        }
                    } else {
                        $enviou_email_arr[] = $invoice->id;
                    }
                }

                $hoje = date('Y-m-d H:i:s', strtotime($this->hoje . date('H:i:s')));

                $dataAtualMais2Dias = date('Y-m-d', strtotime('+2 days', strtotime($hoje)));

                foreach ($enviou_email_arr as $invoice_id) {

                    $this->ci->db->where('id', $invoice_id)
                        ->update(
                            db_prefix() . 'invoices',
                            [
                                'pagamento_atrasado_email_enviado_at'      => $hoje,
                                'pagamento_atrasado_last_overdue_reminder' => $dataAtualMais2Dias
                            ]
                        );
                }
            }
        }
    }

    // RECORRENTES
    public function enviarEmailFaturasRecorrentes()
    {
        $data_servidor = $this->hoje;

        $this->ci->db->select('i.id, i.number, i.status, i.recurring, i.hash, i.pagamento_atrasado_email_enviado_at,
                   i.pagamento_atrasado_last_overdue_reminder, i.recurring_type, i.custom_recurring,
                   i.is_recurring_from, i.duedate, i.clientid, c.default_language,i.bi_nosso_numero');
        $this->ci->db->from('tblinvoices i');
        $this->ci->db->join('tblclients c', 'i.clientid = c.userid');
        $this->ci->db->where('i.duedate IS NOT NULL');
        $this->ci->db->where("(i.is_recurring_from IS NOT NULL OR i.recurring != 0)");
        $this->ci->db->where('i.status', Invoices_model::STATUS_OVERDUE);
        $this->ci->db->where('(i.pagamento_atrasado_email_enviado_at IS NULL)');

        $invoices = $this->ci->db->get()->result();
        if (!empty($invoices)) {

            foreach ($invoices as $invoice) {

                $dia_para_enviar = $this->ci->business_day_calculator->getNextBusinessDay($invoice->duedate);

                if ($dia_para_enviar == $data_servidor) {
                    $this->enviarEmail($invoice);
                }
            }
        }
    }

    // NÃO RECORRENTES
    public function enviarEmailFaturasNaoRecorrentes()
    {
        $data_servidor = $this->hoje;

        $this->ci->db->select('i.id, i.number, i.status, i.recurring, i.hash, i.pagamento_atrasado_email_enviado_at,
                   i.pagamento_atrasado_last_overdue_reminder, i.recurring_type, i.custom_recurring,
                   i.is_recurring_from, i.duedate, i.clientid, c.default_language,i.bi_nosso_numero');
        $this->ci->db->from('tblinvoices i');
        $this->ci->db->join('tblclients c', 'i.clientid = c.userid');
        $this->ci->db->where('i.duedate IS NOT NULL');
        $this->ci->db->where("(i.is_recurring_from IS NULL AND i.recurring = 0)");
        $this->ci->db->where('i.status', Invoices_model::STATUS_OVERDUE);
        $this->ci->db->where('(i.pagamento_atrasado_email_enviado_at IS NULL)');
        $invoices = $this->ci->db->get()->result();

        if (!empty($invoices)) {

            $enviar_email = false;

            foreach ($invoices as $invoice) {

                $pagamento_atrasado_last_overdue_reminder = $invoice->pagamento_atrasado_last_overdue_reminder ?? $invoice->duedate;

                $dia_para_enviar = $this->ci->business_day_calculator->getNextBusinessDay($pagamento_atrasado_last_overdue_reminder);

                if ($dia_para_enviar == $data_servidor) {
                    $enviar_email = true;
                }

                if ($enviar_email) {
                    $this->updateInvoiceMailingDay($invoice->id, $dia_para_enviar);
                    if ($invoice->pagamento_atrasado_last_overdue_reminder <= $data_servidor) {
                        $this->enviarEmail($invoice);
                    }
                }
            }
        }
    }

    private function updateInvoiceMailingDay($id, $next_mailing_day)
    {
        $data = ['pagamento_atrasado_last_overdue_reminder' => $next_mailing_day];
        $this->ci->db->where('id', $id)->update(db_prefix() . 'invoices', $data);
    }
}
