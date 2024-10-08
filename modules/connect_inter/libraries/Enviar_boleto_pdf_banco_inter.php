<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(FCPATH . 'modules/connect_inter/vendor/autoload.php');

use ctodobom\APInterPHP\BancoInter;
use ctodobom\APInterPHP\TokenRequest;

class Enviar_boleto_pdf_banco_inter
{
    protected $ci;
    protected $banco;
    protected $gt_inter;

    // Anexar o retorno dessa função ao envio de boletos
    public function getBoletoBancoInter($invoice)
    {
        $this->ci = &get_instance();

        $this->gt_inter = $this->ci->inter_gateway;

        try {

            $hash      = get_option('connect_inter_ssl_file_hash');
            $cert_file = CONNECT_INTER_MODULE_NAME_UPLOADS_FOLDER . "ssl_files/crt_{$hash}.crt";
            $key_file  = CONNECT_INTER_MODULE_NAME_UPLOADS_FOLDER . "ssl_files/key_{$hash}.key";

            if (!file_exists($key_file) || !file_exists($cert_file)) {
                return;
            }

            $this->banco = new BancoInter(
                "123456",
                $cert_file,
                $key_file,
                new TokenRequest(
                    $this->gt_inter->getSetting('inter_client_id'),
                    $this->gt_inter->getSetting('inter_client_secret'),
                    'boleto-cobranca.read boleto-cobranca.write'
                )
            );
        } catch (\Exception $th) {
            log_activity('error 1: ' . $th->getMessage());
        }

        try {

            if (
                $invoice->bi_nosso_numero
                || $invoice->banco_inter_codigo_solicitacao
            ) {

                $banco_inter_cob_id = $invoice->bi_nosso_numero;

                if ($invoice->datecreated > date('2024-09-17 00:00:00')) {
                    $banco_inter_cob_id = $invoice->banco_inter_codigo_solicitacao;
                }

                $attach = base64_decode($this->banco->getPdfBoletoBase64($banco_inter_cob_id));

                $invoice_number = str_replace('/', '-', format_invoice_number($invoice->id));

                $attachment = [
                    'attachment' =>  $attach,
                    'filename'   => "BOLETO_{$invoice_number}.pdf",
                    'type'       => 'application/pdf',
                ];

                return $attachment;
            }
        } catch (\Exception $th) {
            //throw $th;
        }

        return [];
    }
}
