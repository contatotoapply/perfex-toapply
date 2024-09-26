<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Lembretes extends AdminController
{
    protected $invoice_id;

    public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            set_alert('danger', 'Você não tem permissão para executar esta ação.');
            redirect(admin_url());
        }

        $this->load->model('invoices_model');
        $this->load->library('sms_connect_api/sms_notifications_zap_engine_library');
        $this->invoice_id = $this->input->get('invoice_id');
    }

    private function get_total_dias_atrasados($duedate)
    {
        // Supondo que você tenha as datas em formato de string
        $currentDate = date('Y-m-d'); // Data atual

        // Criar objetos DateTime com as datas
        $duedateObj = new DateTime($duedate);
        $currentDateObj = new DateTime($currentDate);

        // Calcular a diferença entre as datas
        $interval = $currentDateObj->diff($duedateObj);

        // Obter o número de dias de diferença
        $daysDifference = $interval->days;

        return $daysDifference;
    }


    public function calcularDiasVencida($duedate)
    {
        $now = new DateTime();
        $duedate = new DateTime($duedate);

        $interval = $now->diff($duedate);
        return $interval->days;
    }


    /**
     * @param mixed $invoice_id
     * @param mixed $reminder_type | pagamento_a_vencer, fatura_vencida, servico_suspenso
     *
     * @return [type]
     */
    private function abstractLembrete($invoice_id, $reminder_type, $trigger_name)
    {

        $invoice = $this->invoices_model->get($invoice_id);

        if ($invoice) {

            $contacts = $this->db
                ->select('id,userid,phonenumber,firstname,lastname,email,central_notificacao_contact_whatsapp,active')
                ->where('userid', $invoice->clientid)
                ->where('(central_notificacao_contact_whatsapp = 1)')
                ->where('active', 1)
                ->get(db_prefix() . 'contacts')
                ->result_array();

            $invoice_number = format_invoice_number($invoice->id);

            $enviou = false;

            if (!empty($contacts)) {

                foreach ($contacts as $contact) {

                    $phonenumber = $contact['phonenumber'];
                    if (!empty($phonenumber)) {

                        $merge_fields = [
                            'invoice_id'                 => $invoice->id,
                            'contact_firstname'          => $contact['firstname'],
                            'invoice_link'               => base_url('invoice/' . $invoice->id . '/' . $invoice->hash),
                            'invoice_number'             => $invoice_number,
                            'invoice_duedate'            => _d($invoice->duedate),
                            'invoice_total'              => app_format_money($invoice->total, $invoice->currency),
                            'phonenumber'                => $phonenumber,
                            'companyname'                => get_option('companyname'),
                            'empresa'                    => $invoice->client->company,
                            'cliente_email'              => $contact['email'],
                            'qtd_dias_vencida'           => $this->calcularDiasVencida($invoice->duedate) >= 1 ? $this->calcularDiasVencida($invoice->duedate) : "",
                            'total_dias_ate_vencimento'  => $this->get_total_dias_atrasados($invoice->duedate),
                        ];

                        $enviou = $this->sms_notifications_zap_engine_library->trigger($trigger_name, $phonenumber, $merge_fields);

                        $description = "Enviou whatsapp avisando de pagamento atrasado";
                        if ($reminder_type == 'pagamento_a_vencer') {
                            $description = "Enviou whatsapp avisando de pagamento a vencer";
                        } elseif ($reminder_type == 'servico_suspenso') {
                            $description = "Enviou whatsapp avisando de serviço suspenso";
                        }

                        $this->invoices_model->log_invoice_activity($invoice->id, $description . " para " . $phonenumber . " Contato: " . $contact['firstname']);

                        if ($enviou) {
                            $enviou = true;
                        }
                    }
                }
            }

            if($enviou) {
                $data = array(
                    'created_by_staff_id' => get_staff_user_id(),
                    'rel_id' => $invoice_id,
                    'rel_type' => 'invoice',
                    'reminder_type' => $reminder_type,
                    'date' => date('Y-m-d'),
                );

                $this->db->insert(db_prefix() . 'central_notificacoes_lembretes', $data);
                set_alert('success', 'Notificação enviada com sucesso.');
                redirect(admin_url('invoices/list_invoices'));
            } else {
                set_alert('danger', 'Notificação NÃO enviada.');
                redirect(admin_url('invoices/list_invoices'));
            }
        }
    }

    public function enviarLembreteDePagamento()
    {
        $this->abstractLembrete($this->invoice_id, 'pagamento_a_vencer', 'notifications_zap_engine_lembrete_pagamento');
    }

    public function enviarFaturaVencida()
    {
        $this->abstractLembrete($this->invoice_id, 'fatura_vencida', 'notifications_zap_engine_faturas_atrasadas');
    }

    public function enviarServicoSuspenso()
    {
        $this->abstractLembrete($this->invoice_id, 'aviso_servico_suspenso', 'notifications_zap_engine_servico_suspenso');
    }

    public function enviarServicoPronto()
    {
        $clientid = $this->input->get('clientid');

        if(!$clientid){
            set_alert('danger', 'Cliente não encontrado.');
            redirect(admin_url('clients'));
        }

        $client = $this->db->where('userid', $clientid)->get(db_prefix().'clients')->row();

        // if not exists below
        if(!$client){
            set_alert('danger', 'Cliente não encontrado.');
            redirect(admin_url('clients'));
        }

        $tblname = db_prefix().'central_notificacoes_lembretes';

        $data = array(
                   'created_by_staff_id' => get_staff_user_id(),
                   'rel_id'              => $clientid,
                   'rel_type'            => 'client',
                   'reminder_type'       => 'servico_pronto',
                   'date'                => date('Y-m-d'),
                   'status'              => 0
               );

        $this->db->insert($tblname, $data);

        $last_insert_id = $this->db->insert_id();

        $contacts = $this->db
            ->select('id,userid,phonenumber,firstname,lastname,email,central_notificacao_contact_whatsapp,active')
            ->where('userid', $clientid)
            ->where('(central_notificacao_contact_whatsapp = 1)')
            ->where('active', 1)
            ->get(db_prefix() . 'contacts')
            ->result_array();


        if (!empty($contacts)) {

            $enviou = false;

            foreach ($contacts as $contact) {

                $phonenumber = $contact['phonenumber'];

                if (!empty($phonenumber)) {

                    $merge_fields = [
                        'contact_firstname'          => $contact['firstname'],
                        'phonenumber'                => $phonenumber,
                        'companyname'                => get_option('companyname'),
                        'empresa'                    => $client->company,
                        'cliente_email'              => $contact['email']
                    ];

                    $enviou = $this->sms_notifications_zap_engine_library->trigger('notifications_zap_engine_servico_pronto', $phonenumber, $merge_fields);

                    if ($enviou) {
                        $enviou = true;
                    }
                }
            }

            if($enviou){
                $this->db->where('id', $last_insert_id)
                    ->set('status', 1)->update($tblname);
            }
        }


        set_alert('success', 'Notificação de serviço pronto enviada com sucesso.');
        redirect(admin_url('clients/client/'.$clientid.'?group=servico-pronto'));
    }
}
