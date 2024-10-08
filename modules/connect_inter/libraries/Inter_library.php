<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/../vendor/autoload.php');

use Carbon\Carbon;
use ctodobom\APInterPHP\BancoInter;
use ctodobom\APInterPHP\TokenRequest;
use ctodobom\APInterPHP\BancoInterException;
use ctodobom\APInterPHP\Cobranca\Boleto;
use ctodobom\APInterPHP\Cobranca\Mora;
use ctodobom\APInterPHP\Cobranca\Multa;
use ctodobom\APInterPHP\Cobranca\Pagador;
use ctodobom\APInterPHP\Cobranca\Mensagem;

class Inter_library
{

    const HORA_ENVIO_EMAIL_COBRANCA            = 7;
    const HORA_ENVIO_EMAIL_COBRANCA_RECORRENTE = 1;
    const LIMITE_COBRANCA_RUN_TASK             = 5;

    const DOMINGO  = 0;
    const SEGUNDA  = 1;
    const TERCA    = 2;
    const QUARTA   = 3;
    const QUINTA   = 4;
    const SEXTA    = 5;
    const SABADO   = 6;

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

        $this->ci->load->helper('connect_inter/connect_inter');
        $this->ci->load->library(['parser', 'connect_inter/enviar_boleto_pdf_banco_inter']);
        $this->bi_active_log = get_option('banco_inter_active_log');
        $this->ci->load->model('invoices_model');
        if ($this->ci->session->userdata('hoje')) {
            $this->hoje = $this->ci->session->userdata('hoje');
        } else {
            $this->hoje = date('Y-m-d');
        }
    }

    public function getBoleto($nossoNumero)
    {
        return $this->banco->getBoleto($nossoNumero);
    }

    /**
     * @param mixed $data
     *
     * @return [type]
     */
    public function createPix2($data, $invoice)
    {
        try {
            $reply = $this->banco->controllerPut('/pix/v2/cobv/' . $data['txid'], $data);
            if ($reply->body) {
                return $reply->body;
            }
            return false;
        } catch (\Throwable $th) {
            log_activity('Erro ao tentar CRIAR o pix para a fatura. Fatura ID: ' . $invoice->id . ' Mensagem:' . $th->reply->body);
        }
    }

    public function emitirCobranca($data, $invoice)
    {
        try {
            $reply = $this->banco->controllerPostWithJson('/cobranca/v3/cobrancas', $data);
            if ($reply->body) {
                return $reply->body;
            }
            return false;
        } catch (\Throwable $th) {
            dd(1, $th->reply->body);
            log_activity('Erro ao tentar CRIAR o pix para a fatura. Fatura ID: ' . $invoice->id . ' Mensagem:' . $th->reply->body);
        }
    }

    /**
     * @param mixed $data
     *
     * @return [type]
     */
    public function findPixByTxid($txid)
    {
        try {
            $reply = $this->banco->controllerGet('/pix/v2/cobv/' . $txid);
            if ($reply->body) {
                return $reply->body;
            }
            return false;
        } catch (\Throwable $th) {
            log_activity('Erro ao tentar CRIAR o pix para a fatura. Fatura Hash: ' . $txid . ' Mensagem:' . $th->reply->body);
        }
    }

    /**
     * @param mixed $data
     *
     * @return [type]
     */
    public function reviewBillingDue($data, $invoice)
    {
        try {
            $reply = $this->banco->controllerPatch('/pix/v2/cobv/' . $data['txid'], $data);
            if ($reply->body) {
                return $reply->body;
            }
            return false;
        } catch (\Throwable $th) {
            log_activity('Erro ao tentar ATUALIZAR o pix para a fatura. Fatura ID: ' . $invoice->id . ' Mensagem:' . $th->reply->body);
        }
    }

    public function mostrarBoleto($nossoNumero)
    {
        try {
            try {
                $pdf = $this->banco->mostraPdfBrowser($nossoNumero);
                echo base64_decode($pdf);
            } catch (BancoInterException $e) {
                set_alert('warning', $e->getMessage());
                log_activity('Inter_library->mostrarBoleto(1):' . $e->getMessage());
                redirect(site_url('invoice/' . $this->invoice->id . '/' . $this->invoice->hash));
            }
            $var = 0 / 10;
        } catch (\Throwable $th) {
            set_alert('warning', 'Mostrar Boleto: ' . $th->getMessage());
            log_activity('Inter_library->mostrarBoleto(2):' . $th->getMessage());
            redirect(site_url('invoice/' . $this->invoice->id . '/' . $this->invoice->hash));
        }
    }

    private function formatDescription($invoice_id): array
    {
        $items =  $this->ci->db
            ->select('id, description,rel_id, long_description')
            ->where('rel_id', $invoice_id)
            ->where('rel_type', 'invoice')
            ->get(db_prefix() . 'itemable')
            ->result_array();

        $descs = [];

        foreach ($items as $key => $item) {
            $descs[] = substr(strip_tags($item['description']), 0, 78);
        }

        return $descs;
    }

    public function criarBoletoCron($invoice)
    {
        $client        = $invoice->client;
        $this->invoice = $invoice;

        if (is_null($client->company)) {
            $this->ci->invoices_model->log_invoice_activity($invoice->id, 'O nome do cliente  não encontrado.');
            return false;
        }

        try {
            if (is_null($invoice->bi_nosso_numero) || !$invoice->bi_nosso_numero) {

                $tipoPessoa = Pagador::PESSOA_FISICA;
                if (is_null($client->vat)) {
                    $this->ci->invoices_model->log_invoice_activity($invoice->id, 'CPF Ou CNPJ inválido');
                    return false;
                }

                $vat = preg_replace('/[.\/-]/', '', $client->vat);

                if (strlen($vat) == 14) {
                    $tipoPessoa = Pagador::PESSOA_JURIDICA;
                }

                $pagador = new Pagador();
                $pagador->setTipoPessoa($tipoPessoa);
                $pagador->setNome($client->company);
                $pagador->setEndereco($client->billing_street);
                $pagador->setNumero(0);
                $pagador->setBairro("Centro");
                $pagador->setCidade($client->billing_city);

                $cep = preg_replace('/[^0-9]/', '', $client->billing_zip);
                $pagador->setCep($cep);
                $pagador->setCpfCnpj($vat);

                $pagador->setUf($client->billing_state);
                $boleto = new Boleto();
                $boleto->setPagador($pagador);

                $number = inter_seu_numero_format($invoice->number, $invoice->number_format, $invoice->prefix, $invoice->date);
                $boleto->setSeuNumero($number);
                $boleto->setValorNominal($invoice->total);
                $boleto->setDataVencimento($invoice->duedate);

                // Multa
                $multa = new Multa();
                $multa->setCodigoMulta(Multa::PERCENTUAL);
                $multa->setTaxa($this->gt_inter->getSetting('b_inter_multa'));
                $multa->setData(Carbon::parse($invoice->duedate)->addDays($this->gt_inter->getSetting('b_inter_qtd_dias_vencimento_multa'))->format('Y-m-d'));
                $boleto->setMulta($multa);

                // Mora
                $mora = new Mora();
                $mora->setCodigoMora(Mora::TAXA_MENSAL);
                $mora->setData(Carbon::parse($invoice->duedate)->addDays($this->gt_inter->getSetting('b_inter_qtd_dias_vencimento_juros'))->format('Y-m-d'));
                $mora->setValor(0);
                $mora->setTaxa($this->gt_inter->getSetting('b_inter_juros'));
                $boleto->setMora($mora);

                if ($this->gt_inter->getSetting('b_inter_mostrar_linhas') === '1') {
                    $message = new Mensagem();
                    $desc = $this->formatDescription($invoice->id);
                    if (isset($desc[0])) {
                        $message->setLinha1($desc[0]);
                    }
                    if (isset($desc[1])) {
                        $message->setLinha2($desc[1]);
                    }
                    if (isset($desc[2])) {
                        $message->setLinha3($desc[2]);
                    }
                    if (isset($desc[3])) {
                        $message->setLinha4($desc[3]);
                    }
                    if (isset($desc[4])) {
                        $message->setLinha5($desc[4]);
                    }
                    $boleto->setMensagem($message);
                }

                // Cria o boleto
                $bi_boleto = json_encode($this->banco->createBoleto($boleto));

                $this->ci->db->where('id', $invoice->id)
                    ->update(db_prefix() . 'invoices', [
                        'bi_nosso_numero' => $boleto->getNossoNumero(),
                        'bi_boleto'       => $bi_boleto
                    ]);
                $this->ci->invoices_model->log_invoice_activity($invoice->id, '[BANCO INTER V3] - SUCCESS- Boleto criado');
                return true;
            }
            return false;
        } catch (BancoInterException $e) {
            $this->ci->invoices_model->log_invoice_activity($invoice->id, '[BANCO INTER V3] - ERROR - ' . $e->reply->body . "<br/><b>DEBUG_BACKTRACE: </b>");
        }
    }

    public function criarBoleto($invoice)
    {
        if (!$this->allowedCreateOrUpdateBancoInterMethod($invoice->id)) {
            return false;
        }

        $client        = $invoice->client;
        $this->invoice = $invoice;
        $invoice_url   = site_url('invoice/' . $invoice->id . '/' . $invoice->hash);

        if (is_null($client->company)) {
            set_alert('warning', "O nome do cliente  não encontrado.");
            redirect($invoice_url);
        }

        try {
            if (is_null($invoice->bi_nosso_numero) || !$invoice->bi_nosso_numero) {

                $tipoPessoa = Pagador::PESSOA_FISICA;
                if (is_null($client->vat)) {
                    set_alert('warning', 'CPF Ou CNPJ inválido');
                    redirect(site_url('invoice/' . $invoice->id . '/' . $invoice->hash));
                }
                $vat = preg_replace('/[.\/-]/', '', $client->vat);

                if (strlen($vat) == 14) {
                    $tipoPessoa = Pagador::PESSOA_JURIDICA;
                }

                $pagador = new Pagador();
                $pagador->setTipoPessoa($tipoPessoa);
                $pagador->setNome($client->company);
                $pagador->setEndereco($client->billing_street);
                $pagador->setNumero(0);
                $pagador->setBairro("Centro");
                $pagador->setCidade($client->billing_city);

                $cep = preg_replace('/[^0-9]/', '', $client->billing_zip);
                $pagador->setCep($cep);
                $pagador->setCpfCnpj($vat);

                $pagador->setUf($client->billing_state);
                $boleto = new Boleto();
                $boleto->setPagador($pagador);

                $number = inter_seu_numero_format($invoice->number, $invoice->number_format, $invoice->prefix, $invoice->date);
                $boleto->setSeuNumero($number);
                $boleto->setValorNominal($invoice->total);
                $boleto->setDataVencimento($invoice->duedate);

                // Multa
                $multa = new Multa();
                $multa->setCodigoMulta(Multa::PERCENTUAL);
                $multa->setTaxa($this->gt_inter->getSetting('b_inter_multa'));
                $multa->setData(Carbon::parse($invoice->duedate)->addDays($this->gt_inter->getSetting('b_inter_qtd_dias_vencimento_multa'))->format('Y-m-d'));
                $boleto->setMulta($multa);

                // Mora
                $mora = new Mora();
                $mora->setCodigoMora(Mora::TAXA_MENSAL);
                $mora->setData(Carbon::parse($invoice->duedate)->addDays($this->gt_inter->getSetting('b_inter_qtd_dias_vencimento_juros'))->format('Y-m-d'));
                $mora->setValor(0);
                $mora->setTaxa($this->gt_inter->getSetting('b_inter_juros'));
                $boleto->setMora($mora);

                if ($this->gt_inter->getSetting('b_inter_mostrar_linhas') === '1') {
                    $message = new Mensagem();
                    $desc = $this->formatDescription($invoice->id);
                    if (isset($desc[0])) {
                        $message->setLinha1($desc[0]);
                    }
                    if (isset($desc[1])) {
                        $message->setLinha2($desc[1]);
                    }
                    if (isset($desc[2])) {
                        $message->setLinha3($desc[2]);
                    }
                    if (isset($desc[3])) {
                        $message->setLinha4($desc[3]);
                    }
                    if (isset($desc[4])) {
                        $message->setLinha5($desc[4]);
                    }
                    $boleto->setMensagem($message);
                }

                // Cria o boleto
                $bi_boleto = json_encode($this->banco->createBoleto($boleto));

                $this->ci->db->where('id', $invoice->id)
                    ->update(db_prefix() . 'invoices', [
                        'bi_nosso_numero' => $boleto->getNossoNumero(),
                        'bi_boleto'       => $bi_boleto
                    ]);

                // $this->mostrarBoleto($boleto->getNossoNumero());
                return true;
            } else {
                $this->mostrarBoleto($invoice->bi_nosso_numero);
            }
            return false;
        } catch (BancoInterException $e) {
            $erro = json_decode($e->reply->body);
            set_alert('warning', 'Criar Boleto 2: ' .  $erro->title);
            log_activity('Inter_library->criarBoleto@' . $e->reply->body . "<br> URL: {$invoice_url}");
            redirect(site_url('invoice/' . $invoice->id . '/' . $invoice->hash));
        }
    }

    public function gerarBoletoCron()
    {

        $invoice = $this->ci->db->select('*')->from(db_prefix() . 'invoices')
            ->where('bi_nosso_numero IS NULL')
            ->where('datecreated >=', get_option('data_banco_inter_inicial'))
            ->where('bi_tentativas_criar_boleto <=', 2)
            ->where('duedate >=', $this->hoje) // Só queremos gerar boletos cuja data de vencimento é maior ou igual a data atual.
            ->where('status', Invoices_model::STATUS_UNPAID) // Só queremos gerar boletos que não foram pagos ainda.
            ->where('allowed_payment_modes LIKE', '%banco_inter%') // Só queremos boletos do banco inter
            ->get()->row();

        if (!is_null($invoice)) {

            $this->ci->db->where('id', $invoice->id)
                ->set(
                    'bi_tentativas_criar_boleto',
                    'bi_tentativas_criar_boleto+1',
                    FALSE
                )
                ->update(db_prefix() . 'invoices');

            $invoice->client = $this->ci->db
                ->select('*')->from(db_prefix() . 'clients')
                ->where('userid', $invoice->clientid)
                ->get()->row();

            if ($invoice->client) {
                if ($this->criarBoletoCron($invoice)) {
                    if ($this->bi_active_log) {
                        $this->ci->invoices_model->log_invoice_activity($invoice->id, '[BANCO INTER V3] - Boleto criado pela CRON');
                    }
                    $this->ci->db->where('id', $invoice->id)
                        ->update(db_prefix() . 'invoices', [
                            'banco_inter_boleto_gerado_at' => date('Y-m-d H:i:s')
                        ]);
                    return true;
                }
                if ($this->bi_active_log) {
                    $this->ci->invoices_model->log_invoice_activity($invoice->id, '[BANCO INTER V3] - ERROR - Boleto Boleto Não criado pela CRON (0)');
                }
            }

            if ($this->bi_active_log) {
                $this->ci->invoices_model->log_invoice_activity($invoice->id, '[BANCO INTER V3] - ERROR - Boleto Boleto Não criado pela CRON (1)');
            }

            return false;
        }
        return false;
    }

    public function adicionar_itens_juros_multas()
    {

        $invoices = $this->ci->db->select('id,duedate,status')
            ->where('status', Invoices_model::STATUS_OVERDUE)
            ->where('duedate <', 'CURRENT_DATE', FALSE)
            ->where('banco_inter_item_adicionado', 0)
            ->get(db_prefix() . 'invoices')
            ->result();

        foreach ($invoices as $invoice) {

            $this->ci->db->insert(db_prefix() . 'itemable', [
                'rel_id'           => $invoice->id,
                'rel_type'         => 'invoice',
                'description'      => 'Multa/Juros por Atraso',
                'long_description' => '',
                'qty'              => '1.00',
                'rate'             => 0,
                'unit'             => '',
                'item_order'       => '2'
            ]);

            $this->ci->db->where('id', $invoice->id)
                ->update(db_prefix() . 'invoices', [
                    'banco_inter_item_adicionado' => 1
                ]);
        }
    }

    public function cancelarBoleto($bi_nosso_numero, $invoice_id)
    {
        if (!is_null($bi_nosso_numero)) {
            $this->banco->baixaBoleto($bi_nosso_numero, 'APEDIDODOCLIENTE');
        }
    }

    public function atualizarBoleto($invoice)
    {
        $client        = $invoice->client;

        $this->invoice = $invoice;

        if (is_null($client->company)) {
            return false;
        }

        try {
            $tipoPessoa = Pagador::PESSOA_FISICA;

            if (is_null($client->vat)) {
                $this->ci->invoices_model->log_invoice_activity($invoice->id, '[BANCO INTER V3] - ERROR(1) - CPF Ou CNPJ inválido');
                return false;
            }

            $vat = preg_replace('/[.\/-]/', '', $client->vat);

            if (strlen($vat) == 14) {
                $tipoPessoa = Pagador::PESSOA_JURIDICA;
            }

            $pagador = new Pagador();
            $pagador->setTipoPessoa($tipoPessoa);
            $pagador->setNome($client->company);
            $pagador->setEndereco($client->billing_street);
            $pagador->setNumero(0);
            $pagador->setBairro("Centro");
            $pagador->setCidade($client->billing_city);

            $cep = preg_replace('/[^0-9]/', '', $client->billing_zip);
            $pagador->setCep($cep);
            $pagador->setCpfCnpj($vat);

            $pagador->setUf($client->billing_state);
            $boleto = new Boleto();
            $boleto->setPagador($pagador);

            $number = inter_seu_numero_format($invoice->number, $invoice->number_format, $invoice->prefix, $invoice->date);
            $boleto->setSeuNumero($number);
            $boleto->setValorNominal($invoice->total);
            $boleto->setDataVencimento($invoice->duedate);

            // Multa
            $multa = new Multa();
            $multa->setCodigoMulta(Multa::PERCENTUAL);
            $multa->setTaxa($this->gt_inter->getSetting('b_inter_multa'));
            $multa->setData(Carbon::parse($invoice->duedate)->addDays($this->gt_inter->getSetting('b_inter_qtd_dias_vencimento_multa'))->format('Y-m-d'));
            $boleto->setMulta($multa);

            // Mora
            $mora = new Mora();
            $mora->setCodigoMora(Mora::TAXA_MENSAL);
            $mora->setData(Carbon::parse($invoice->duedate)->addDays($this->gt_inter->getSetting('b_inter_qtd_dias_vencimento_juros'))->format('Y-m-d'));
            $mora->setValor(0);
            $mora->setTaxa($this->gt_inter->getSetting('b_inter_juros'));
            $boleto->setMora($mora);

            if ($this->gt_inter->getSetting('b_inter_mostrar_linhas') === '1') {
                $message = new Mensagem();
                $desc = $this->formatDescription($invoice->id);
                if (isset($desc[0])) {
                    $message->setLinha1($desc[0]);
                }
                if (isset($desc[1])) {
                    $message->setLinha2($desc[1]);
                }
                if (isset($desc[2])) {
                    $message->setLinha3($desc[2]);
                }
                if (isset($desc[3])) {
                    $message->setLinha4($desc[3]);
                }
                if (isset($desc[4])) {
                    $message->setLinha5($desc[4]);
                }
                $boleto->setMensagem($message);
            }

            // Cria o boleto
            $bi_boleto = json_encode($this->banco->createBoleto($boleto));

            $this->ci->db->where('id', $invoice->id)
                ->update(db_prefix() . 'invoices', [
                    'bi_nosso_numero' => $boleto->getNossoNumero(),
                    'bi_boleto'       => $bi_boleto
                ]);
            $this->ci->invoices_model->log_invoice_activity($invoice->id, '[BANCO INTER V3] - SUCCESS - Boleto criado com sucesso.');
            return true;
        } catch (BancoInterException $e) {
            $this->ci->invoices_model->log_invoice_activity($invoice->id, '[BANCO INTER V3] - ERROR - ' . $e->reply->body);
        }
    }

    public function send_email_updated_invoice($invoice)
    {
        // Criar o email template
        $contacts = $this->ci->clients_model->get_contacts($invoice->clientid, [
            'active' => 1,
            'invoice_emails' => 1,
        ]);

        if (!is_null($contacts)) {

            $invoiceLink    = base_url('invoice/' . $invoice->id . '/' . $invoice->hash);
            $invoiceNumber  = format_invoice_number($invoice->id);
            $invoiceDueDate = _d($invoice->duedate);

            if (get_option('paymentmethod_banco_inter_enviar_boleto_email')) {
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
                $mailtemplate = mail_template('send_email_on_invoice_update', 'banco_inter', $data);
                $mailtemplate->add_attachment([
                    'attachment' => $attach,
                    'filename'   => str_replace('/', '-', $invoiceNumber  . '.pdf'),
                    'type'       => 'application/pdf',
                ]);
                if (get_option('paymentmethod_banco_inter_enviar_boleto_email')) {
                    $mailtemplate->add_attachment($boleto);
                }

                if ($mailtemplate->send()) {
                    log_activity("[Fatura ID: ({$invoice->id})] | Email enviado aos contatos ao atualizar fatura. | Email: " . $contact['email']);
                }
            }
        }
    }

    public function gerarNovoBoleto($invoice, $post)
    {
        $invoice_id = $invoice->id;

        if ($this->allowedCreateOrUpdateBancoInterMethod($invoice_id)) {

            $test_update = strtotime(to_sql_date($post['duedate'])) > strtotime($post['old_duedate']) || ($post['old_total'] != $post['total']);

            $test_update = $test_update || is_null($invoice->bi_boleto);

            if ($invoice->status != 2 && $test_update) {
                if ($invoice->bi_nosso_numero) {
                    $this->banco->baixaBoleto($invoice->bi_nosso_numero, 'APEDIDODOCLIENTE');
                    log_activity('Fatura marcada como cancelado ou excluído: Invoice ID:  ' . $invoice->id);
                    $this->ci->db->where('id', $invoice_id)
                        ->update(db_prefix() . 'invoices', [
                            'bi_nosso_numero' => NULL
                        ]);
                    $this->atualizarBoleto($invoice, $post);
                    log_activity('Invoice atualizada: Invoice ID:  ' . $invoice->id);
                    if ($this->ci->db->affected_rows() > 0) {
                    }
                } else {
                    if ($this->atualizarBoleto($invoice, $post)) {
                    }
                }
            }
        }
    }

    public function gerarBoletoManual($id)
    {
        if ($this->allowedCreateOrUpdateBancoInterMethod($id)) {
            $invoice = $this->ci->invoices_model->get($id);
            $this->criarBoletoCron($invoice);
            return true;
        }
        return false;
    }

    public function allowedCreateOrUpdateBancoInterMethod($invoice_id)
    {
        $row = $this->ci->db->where('id', $invoice_id)
            ->where('allowed_payment_modes LIKE', '%banco_inter%')
            ->get(db_prefix() . 'invoices')->row();
        return !is_null($row);
    }

    private function dias_feriados($ano = null)
    {
        if ($ano === null) {
            $ano = intval(date('Y'));
        }

        $pascoa     = easter_date($ano);
        $dia_pascoa = date('j', $pascoa);
        $mes_pascoa = date('n', $pascoa);
        $ano_pascoa = date('Y', $pascoa);

        $feriados = array(
            // Datas Fixas dos feriados brasileiros
            'Ano Novo'                 => ['time' => mktime(0, 0, 0, 1,  1,   $ano), 'e_carnaval' => false], // Confraternização Universal - Lei nº 662, de 06/04/49
            'Tiradentes'               => ['time' => mktime(0, 0, 0, 4,  21,  $ano), 'e_carnaval' => false], // Tiradentes - Lei nº 662, de 06/04/49
            'Dia do Trabalhador'       => ['time' => mktime(0, 0, 0, 5,  1,   $ano), 'e_carnaval' => false], // Dia do Trabalhador - Lei nº 662, de 06/04/49
            'Independência do Brasil'  => ['time' => mktime(0, 0, 0, 9,  7,   $ano), 'e_carnaval' => false], // Dia da Independência - Lei nº 662, de 06/04/49
            'Nossa Senhora Aparecida'  => ['time' => mktime(0, 0, 0, 10,  12, $ano), 'e_carnaval' => false], // N. S. Aparecida - Lei nº 6802, de 30/06/80
            'Finados'                  => ['time' => mktime(0, 0, 0, 11,  2,  $ano), 'e_carnaval' => false], // Todos os santos - Lei nº 662, de 06/04/49
            'Proclamação da República' => ['time' => mktime(0, 0, 0, 11, 15,  $ano), 'e_carnaval' => false], // Proclamação da republica - Lei nº 662, de 06/04/49
            'Natal'                    => ['time' => mktime(0, 0, 0, 12, 25,  $ano), 'e_carnaval' => false], // Natal - Lei nº 662, de 06/04/49
            'Segunda de Carnaval'      => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 48,  $ano_pascoa), 'e_carnaval' => 2], //2ºferia Carnaval
            'Terça de Carnaval'        => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 47,  $ano_pascoa), 'e_carnaval' => 1], //3ºferia Carnaval
            'Sexta-feira da Paixão'    => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 2,  $ano_pascoa), 'e_carnaval' => false], //6ºfeira Santa
            'Páscoa'                   => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa,  $ano_pascoa), 'e_carnaval' => false], //Pascoa
            'Corpus Christi'           => ['time' => mktime(0, 0, 0, $mes_pascoa, $dia_pascoa + 60,  $ano_pascoa), 'e_carnaval' => false], //Corpus Cirist
        );

        asort($feriados);

        return $feriados;
    }

    private function eFeriado($duedate)
    {
        $dias_feriados = $this->dias_feriados();

        foreach ($dias_feriados as $data) {
            if ($duedate == date("Y-m-d", $data['time'])) return $data;
        }
        return false;
    }

    public function agendarEmailCobrancaFaturasRecorrentes()
    {

        $invoices = $this->ci->db->select('id,is_recurring_from,duedate,clientid,default_language')
            ->from(db_prefix() . 'invoices')
            ->join('tblclients', 'tblinvoices.clientid = tblclients.userid')
            ->where('(is_recurring_from IS NOT NULL OR recurring != 0)')
            ->where('duedate IS NOT NULL')
            ->where_not_in('status', [Invoices_model::STATUS_PAID, Invoices_model::STATUS_CANCELLED, Invoices_model::STATUS_DRAFT])
            ->where('email_cobranca_enviado_recorrente_at', null)
            ->where('duedate <', $this->hoje)
            ->get(NULL)
            ->result();

        if (count($invoices)) {

            $table_invoice_name = db_prefix() . 'invoices';

            foreach ($invoices as $invoice) {

                $duedate = $invoice->duedate;

                $weekday = date('w', strtotime($duedate));

                $bi_next_mailing_day = null;

                if ($data = $this->eFeriado($duedate)) {
                    if ($dia_add = $data['e_carnaval']) {
                        $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+$dia_add days"));
                    } else {
                        switch ($weekday) {
                            case self::DOMINGO:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+1 days"));
                            case self::SEGUNDA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+1 days"));
                                break;
                            case self::TERCA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+1 days"));
                                break;
                            case self::QUARTA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+1 days"));
                                break;
                            case self::QUINTA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+4 days"));
                                break;
                            case self::SEXTA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+4 days"));
                                break;
                            case self::SABADO:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+3 days"));
                                break;
                        }
                    }
                } else if (in_array($weekday, [self::SABADO, self::DOMINGO])) {
                    if ($weekday == self::DOMINGO) {
                        $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . " next Tuesday"));
                    }
                    if ($weekday == self::SABADO) {
                        $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . " next Tuesday"));
                    }
                } else if ($weekday == self::SEXTA) {
                    $bi_next_mailing_day = date('Y-m-d', strtotime($duedate . " next Tuesday"));
                } else {
                    $bi_next_mailing_day = date('Y-m-d', strtotime($duedate));
                }

                $this->ci->db->where('id', $invoice->id)
                    ->update(
                        $table_invoice_name,
                        ['bi_next_mailing_day' => $bi_next_mailing_day]
                    );
            }
            $this->sendEmailCobrancaFaturasRecorrentes();
        }
    }


    public function enviarEmailE2DiasAposVencimentoFaturasRecorrentesOld()
    {
        $invoices = $this->ci->db->select('id,status,recurring,recurring_type,custom_recurring,is_recurring_from,duedate,clientid,default_language')
            ->from(db_prefix() . 'invoices')
            ->join('tblclients', 'tblinvoices.clientid = tblclients.userid')
            ->where('duedate IS NOT NULL')
            ->where("(is_recurring_from IS NOT NULL OR recurring != 0)")
            ->where_not_in('status', [Invoices_model::STATUS_PAID, Invoices_model::STATUS_CANCELLED, Invoices_model::STATUS_DRAFT])
            ->where('email_cobranca_enviado_recorrente_at', null)
            ->where('duedate <', $this->hoje)
            ->get(NULL)
            ->result();

        if (!empty($invoices)) {

            $table_invoice_name = db_prefix() . 'invoices';

            $enviar_email = true;

            foreach ($invoices as $invoice) {

                $duedate = $invoice->duedate;

                $weekday = date('w', strtotime($duedate));

                $bi_next_mailing_day = null;

                if ($data = $this->eFeriado($duedate)) {
                    if ($dia_add = $data['e_carnaval']) {
                        $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+$dia_add days"));
                    } else {
                        switch ($weekday) {
                            case self::DOMINGO:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+1 days"));
                            case self::SEGUNDA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+1 days"));
                                break;
                            case self::TERCA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+1 days"));
                                break;
                            case self::QUARTA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+1 days"));
                                break;
                            case self::QUINTA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+4 days"));
                                break;
                            case self::SEXTA:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+4 days"));
                                break;
                            case self::SABADO:
                                $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . "+3 days"));
                                break;
                        }
                    }
                    $enviar_email = false;
                } else if (in_array($weekday, [self::SABADO, self::DOMINGO])) {
                    if ($weekday == self::DOMINGO) {
                        $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . " next Tuesday"));
                    }
                    if ($weekday == self::SABADO) {
                        $bi_next_mailing_day = date('Y-m-d H:i:s', strtotime($duedate . " next Tuesday"));
                    }
                    $enviar_email = false;
                } else if ($weekday == self::SEXTA) {
                    $bi_next_mailing_day = date('Y-m-d', strtotime($duedate . " next Tuesday"));
                    $enviar_email = false;
                } else {
                    $bi_next_mailing_day = date('Y-m-d', strtotime($duedate));
                }

                $this->ci->db->where('id', $invoice->id)
                    ->update(
                        $table_invoice_name,
                        ['bi_next_mailing_day' => $bi_next_mailing_day]
                    );
            }
            if ($enviar_email) {
                $this->sendEmailCobrancaFaturasRecorrentes();
            }
        }
    }


    private function sendEmailCobrancaFaturasRecorrentes()
    {
        $hoje = $this->hoje;

        $invoices = $this->ci->db->select('id,is_recurring_from,hash,duedate,clientid,default_language,bi_next_mailing_day,bi_nosso_numero')
            ->from(db_prefix() . 'invoices')
            ->join('tblclients', 'tblinvoices.clientid = tblclients.userid')
            ->where('(is_recurring_from IS NOT NULL OR recurring != 0)')
            ->where('email_cobranca_enviado_recorrente_at IS NULL')
            ->where('duedate <', $hoje)
            ->where_not_in('status', [Invoices_model::STATUS_PAID, Invoices_model::STATUS_CANCELLED, Invoices_model::STATUS_DRAFT])
            ->get(NULL)
            ->result();

        if (empty($invoices)) {
            return;
        }

        if (date('H') >= self::HORA_ENVIO_EMAIL_COBRANCA_RECORRENTE) {

            foreach ($invoices as $invoice) {
                // Pegar email de cobranças

                $contacts = $this->ci->db
                    ->select('id,userid,firstname,lastname,email,invoice_emails,active')
                    ->where('userid', $invoice->clientid)
                    ->where('invoice_emails', 1)
                    ->where('active', 1)
                    ->get(db_prefix() . 'contacts')->result_array();

                if (count($contacts)) {

                    $invoiceLink    = base_url('invoice/' . $invoice->id . '/' . $invoice->hash);
                    $invoiceNumber  = format_invoice_number($invoice->id);
                    $invoiceDueDate = _d($invoice->duedate);

                    // ANEXOS
                    $invoice        = $this->ci->invoices_model->get($invoice->id);
                    $pdf = invoice_pdf($invoice);
                    $attach = $pdf->Output($invoiceNumber  . '.pdf', 'S');

                    foreach ($contacts as $contact) {
                        $data = [
                            'contact_firstname' => $contact['firstname'],
                            'email'             => $contact['email'],
                            'invoice_link'      => $invoiceLink,
                            'invoice_number'    => $invoiceNumber,
                            'invoice_duedate'   => $invoiceDueDate
                        ];

                        // Se for do banco inter, enviar boleto.
                        $mailtemplate = mail_template('sua_fatura_vence_hoje', 'banco_inter', $data);

                        $email_ja_enviado = $this->ci->db->where('rel_id', $invoice->id)
                            ->where('rel_type', 'invoice')
                            ->where('email', $contact['email'])
                            ->where('slug', $mailtemplate->slug)
                            ->get(db_prefix() . 'tracked_mails')->row();

                        if (is_null($email_ja_enviado)) {

                            $mailtemplate->add_attachment([
                                'attachment' => $attach,
                                'filename'   => str_replace('/', '-', $invoiceNumber  . '.pdf'),
                                'type'       => 'application/pdf',
                            ]);
                            if (get_option('paymentmethod_banco_inter_enviar_boleto_email')) {
                                $mailtemplate->add_attachment($this->ci->enviar_boleto_pdf_banco_inter->getBoletoBancoInter($invoice));
                            }
                            if ($mailtemplate->send()) {
                                log_activity("Invoice: ({$invoice->id}). Eng. Rev: enviarEmailCobrancaDiaVencimento (2): " . date('Y-m-d H:i:s') . ' - ' . $contact['email']);
                            }
                        }
                    }

                    $hoje = date('Y-m-d H:i:s', strtotime($this->hoje . date('H:i:s')));

                    $dataMais2Dias = date('Y-m-d H:i:s', strtotime('+2 days', strtotime($hoje)));

                    $res = $this->ci->db->where('id', $invoice->id)
                        ->update(
                            db_prefix() . 'invoices',
                            [
                                'email_cobranca_enviado_recorrente_at' => $hoje,
                                'bi_next_mailing_day'                  => $dataMais2Dias
                            ]
                        );

                    log_activity('enviar_email_dia_vencimento: ' . $hoje . "|" . count($invoices) . "||" . $res);
                }
            }
        }
    }

    function updateInvoiceMailingDay($id, $next_mailing_day)
    {
        $this->ci->db->where('id', $id)->update(db_prefix() . 'invoices', ['bi_next_mailing_day' => $next_mailing_day]);
    }

    /**
     * @param mixed $chave
     * @param mixed $data webhookUrl A url do webhook
     *
     * @return [type]
     */
    public function createWebhookPix($chave, $data)
    {
        try {
            $reply = $this->banco->controllerPut('/pix/v2/webhook/' . $chave, $data);
            if ($reply->body) {
                return $reply->body;
            }
            return false;
        } catch (\Throwable $th) {
            log_activity('Erro ao tentar CRIAR o webhook para pix para a fatura. Mensagem:' . $th->reply->body);
        }
    }
    /**
     * @param mixed $chave
     *
     * @return [type]
     */
    public function getWebhookPix($chave)
    {
        try {
            $reply = $this->banco->controllerGet('/pix/v2/webhook/' . $chave);
            if ($reply->body) {
                return $reply->body;
            }
            return false;
        } catch (\Throwable $th) {
            log_activity('Erro ao tentar CRIAR o webhook para pix para a fatura. Mensagem:' . $th->reply->body);
        }
    }
}
