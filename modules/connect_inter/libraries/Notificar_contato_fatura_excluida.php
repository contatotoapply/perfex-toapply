<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/../vendor/autoload.php');

class Notificar_contato_fatura_excluida extends App_Model
{

    protected $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library(['banco_inter/enviar_boleto_pdf_banco_inter']);
    }

    public function enviarNotificacaoViaEmail($invoice_id)
    {
        $invoice = $this->invoices_model->get($invoice_id);

        if ($invoice->status != 6) {

            // $options_notifications = get_option("CONFIG_NAME<!--->_config_contact_responsible_billing_notifications");

            // $notifactions = unserialize($options_notifications);

            // $this->ci->db->where('userid', $invoice->client->userid);
            // $this->ci->db->group_start();
            // $this->ci->db->where('is_primary', 1);
            // foreach ($notifactions as $notification) {
            //     $this->ci->db->or_where($notification, 1);
            // }
            // $this->ci->db->group_end();
            // $query = $this->ci->db->get(db_prefix() . 'contacts');

            // $contacts = $query->result_array();

            // if (!empty($contacts)) {

            //     foreach ($contacts as $contact) {

            //         $contact['invoice'] = (array) $invoice;

            //         $contact['client']  = (array) $invoice->client;

            //         $mailtemplate = mail_template('emaill_notifica_contato_fatura_excluida', 'banco_inter', $contact);

            //         $invoiceNumber  = format_invoice_number($invoice->id);

            //         // ANEXOS
            //         $invoice        = $this->ci->invoices_model->get($invoice->id);
            //         $pdf = invoice_pdf($invoice);
            //         $attach = $pdf->Output($invoiceNumber  . '.pdf', 'S');

            //         $mailtemplate->add_attachment([
            //             'attachment' => $attach,
            //             'filename'   => str_replace('/', '-', $invoiceNumber  . '.pdf'),
            //             'type'       => 'application/pdf',
            //         ]);

            //         if (get_option('paymentmethod_banco_inter_enviar_boleto_email')) {
            //             $mailtemplate->add_attachment($this->ci->enviar_boleto_pdf_banco_inter->getBoletoBancoInter($invoice));
            //         }

            //         if ($mailtemplate->send()) {
            //             log_activity("Enviou email para o contato ao cancelar ou excluir uma fatura." . $contact['email'] . ' [Invoice ID: ' . $invoice_id . ']');
            //         }
            //     }
            // }
        }
    }
}
