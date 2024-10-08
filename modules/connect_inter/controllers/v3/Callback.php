
<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Callback extends ClientsController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
    }

    public function index()
    {
        $post_callback = json_decode(file_get_contents('php://input'));

        if (!empty($post_callback)) {
            $post_callback = $post_callback[0];

            $event_name = $post_callback->situacao;

            log_activity('[BANCO INTER V3 - Webhook] - DADOS RECEBIDOS: ' . json_encode($post_callback));

            if ($event_name == 'RECEBIDO') {
                $this->pagar($post_callback);
            }

            if ($event_name == 'A_RECEBER') {

                if (
                    isset($post_callback->codigoSolicitacao)
                    && !empty($post_callback->codigoSolicitacao)
                ) {

                    $codigoSolicitacao = $post_callback->codigoSolicitacao;

                    $row = $this->db->select('id,banco_inter_dados_cobranca')
                        ->where('banco_inter_codigo_solicitacao', $codigoSolicitacao)
                        ->get(db_prefix() . 'invoices')->row();

                    $banco_inter_dados_cobranca = [];

                    if (
                        isset($row->banco_inter_dados_cobranca)
                        && !empty($row->banco_inter_dados_cobranca)
                    ) {

                        $banco_inter_dados_cobranca = json_decode($row->banco_inter_dados_cobranca, true);

                        $banco_inter_dados_cobranca['boleto'] = [
                            'nossoNumero' => $post_callback->nossoNumero,
                            'codigoBarras' => $post_callback->codigoBarras,
                            'linhaDigitavel' => $post_callback->linhaDigitavel,
                        ];
                        $banco_inter_dados_cobranca['pix'] = [
                            'txid' => $post_callback->txid,
                            'pixCopiaECola' => $post_callback->pixCopiaECola,
                        ];
                    } else {
                        $banco_inter_dados_cobranca = [
                            'boleto' => [
                                'nossoNumero' => $post_callback->nossoNumero,
                                'codigoBarras' => $post_callback->codigoBarras,
                                'linhaDigitavel' => $post_callback->linhaDigitavel,
                            ],
                            'pix' => [
                                'txid' => $post_callback->txid,
                                'pixCopiaECola' => $post_callback->pixCopiaECola,
                            ]
                        ];
                    }

                    $this->db->where('id', $row->id)
                        ->update(
                            db_prefix() . 'invoices',
                            ['banco_inter_dados_cobranca' => json_encode($banco_inter_dados_cobranca)]
                        );

                    $affected_rows = $this->db->affected_rows();

                    if ($affected_rows) {
                        echo 'Sucesso.';
                        $this->invoices_model->log_invoice_activity(
                            $row->id,
                            '[BANCO INTER V3 - Webhook] Atualizado os dados de cobrança da fatura [' . format_invoice_number($row->id) . ']'
                        );
                    }
                }
            }
        }
    }

    protected function pagar($post_received)
    {
        $codigoSolicitacao = $post_received->codigoSolicitacao;

        $invoice = $this
            ->db
            ->select('id, bi_nosso_numero, total, banco_inter_item_adicionado')
            ->where('banco_inter_codigo_solicitacao', $codigoSolicitacao)
            ->get(db_prefix() . 'invoices')->row();

        if (!is_null($invoice)) {

            $valorTotalRecebimento = $post_received->valorTotalRecebido;

            $invoice_id            = $invoice->id;

            $this->db->where('id', $invoice_id);
            $this->db->update(db_prefix() . 'invoices', [
                'status'   => 2,
                'total'    => $valorTotalRecebimento,
                'subtotal' => $valorTotalRecebimento
            ]);

            if ($invoice->total < $valorTotalRecebimento && !$invoice->banco_inter_item_adicionado) {

                $juros_multas =  $valorTotalRecebimento - $invoice->total;

                $this->adicionar_itens_juros_multas($invoice_id, $juros_multas);
            }

            $transactionid = $post_received->nossoNumero . '|' . $post_received->codigoSolicitacao;

            $data = [
                'invoiceid'       => $invoice_id,
                'amount'          => $valorTotalRecebimento,
                'date'            => date('d/m/Y', strtotime($post_received->dataHoraSituacao)),
                'paymentmode'     => 'connect_inter',
                'paymentmethod'   => 'Módulo Banco Inter  V3 - ' . $post_received->origemRecebimento,
                'transactionid'   => $transactionid,
                'note'            => 'Baixa dada via webhook do banco Inter em ' . date('d/m/Y H:i:s'),
            ];

            if ($valorTotalRecebimento) {
                $this->db->where('id', $invoice_id)
                    ->update(db_prefix() . 'invoices', [
                        'total'    => $valorTotalRecebimento,
                        'subtotal' => $valorTotalRecebimento
                    ]);
            }

            $payment = $this->db->where('invoiceid', $invoice_id)
                ->where('transactionid', $transactionid)
                ->get(db_prefix() . 'invoicepaymentrecords')
                ->row();

            if (!$payment) {
                try {
                    $this->load->library("banco_inter/inter_gateway");
                    $this->inter_gateway->addPayment($data);
                    echo 'Pagamento adicionado com sucesso';
                } catch (\Throwable $th) {
                    $this->invoices_model->log_invoice_activity($invoice_id, '[BANCO INTER V3 - Webhook] Erro ao adicionar pagamento à fatura [' . format_invoice_number($invoice_id) . ']');
                }
            }

            if ($valorTotalRecebimento) {

                $this->db->where('id', $invoice_id)
                    ->update(db_prefix() . 'invoices', [
                        'total'    => $valorTotalRecebimento,
                        'subtotal' => $valorTotalRecebimento
                    ]);

                if ($this->db->affected_rows()) {
                    $this->invoices_model->log_invoice_activity($invoice_id, '[BANCO INTER V3 - Webhook] Atualizado o valor total da fatura [' . format_invoice_number($invoice_id) . ']');
                }
            }

            return true;
        }
    }

    /**
     * @param mixed $invoice_id
     * @param mixed $valor
     *
     * @return [type]
     */
    public function adicionar_itens_juros_multas($invoice_id, $valor)
    {
        $newitems = [
            [
                'order'            => 2,
                'description'      => 'Multa/Juros por Atraso',
                'long_description' => '2% de multa legal por atraso/pagamento após o vencimento, e juros de mora de 1% ao mês',
                'qty'              => 1,
                'unit'             => null,
                'rate'             => $valor,
            ]
        ];

        foreach ($newitems as $item) {
            if ($new_item_added = add_new_sales_item_post($item, $invoice_id, 'invoice')) {
                _maybe_insert_post_item_tax($new_item_added, $item, $invoice_id, 'invoice');
            }
        }

        $this->db->where('id', $invoice_id)
            ->update(db_prefix() . 'invoices', [
                'banco_inter_item_adicionado' => 1
            ]);

        if ($this->db->affected_rows()) {
            $this->invoices_model->log_invoice_activity($invoice_id, '[BANCO INTER V3 - Webhook] Adicionado itens de juros e multas à fatura [' . format_invoice_number($invoice_id) . ']');
        }
    }
}
