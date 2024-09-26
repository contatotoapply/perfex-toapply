<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Asaas_invoice_callback extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('connect_asaas/asaas_gateway');
    }

    public function index()
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0) {

            $response          = trim(file_get_contents("php://input"));
            $content           = json_decode($response);
            if (!$content) {
                echo 'Asaas: Falha ao receber callback';
                return;
            }
            $externalReference = $content->invoice->externalReference;
            $pdfUrl            = $content->invoice->pdfUrl;
            $xmlUrl            = $content->invoice->xmlUrl;

            $CI = &get_instance();

            $this->db->where('hash', $externalReference);
            $invoice = $this->db->get('tblinvoices')->row();


            if ($xmlUrl) {
                $invoice_xml = file_get_contents($xmlUrl);

                file_put_contents(FCPATH . 'modules/connect_asaas/files/xml/' . $externalReference . '.xml', $invoice_xml);

                $invoice_xml_file = pathinfo(FCPATH . 'modules/connect_asaas/files/xml/' . $externalReference . '.xml');

                $type = mime_content_type(FCPATH . 'modules/connect_asaas/files/xml/' . $externalReference . '.xml');

                $filename = $invoice_xml_file['filename'];

                $data = [
                    "invoice_id"                => $content->invoice->id,
                    "status"                    => $content->invoice->status,
                    "customer"                  => $content->invoice->customer,
                    "type"                      => $content->invoice->type,
                    "statusDescription"         => $content->invoice->statusDescription,
                    "serviceDescription"        => $content->invoice->serviceDescription,
                    "pdfUrl"                    => $content->invoice->pdfUrl,
                    "xmlUrl"                    => $content->invoice->xmlUrl,
                    "rpsSerie"                  => $content->invoice->rpsSerie,
                    "rpsNumber"                 => $content->invoice->rpsNumber,
                    "number"                    => $content->invoice->number,
                    "validationCode"            => $content->invoice->validationCode,
                    "value"                     => $content->invoice->value,
                    "deductions"                => $content->invoice->deductions,
                    "effectiveDate"             => $content->invoice->effectiveDate,
                    "observations"              => $content->invoice->observations,
                    "estimatedTaxesDescription" => $content->invoice->estimatedTaxesDescription,
                    "payment"                   => $content->invoice->payment,
                    "installment"               => $content->invoice->installment,
                    "externalReference"         => $content->invoice->externalReference,
                ];

                $this->db->insert(db_prefix() . 'asaas_invoice_files', $data);

                if (!$invoice) {
                    log_activity('Asaas: Emissão de nota fiscal avulsa.');
                    echo 'Asaas: Emissão de nota fiscal avulsa.';
                }

                if ($invoice) {

                    $path = get_upload_path_by_type('invoice') . $invoice->id . '/';

                    _maybe_create_upload_path($path);

                    file_put_contents($path . '.xml', $invoice_xml);

                    $attachment = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype' => $type,
                    ];

                    $insert_id = $CI->misc_model->add_attachment_to_database($invoice->id, 'invoice', $attachment);

                    $CI->load->model('invoices_model');
                    $CI->invoices_model->log_invoice_activity($invoice->id, 'invoice_activity_added_attachment');

                    log_activity('Asaas: Emissão de nota fiscal para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference);
                    echo 'Asaas: Emissão de nota fiscal para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference;
                }
            }

            if ($pdfUrl) {
                $invoice_pdf = file_get_contents($pdfUrl);

                file_put_contents(FCPATH . 'modules/connect_asaas/files/pdf/' . $externalReference . '.pdf', $invoice_pdf);

                $invoice_pdf_file = pathinfo(FCPATH . 'modules/connect_asaas/files/pdf/' . $externalReference . '.pdf');

                //   $type = $invoice_pdf_file['extension'];

                $type = mime_content_type(FCPATH . 'modules/connect_asaas/files/pdf/' . $externalReference . '.pdf');

                $filename = $invoice_pdf_file['filename'];

                $data = [
                    "invoice_id"                => $content->invoice->id,
                    "status"                    => $content->invoice->status,
                    "customer"                  => $content->invoice->customer,
                    "type"                      => $content->invoice->type,
                    "statusDescription"         => $content->invoice->statusDescription,
                    "serviceDescription"        => $content->invoice->serviceDescription,
                    "pdfUrl"                    => $content->invoice->pdfUrl,
                    "xmlUrl"                    => $content->invoice->xmlUrl,
                    "rpsSerie"                  => $content->invoice->rpsSerie,
                    "rpsNumber"                 => $content->invoice->rpsNumber,
                    "number"                    => $content->invoice->number,
                    "validationCode"            => $content->invoice->validationCode,
                    "value"                     => $content->invoice->value,
                    "deductions"                => $content->invoice->deductions,
                    "effectiveDate"             => $content->invoice->effectiveDate,
                    "observations"              => $content->invoice->observations,
                    "estimatedTaxesDescription" => $content->invoice->estimatedTaxesDescription,
                    "payment"                   => $content->invoice->payment,
                    "installment"               => $content->invoice->installment,
                    "externalReference"         => $content->invoice->externalReference,
                ];

                $this->db->insert(db_prefix() . 'asaas_invoice_files', $data);

                if (!$invoice) {
                    log_activity('Asaas: Emissão de nota fiscal avulsa.');
                    echo 'Asaas: Emissão de nota fiscal avulsa.';
                }

                if ($invoice) {

                    $path = get_upload_path_by_type('invoice') . $invoice->id . '/';

                    _maybe_create_upload_path($path);

                    file_put_contents($path . $externalReference . '.pdf', $invoice_pdf);

                    $attachment = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype' => $type,
                    ];

                    $CI->misc_model->add_attachment_to_database($invoice->id, 'invoice', $attachment);

                    $CI->load->model('invoices_model');
                    $CI->invoices_model->log_invoice_activity($invoice->id, 'invoice_activity_added_attachment');

                    log_activity('Asaas: Emissão de nota fiscal para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference);
                    echo 'Asaas: Emissão de nota fiscal para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference;
                }
            }
        }
        echo 'OK: GET';
    }
}
