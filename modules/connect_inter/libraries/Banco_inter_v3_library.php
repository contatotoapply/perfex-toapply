<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/../vendor/autoload.php');

use ctodobom\APInterPHP\BancoInter;
use ctodobom\APInterPHP\TokenRequest;
use ctodobom\APInterPHP\BancoInterException;

class Banco_inter_v3_library
{

    const BASE_URL_SANDBOX = 'https://cdpj-sandbox.partners.uatinter.co';
    const BASE_URL_PRODUCAO = 'https://cdpj.partners.bancointer.com.br';

    public    $banco;
    protected $ci;
    protected $invoice;
    protected $gt_inter;
    protected $bi_active_log;
    protected $hoje;
    protected $is_sandbox;
    protected $base_url;

    public function __construct()
    {
        $this->ci = &get_instance();

        $this->gt_inter = $this->ci->inter_gateway;

        try {
            if ($this->ci->app_modules->is_active('connect_inter')) {

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
                        'extrato.read boleto-cobranca.read boleto-cobranca.write pagamento-boleto.write pagamento-boleto.read pagamento-darf.write cob.write cob.read cobv.write cobv.read pix.write pix.read webhook.read webhook.write payloadlocation.write payloadlocation.read pagamento-pix.write pagamento-pix.read webhook-banking.write webhook-banking.read'
                    )
                );

                $this->is_sandbox = get_option('paymentmethod_connect_inter_test_mode_enabled') ? true : false;

                $this->base_url = $this->is_sandbox
                    ? $this->base_url = self::BASE_URL_SANDBOX
                    : $this->base_url = self::BASE_URL_PRODUCAO;

                $this->banco->setApiBaseURL($this->base_url);

            }
        } catch (\Throwable $th) {
            log_activity('Inter_library->__construct:' . $th->getMessage());
        }

        $this->ci->load->model('invoices_model');
    }

    /**
     * @param mixed $data
     * @param mixed $invoice
     *
     * @return [type]
     */
    public function emitirCobranca($data, $invoice)
    {
        try {
            $reply = $this->banco->controllerPostWithJson('/cobranca/v3/cobrancas', $data);

            if ($reply->body) {
                return $reply->body;
            }
            return false;
        } catch (\Throwable $th) {
            log_activity('[BANCO INTER V3] ERROR - Erro ao tentar CRIAR o pix para a fatura. Fatura ID: ' . $invoice->id . ' Mensagem:' . $th->reply->body);
        }
    }


    /**
     * @param mixed $codigoSolicitacao
     *
     * @return [type]
     */
    public function getCobranca($codigoSolicitacao)
    {
        $response = $this->banco->controllerGet("/cobranca/v3/cobrancas/" . $codigoSolicitacao);

        if (!$response->body) {
            throw new BancoInterException('Erro ao receber dados ', 0, $response);
        }

        $response = json_decode($response->body);

        return $response;
    }

    /**
     * @param string $codigoSolicitacao
     *
     * @return [type]
     */
    public function mostraPdfBrowser(string $codigoSolicitacao)
    {
        header('Content-Type: application/pdf');
        $reply = $this->getPdfBoletoBase64($codigoSolicitacao);
        return base64_decode($reply);
    }

    /**
     * @param string $codigoSolicitacao
     *
     * @return [type]
     */
    public function baixarPdf(string $codigoSolicitacao)
    {
        $reply = $this->getPdfBoletoBase64($codigoSolicitacao);
        return base64_decode($reply);
    }

    /**
     * Faz download do PDF do boleto e retorna apenas o conteúdo binário
     * codificado em string base64
     *
     * @param  string $nossoNumero
     * @throws BancoInterException
     * @return string Conteúdo do PDF codificado em string base64
     */
    public function getPdfBoletoBase64(string $codigoSolicitacao): string
    {
        $reply = $this->banco->controllerGet("/cobranca/v3/cobrancas/{$codigoSolicitacao}/pdf");

        if (!$reply->body) {

            throw new BancoInterException('Erro ao receber o PDF', 0, $reply);
        }

        return json_decode($reply->body)->pdf;
    }

    /**
     * @param string $codigoSolicitacao
     *
     * @return [type]
     */
    public function cancelarBoleto(string $codigoSolicitacao)
    {
        if (!is_null($codigoSolicitacao)) {
            try {

                $data = ['motivoCancelamento' => 'A pedido do cliente'];

                $reply = $this->banco->cancelarBoleto("/cobranca/v3/cobrancas/{$codigoSolicitacao}/cancelar", $data);

                if (isset($reply['http_code'])) {
                    return $reply['http_code'] == 202;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
            }
        }
    }

    /**
     * @param mixed $url
     *
     * @return [type]
     */
    public function createWebhook($url)
    {

        $result = $this->banco->controllerPut('/cobranca/v3/cobrancas/webhook', [
            'webhookUrl' => $url
        ]);

        return $result;
    }

    /**
     * @return [type]
     */
    public function getWebhookCadastrado()
    {
        $result = $this->banco->controllerGet('/cobranca/v3/cobrancas/webhook');

        $response = $result->body;
        if ($response) {
            return json_decode($response);
        }
        return null;
    }
}
