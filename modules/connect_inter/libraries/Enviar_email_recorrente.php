<?php


defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/../vendor/autoload.php');

class Enviar_email_recorrente
{

    const HORA_ENVIO_EMAIL_COBRANCA_RECORRENTE = 1;
    const LIMITE_COBRANCA_RUN_TASK             = 1;

    const DOMINGO  = 0;
    const SEGUNDA  = 1;
    const TERCA    = 2;
    const QUARTA   = 3;
    const QUINTA   = 4;
    const SEXTA    = 5;
    const SABADO   = 6;

    protected $ci;
    protected $banco;
    protected $invoice;
    protected $gt_inter;
    protected $bi_active_log;
    protected $hoje;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->gt_inter = $this->ci->inter_gateway;
        $this->ci->load->helper('connect_inter/connect_inter');
        $this->ci->load->library(['connect_inter/feriado_library', 'connect_inter/enviar_boleto_pdf_banco_inter']);
        $this->bi_active_log = get_option('banco_inter_active_log');
        $this->ci->load->model('invoices_model');

        if($this->ci->session->userdata('hoje')){
            $this->hoje = $this->ci->session->userdata('hoje');
        }
        else{
            $this->hoje = date('Y-m-d');
        }
    }


    private function sendEmailCobrancaFaturasRecorrentes($invoices)
    {
        $hoje = $this->hoje;

        if (date('H') >= self::HORA_ENVIO_EMAIL_COBRANCA_RECORRENTE) {

            $enviar_email = true;

            $hoje = date('Y-m-d H:i:s', strtotime($this->hoje . date('H:i:s')));

            $dataMais2Dias = date('Y-m-d H:i:s', strtotime('+2 days', strtotime($hoje)));

            foreach ($invoices as $invoice) {

                if ($this->ci->session->has_userdata("pagamento_atrasado_fatura_{$invoice->id}")) {
                    $this->ci->session->unset_userdata("pagamento_atrasado_fatura_{$invoice->id}");
                    $this->atualizar_data_envio($hoje, $dataMais2Dias, $invoice->id);
                    $enviar_email = false;
                }

                if ($enviar_email) {

                    $contacts = $this->ci->db
                        ->select('id,userid,firstname,lastname,email,invoice_emails,active')
                        ->where('userid', $invoice->clientid)
                        ->where('(invoice_emails = 1)')
                        ->where('active', 1)
                        ->get(db_prefix() . 'contacts')->result_array();

                    if (!empty($contacts)) {

                        // ANEXOS
                        $invoice_number = format_invoice_number($invoice->id);
                        $invoice        = $this->ci->invoices_model->get($invoice->id);
                        $pdf            = invoice_pdf($invoice);
                        $attach         = $pdf->Output($invoice_number . '.pdf', 'S');
                        if (get_option('paymentmethod_banco_inter_enviar_boleto_email')) {
                            $boleto = $this->ci->enviar_boleto_pdf_banco_inter->getBoletoBancoInter($invoice);
                        }

                        foreach ($contacts as $contact) {

                            $data = [
                                'invoice_id'        => $invoice->id,
                                'contact_firstname' => $contact['firstname'],
                                'invoice_link'      => base_url('invoice/' . $invoice->id . '/' . $invoice->hash),
                                'invoice_number'    => $invoice_number,
                                'invoice_duedate'   => _d($invoice->duedate),
                                'email'             => $contact['email']
                            ];

                            $emailtemplate = mail_template('email_pagamento_em_atraso', 'banco_inter', $data);

                            $email_ja_enviado = $this->ci->db->where('rel_id', $invoice->id)
                                ->where('rel_type', 'invoice')
                                ->where('email', $contact['email'])
                                ->where('slug', $emailtemplate->slug)
                                ->get(db_prefix() . 'tracked_mails')->row();

                            if (is_null($email_ja_enviado)) {

                                $emailtemplate->add_attachment([
                                    'attachment' => $attach,
                                    'filename'   => str_replace('/', '-', $invoice_number . '.pdf'),
                                    'type'       => 'application/pdf',
                                ]);

                                // Se for do banco inter, enviar boleto
                                if (is_array($boleto) && !empty($boleto) && get_option('paymentmethod_banco_inter_enviar_boleto_email')) {
                                    $emailtemplate->add_attachment($boleto);
                                }

                                if ($emailtemplate->send()) {
                                    if (!$this->ci->session->has_userdata("pagamento_atrasado_fatura_{$invoice->id}")) {
                                        $this->ci->session->set_userdata("pagamento_atrasado_fatura_{$invoice->id}", $invoice->id);
                                    }
                                    log_activity("[Fatura ID {$invoice->id}]. Pagamento em Atraso | Email: " . $contact['email']);
                                }
                            }
                        }
                        $this->atualizar_data_envio($hoje, $dataMais2Dias, $invoice->id);
                    }
                }
            }
        }
    }

    private function atualizar_data_envio($data_atual, $data_mais_2_dias, $invoice_id)
    {
        $this->ci->db->where('id', $invoice_id)
            ->update(
                db_prefix() . 'invoices',
                [
                    'email_cobranca_enviado_recorrente_at' => $data_atual,
                    'bi_next_mailing_day'                  => $data_mais_2_dias
                ]
            );
    }

    public function agendarEenviarEmailE2DiasAposVencimentoFaturasRecorrentes()
    {
        $hoje = $this->hoje;

        $invoices = $this->ci->db
            ->select('id,status,recurring,bi_next_mailing_day,email_cobranca_enviado_recorrente_at,
                recurring_type,custom_recurring,is_recurring_from,duedate,clientid,default_language')
            ->from(db_prefix() . 'invoices')
            ->join('tblclients', 'tblinvoices.clientid = tblclients.userid')
            ->where('duedate IS NOT NULL')
            ->where("(is_recurring_from IS NOT NULL OR recurring != 0)")
            ->where('email_cobranca_enviado_recorrente_at IS NULL')
            ->where_not_in('status', [Invoices_model::STATUS_PAID, Invoices_model::STATUS_CANCELLED, Invoices_model::STATUS_DRAFT])
            ->where('duedate <', $hoje)
            ->get(NULL)
            ->result();

        if (empty($invoices)) {
            return false;
        }

        $enviar_email = true;

        foreach ($invoices as $invoice) {

            $biNextMailingDay = $invoice->bi_next_mailing_day ?? $invoice->duedate;

            $weekday = date('w', strtotime($biNextMailingDay));

            if ($data = $this->ci->feriado_library->eFeriado($biNextMailingDay)) {
                if ($dia_add = $data['e_carnaval']) {
                    $biNextMailingDay = date('Y-m-d H:i:s', strtotime("$biNextMailingDay +$dia_add days"));
                } else {
                    switch ($weekday) {
                        case self::QUINTA:
                        case self::SEXTA:
                            $biNextMailingDay = date('Y-m-d H:i:s', strtotime("$biNextMailingDay +4 days"));
                            break;
                        case self::SABADO:
                            $biNextMailingDay = date('Y-m-d H:i:s', strtotime("$biNextMailingDay +3 days"));
                            break;
                        default:
                            $biNextMailingDay = date('Y-m-d H:i:s', strtotime("$biNextMailingDay +1 day"));
                    }
                }
                $enviar_email = false;
            } elseif (in_array($weekday, [self::SABADO, self::DOMINGO])) {
                $biNextMailingDay = date('Y-m-d H:i:s', strtotime("$biNextMailingDay next Tuesday"));
                $enviar_email = false;
            } elseif ($weekday == self::SEXTA) {
                $biNextMailingDay = date('Y-m-d', strtotime("$biNextMailingDay next Monday"));
                $enviar_email = false;
            }

            $this->updateInvoiceMailingDay($invoice->id, $biNextMailingDay);
        }
        if ($enviar_email && date('Y-m-d') >= $biNextMailingDay && !in_array(date('w'), [self::SABADO, self::DOMINGO])) {
            $this->sendEmailCobrancaFaturasRecorrentes($invoices);
        }
    }

    private function updateInvoiceMailingDay($id, $next_mailing_day)
    {
        $this->ci->db->where('id', $id)->update(db_prefix() . 'invoices', ['bi_next_mailing_day' => $next_mailing_day]);
    }
}
