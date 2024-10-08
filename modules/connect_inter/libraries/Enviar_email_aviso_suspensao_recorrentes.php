<?php


defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/../vendor/autoload.php');

class Enviar_email_aviso_suspensao_recorrentes
{
    public const HORA_ENVIO_EMAIL_COBRANCA_RECORRENTE    = 1;
    public const LIMITE_COBRANCA_RUN_TASK                = 1;
    public const QUANTIDADE_DIAS_ENVIAR_EMAIL_SUCESSIVOS = 2;

    public const DOMINGO  = 0;
    public const SEGUNDA  = 1;
    public const TERCA    = 2;
    public const QUARTA   = 3;
    public const QUINTA   = 4;
    public const SEXTA    = 5;
    public const SABADO   = 6;

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
        $this->ci->load->library('connect_inter/business_day_calculator');

        if ($this->ci->session->userdata('hoje')) {
            $this->hoje = $this->ci->session->userdata('hoje');
        } else {
            $this->hoje = date('Y-m-d');
        }
    }

    public function enviar()
    {
        $data_servidor = $this->hoje;

        $this->ci->db->select('id, duedate,clientid, DATEDIFF(NOW(), duedate) AS days_overdue,
         DATEDIFF(NOW(), aviso_suspensao_proximo_dia_util) as total_dias_enviar_novamente,
         aviso_suspensao_proximo_dia_util,
             recurring, is_recurring_from, status');
        $this->ci->db->from('tblinvoices');
        $this->ci->db->where('(duedate IS NOT NULL and duedate < "' . $data_servidor . '" || aviso_suspensao_proximo_dia_util = "' . $data_servidor . '")');
        $this->ci->db->where_in('status', [Invoices_model::STATUS_PARTIALLY, Invoices_model::STATUS_OVERDUE]);
        $this->ci->db->where('(is_recurring_from IS NOT NULL || recurring != 0)', FALSE, FALSE);
        $this->ci->db->where('cancel_overdue_reminders', 0);
        $this->ci->db->where('aviso_suspensao_enviado_at IS NULL');
        $this->ci->db->where('pagamento_atrasado_email_enviado_at IS NOT NULL');
        $this->ci->db->having('days_overdue >=', self::QUANTIDADE_DIAS_ENVIAR_EMAIL_SUCESSIVOS);

        $query    = $this->ci->db->get();
        $invoices = $query->result();

        if (empty($invoices)) {
            return false;
        }

        $enviar_email = false;

        $biNextMailingDay = null;

        foreach ($invoices as $invoice) {

            $biNextMailingDay = $invoice->aviso_suspensao_proximo_dia_util ?? $invoice->duedate;

            $dia_para_enviar = $this->ci->business_day_calculator->getNextBusinessDaySuspensionNotice($biNextMailingDay);

            if ($dia_para_enviar == $data_servidor) {
                $enviar_email = true;
            }

            $this->updateInvoiceSuspensionNotice($invoice->id, date('Y-m-d', strtotime($biNextMailingDay)));
        }
        if ($enviar_email && $data_servidor >= $biNextMailingDay) {
            $this->enviarEmailAvisoSuspensao($invoices);
        }
    }


    private function enviarEmailAvisoSuspensao($invoices)
    {
        foreach ($invoices as $invoice) {

            $this->ci->db->where('id', $invoice->id)->update(db_prefix() . 'invoices', ['aviso_suspensao_enviado_at' => date('Y-m-d H:i:s')]);

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
                $boleto         = null;

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

                    $emailtemplate = mail_template('aviso_suspensao', 'banco_inter', $data);

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
                            log_activity("[Fatura ID {$invoice->id}]. Aviso de Suspensão | Email: " . $contact['email']);
                        }
                    }
                }
            }
        }
    }

    private function updateInvoiceSuspensionNotice($id, $next_mailing_day)
    {
        $this->ci->db->where('id', $id)->update(db_prefix() . 'invoices', ['aviso_suspensao_proximo_dia_util' => $next_mailing_day]);
    }
}
