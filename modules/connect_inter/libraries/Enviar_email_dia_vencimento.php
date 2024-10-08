<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Enviar_email_dia_vencimento
{

    private $ci;
    private $hoje;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->helper('connect_inter/connect_inter');
        $this->ci->load->library(['parser', 'connect_inter/enviar_boleto_pdf_banco_inter']);
        $this->ci->load->model('invoices_model');

        if ($this->ci->session->userdata('hoje')) {
            $this->hoje = $this->ci->session->userdata('hoje');
        } else {
            $this->hoje = date('Y-m-d');
        }
    }


    public function enviar()
    {

        try {

            $invoices = $this->ci->db->select('id,company,clientid,hash,duedate,default_language,bi_nosso_numero')
                ->from(db_prefix() . 'invoices')
                ->join(db_prefix() . 'clients', db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid')
                ->where('email_cobranca_enviado_dia_vencimento_at IS NULL')
                ->where('duedate', $this->hoje)
                ->where_in('status', [Invoices_model::STATUS_UNPAID, Invoices_model::STATUS_PARTIALLY])
                ->get()->result();

            $enviar_email = true;

            foreach ($invoices as $invoice) {

                if ($this->ci->session->has_userdata("dia_vencimento_{$invoice->id}")) {
                    $this->atualiar_data_que_email_foi_enviado($invoice->id);
                    $this->ci->session->unset_userdata("dia_vencimento_{$invoice->id}");
                    $enviar_email = false;
                }

                $contacts = $this->ci->clients_model->get_contacts($invoice->clientid, [
                    'active' => 1,
                    'invoice_emails' => 1,
                ]);

                if (!is_null($contacts) && $enviar_email) {

                    $invoiceLink    = base_url('invoice/' . $invoice->id . '/' . $invoice->hash);
                    $invoiceNumber  = format_invoice_number($invoice->id);
                    $invoiceDueDate = _d($invoice->duedate);
                    if (get_option('paymentmethod_connect_inter_enviar_boleto_email')) {
                        $boleto = $this->ci->enviar_boleto_pdf_banco_inter->getBoletoBancoInter($invoice);
                    }
                    $invoice        = $this->ci->invoices_model->get($invoice->id);
                    $pdf            = invoice_pdf($invoice);
                    $attach         = $pdf->Output($invoiceNumber  . '.pdf', 'S');

                    foreach ($contacts as $contact) {

                        $data = [
                            'invoice_id'        => $invoice->id,
                            'contact_firstname' => $contact['firstname'],
                            'email'             => $contact['email'],
                            'invoice_link'      => $invoiceLink,
                            'invoice_number'    => $invoiceNumber,
                            'invoice_duedate'   => $invoiceDueDate
                        ];

                        // Se for do banco inter, enviar boleto.
                        $mailtemplate = mail_template('sua_fatura_vence_hoje', 'banco_inter', $data);
                        $mailtemplate->add_attachment([
                            'attachment' => $attach,
                            'filename'   => str_replace('/', '-', $invoiceNumber  . '.pdf'),
                            'type'       => 'application/pdf',
                        ]);

                        if (get_option('paymentmethod_connect_inter_enviar_boleto_email')) {
                            $mailtemplate->add_attachment($boleto);
                        }

                        $email_ja_enviado = $this->ci->db->where('rel_id', $invoice->id)
                            ->where('rel_type', 'invoice')
                            ->where('email', $contact['email'])
                            ->where('slug', $mailtemplate->slug)
                            ->get(db_prefix() . 'tracked_mails')->row();

                        if (is_null($email_ja_enviado)) {

                            if ($mailtemplate->send()) {

                                if (!$this->ci->session->has_userdata("dia_vencimento_{$invoice->id}")) {
                                    $this->ci->session->set_userdata("dia_vencimento_{$invoice->id}", $invoice->id);
                                }

                                log_activity("[Fatura ID: ({$invoice->id})] | enviarEmailCobrancaDiaVencimento | " . $contact['email']);
                            }
                        }
                    }

                    $this->atualiar_data_que_email_foi_enviado($invoice->id);
                }
            }
        } catch (\Throwable $th) {
            log_activity('Houve um erro ao tentar enviar o email de cobrança no dia do vencimento. ' . $th->getMessage());
        }
    }

    private function atualiar_data_que_email_foi_enviado($invoice_id)
    {
        $this->ci->db->where('id', $invoice_id)
            ->update(db_prefix().'invoices', ['email_cobranca_enviado_dia_vencimento_at' => date('Y-m-d H:i:s')]);
    }
}
