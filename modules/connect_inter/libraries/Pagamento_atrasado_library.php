<?php


defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/../vendor/autoload.php');

class Pagamento_atrasado_library
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
        $this->ci->load->library([
            'parser',
            'connect_inter/feriado_library', 'connect_inter/enviar_boleto_pdf_banco_inter'
        ]);
        $this->bi_active_log = get_option('banco_inter_active_log');
        $this->ci->load->model('invoices_model');
        if($this->ci->session->userdata('hoje')){
            $this->hoje = $this->ci->session->userdata('hoje');
        }
        else{
            $this->hoje = date('Y-m-d');
        }
    }


    private function enviarEmailCobrancaFaturasRecorrentesAtrasadas()
    {
        $hoje = $this->hoje;

        $invoices = $this->ci->db->select('id,is_recurring_from,hash,duedate,clientid,default_language,bi_next_mailing_day,bi_nosso_numero')
            ->from(db_prefix() . 'invoices')
            ->join('tblclients', 'tblinvoices.clientid = tblclients.userid')
            ->where('duedate IS NOT NULL')
            ->where('(is_recurring_from IS NOT NULL OR recurring != 0)')
            ->where('(email_cobranca_enviado_recorrente_at IS NULL OR bi_next_mailing_day IS NOT NULL)')
            ->where('duedate <=', $hoje)
            ->where_not_in('status', [Invoices_model::STATUS_PAID, Invoices_model::STATUS_CANCELLED, Invoices_model::STATUS_DRAFT])
            ->where('bi_next_mailing_day <=', $hoje)
            ->get(NULL)
            ->result();

        if (empty($invoices)) {
            return;
        }

        if (date('H') >= self::HORA_ENVIO_EMAIL_COBRANCA_RECORRENTE) {

            foreach ($invoices as $invoice) {
                $contacts = $this->ci->db
                    ->select('id,userid,firstname,lastname,email,invoice_emails,active')
                    ->where('userid', $invoice->clientid)
                    ->where('invoice_emails', 1)
                    ->where('active', 1)
                    ->get(db_prefix() . 'contacts')->result_array();

                if (!empty($contacts)) {

                    // Problema aqui no-subject. Slug: invoice-enviado-para-cliente-pagamento-atrasado
                    $emailtemplate = $this->ci->db
                        ->where('slug', 'send-invoice-one-business-day-after-duedate')
                        ->where('language', $invoice->default_language)
                        ->get(db_prefix() . 'emailtemplates')->row();

                    $enviou_email_arr = [];

                    foreach ($contacts as $contact) {
                        // Pega o Id do Custom Field do Email de Cobrança
                        $invoice_number = format_invoice_number($invoice->id);

                        // ANEXOS
                        $invoice        = $this->ci->invoices_model->get($invoice->id);
                        $pdf = invoice_pdf($invoice);
                        $attach = $pdf->Output($invoice_number . '.pdf', 'S');

                    }

                    $hoje = date('Y-m-d H:i:s', strtotime($this->hoje . date('H:i:s')));

                    $dataMais2Dias = date('Y-m-d H:i:s', strtotime('+2 days', strtotime($hoje)));

                    foreach ($enviou_email_arr as $invoice_id) {

                        $res = $this->ci->db->where('id', $invoice_id)
                            ->update(
                                db_prefix() . 'invoices',
                                [
                                    'email_cobranca_enviado_recorrente_at' => $hoje,
                                    'bi_next_mailing_day'                  => $dataMais2Dias
                                ]
                            );

                        log_activity('Enviou email faturas recorrentes: ' . $hoje . "|" . count($invoices) . "||" . $res);
                    }
                }
            }
        }
    }

    public function agendarEmail()
    {
        $hoje = $this->hoje;

        $invoices = $this->ci->db
            ->select('id,status,recurring,bi_next_mailing_day,email_cobranca_enviado_recorrente_at,
                recurring_type,custom_recurring,is_recurring_from,duedate,clientid,default_language,bi_nosso_numero')
            ->from(db_prefix() . 'invoices')
            ->join('tblclients', 'tblinvoices.clientid = tblclients.userid')
            ->where('duedate IS NOT NULL')
            ->where("(is_recurring_from IS NOT NULL OR recurring != 0)")
            ->where('status', Invoices_model::STATUS_OVERDUE)
            ->where('(email_cobranca_enviado_recorrente_at IS NULL OR bi_next_mailing_day IS NOT NULL)')
            ->where('duedate <=', $hoje)
            ->get(NULL)
            ->result();

        if (empty($invoices)) {
            return false;
        }

        $enviar_email = true;

        foreach ($invoices as $invoice) {

            $bi_next_mailing_day = $invoice->bi_next_mailing_day ?? $invoice->duedate;

            $weekday = date('w', strtotime($bi_next_mailing_day));

            if ($data = $this->ci->feriado_library->eFeriado($bi_next_mailing_day)) {
                if ($dia_add = $data['e_carnaval']) {
                    $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime("$bi_next_mailing_day +$dia_add days"));
                } else {
                    switch ($weekday) {
                        case self::QUINTA:
                            $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime("$bi_next_mailing_day +1 day"));
                            break;
                        case self::SEXTA:
                            $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime("$bi_next_mailing_day +4 days"));
                            break;
                        case self::SABADO:
                            $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime("$bi_next_mailing_day +3 days"));
                            break;
                        default:
                            $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime("$bi_next_mailing_day +1 day"));
                    }
                }
                $enviar_email = false;
            } elseif (in_array($weekday, [self::SABADO, self::DOMINGO])) {
                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime("$bi_next_mailing_day next Tuesday"));
                $enviar_email = false;
            } elseif ($weekday == self::SEXTA) {
                $bi_next_mailing_day = date('Y-m-d', strtotime("$bi_next_mailing_day next Monday"));
                $enviar_email = false;
            }

            $this->updateInvoiceMailingDay($invoice->id, $bi_next_mailing_day);
        }

        if ($enviar_email) {
            $this->enviarEmailCobrancaFaturasRecorrentesAtrasadas();
        }
    }

    private function updateInvoiceMailingDay($id, $next_mailing_day)
    {
        $this->ci->db->where('id', $id)->update(db_prefix() . 'invoices', ['bi_next_mailing_day' => $next_mailing_day]);
    }
}
