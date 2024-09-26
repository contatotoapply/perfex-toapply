<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Asaas_invoice_callback extends ClientsController
{
    const STATUS_INVOICE_AUTHORIZED = 'INVOICE_AUTHORIZED';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('connect_asaas/asaas_gateway');
        $this->load->model('invoices_model');
    }

    public function index()
    {

        log_activity('[ASAAS]: Callback de NFSe.');

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0) {

            $response          = trim(file_get_contents("php://input"));
            log_activity('[ASAAS]: Callback de NFSe: ' . $response);
            $content           = json_decode($response);

            if (!$content) {
                echo 'Asaas: Falha ao receber callback';
                return;
            }

            $event = $content->event;

            switch ($event) {

                case  self::STATUS_INVOICE_AUTHORIZED:

                    log_activity('[ASAAS]: Callback de NFSe - INVOICE_AUTHORIZED');

                    $externalReference = $content->invoice->externalReference;

                    $pdfUrl            = $content->invoice->pdfUrl;

                    $xmlUrl            = $content->invoice->xmlUrl;

                    $invoice = $this->db->where('hash', $externalReference)
                        ->get(db_prefix() . 'invoices')->row();

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

                    $path = get_upload_path_by_type('invoice') . $invoice->id . '/';

                    _maybe_create_upload_path($path);

                    if (!$invoice) {
                        log_activity('Asaas: Emissão de nota fiscal avulsa.');
                        echo 'Asaas: Emissão de nota fiscal avulsa.';
                    } else {

                        $pdf_gerado = $this->gerar_pdf($pdfUrl, $invoice, $externalReference, $path);

                        $xml_gerada = $this->gerar_xml($xmlUrl, $invoice, $externalReference, $path);

                        if ($pdf_gerado || $xml_gerada) {

                            $this->invoices_model->log_invoice_activity($invoice->id, 'invoice_activity_added_attachment');

                            log_activity('Asaas: Emissão de nota fiscal para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference);

                            echo 'Asaas: Emissão de nota fiscal para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference;
                        } else {

                            log_activity('Asaas: Falha ao gerar nota fiscal para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference);

                            echo 'Asaas: Falha ao gerar nota fiscal para a fatura ' . $invoice->id . ', com o ID: ' . $externalReference;
                        }
                    }

                    break;

                default:
                    # code...
                    break;
            }
        }
        echo 'OK: GET';
    }

    private function gerar_pdf($pdfUrl, $invoice, $externalReference, $path)
    {
        if ($pdfUrl) {

            $invoice_pdf = file_get_contents($pdfUrl);

            file_put_contents($path . $externalReference . '.pdf', $invoice_pdf);

            $attachment = [
                [
                    'contact_id' => 0,
                    'file_name'  => $externalReference . '.pdf',
                    'filetype'   => 'application/pdf',
                ]
            ];

            $this->misc_model->add_attachment_to_database($invoice->id, 'invoice', $attachment);

            return true;
        }

        return false;
    }

    private function gerar_xml($xmlUrl, $invoice, $externalReference, $path)
    {
        if ($xmlUrl) {

            $invoice_pdf = file_get_contents($xmlUrl);

            file_put_contents($path . $externalReference . '.xml', $invoice_pdf);

            $attachment = [
                [
                    'contact_id' => 0,
                    'file_name'  => $externalReference . '.xml',
                    'filetype'   => 'text/xml',
                ]
            ];

            $this->misc_model->add_attachment_to_database($invoice->id, 'invoice', $attachment);

            return true;
        }

        return false;
    }
}
