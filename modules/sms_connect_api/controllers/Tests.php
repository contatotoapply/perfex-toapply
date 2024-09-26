<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tests extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("sms_connect_api/sms_mpwa_gateway");
        $this->load->library("sms_connect_api/send_post");
        $this->load->library("sms_connect_api/sms_notifications_zap_engine_library");
    }


    public function testSendDataPost()
    {
        $data = ['url' => 'http://valter.develop.com.br/api/leads', 'name' => 'Taffarel', 'source' => '1', 'status' => 1];
        $result = $this->send_post->lembrete(66, 'sms_trigger_notifications_zap_engine_lembrete_pagamento_json');
        dd(1, $result);
    }

    public function testCreateInstance()
    {

        $data = [
            "instanceName" => "Instância Nome 4",
            "token" => "",
            "qrcode" => true
        ];
        $this->notifications_zap_engine_library->create_instance($data);
    }

    public function testListInstances()
    {
        $this->notifications_zap_engine_library->list_instances();
    }


    public function testSendMessage()
    {
        dd(1, $this->sms_notifications_zap_engine_library->send(31988070770, 'Teste de envio de mensagem em: *' . date('Y-m-d H:i:s') . '*'));
    }

    public function testRemoverAcentosECedilha()
    {
        $str = 'Olá, tudo bem? Coração é uma palavra com acento àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ';
        dd(1, removerAcentosECedilha($str));
    }

    public function testeEnviarLembreteFaturasVencidas()
    {
        $this->load->library("sms_connect_api/enviar_lembrete_faturas_vencidas");
        // $this->db->select('*')->get(db_prefix() . 'central_notificacoes_lembretes')->result();
        $invoices = $this->db->select('*')->get(db_prefix() . 'invoices')->result();
        // echo '<pre>';
        // print_r($invoices);
        $this->enviar_lembrete_faturas_vencidas->enviar_lembrete();
        echo 'Ok';
    }

    public function testSmsMpwaGatewaySend()
    {
        $this->sms_mpwa_gateway->send(55639364840, 'Teste de envio de mensagem em: *' . date('Y-m-d H:i:s') . '*');
    }

    public function salvar_config_defaults()
    {
        $gateway_id = $this->app_sms->get_active_gateway()['id'];
        $gateway = $this->{'sms_' . $gateway_id };
        dd(1, $gateway->send(55639364840, 'Teste de envio de mensagem em: *' . date('Y-m-d H:i:s') . '*'));

        add_option('central_notificacoes_softpowerne_url_base_do_servidor', '');
        add_option('central_notificacoes_softpowerne_hora_para_enviar_notificacao_para_faturas_em_atraso', '7');
        add_option('central_notificacoes_softpowerne_hora_para_enviar_notificacao_para_faturas_no_dia_do_vencimento', '8');
        add_option('central_notificacoes_softpowerne_reenviar_automaticamente_um_lembrete_depois_de_dias', '1');
        add_option('central_notificacoes_softpowerne_enviar_lembrete_x_dias_antes_da_data_de_vencimento', '2');

        update_option('sms_notifications_zap_engine_library_qtd_dias_para_avisar_lembrete_vencimento', '3,2,1');
        update_option('sms_notifications_zap_engine_library_qtd_dias_de_lembretes', '1,2');
        update_option('sms_notifications_zap_engine_library_qtd_dias_para_enviar_lembrete_suspensao', '3,4');
        update_option('sms_notifications_zap_engine_library_qtd_dias_para_suspender_servicos', '5');

        update_option('sms_trigger_notifications_zap_engine_lembrete_pagamento', 'Prezado(a) {contact_firstname},

Gostaríamos de lembrá-lo(a) sobre a iminente data de vencimento da fatura associada à sua conta. Agradecemos sua atenção a este assunto.

Detalhes da Fatura:
- Número da Fatura: {invoice_number}
- Data de Vencimento: {invoice_duedate}
- Valor: {invoice_total}

Para acessar a fatura completa e efetuar o pagamento, por favor, utilize o seguinte link: {invoice_link}

Agradecemos pela sua cooperação.

Atenciosamente,
{companyname}');

        add_option('sms_trigger_notifications_zap_engine_enviar_no_dia_do_vencimento', 'Assunto: *Lembrete de Vencimento*

Prezado(a)  {contact_firstname},

Esperamos que esteja bem! Queremos lembrar que hoje é o dia do vencimento referente à fatura número
*{invoice_number}*. Para garantir que sua conta permaneça em dia, solicitamos que realize o pagamento até o final do dia.

Link da fatura: {invoice_link}

Se você já efetuou o pagamento, por favor, ignore este lembrete.

Agradecemos por sua parceria contínua e ficamos à disposição para qualquer dúvida ou assistência.

Atenciosamente,
{companyname}');

        add_option('sms_trigger_notifications_zap_engine_faturas_atrasadas', 'Lembrete Importante: *Faturas Vencidas*
*Prezado(a) cliente,*

Total de Dias Vencidos: {qtd_dias_vencida}

Esperamos que esteja tudo bem com você. Gostaríamos de lembrar que a sua cobrança referente à fatura
*{invoice_number}* ainda não foi quitada. Pedimos que, por gentileza, regularize o pagamento o mais breve possível.

Valorizamos a sua parceria e entendemos que imprevistos acontecem. Caso haja alguma dificuldade, estamos disponíveis para ajudar a encontrar uma solução adequada. Não deixe de entrar em contato conosco.

Agradecemos a sua atenção e contamos com sua colaboração para manter nosso relacionamento em dia.

Atenciosamente,

Link: {invoice_link}

*{companyname} | CRM*');

        add_option('sms_trigger_notifications_zap_engine_servico_suspenso', '
Lembrete Importante: *Suspensão de Serviços em Breve*

Prezado(a) {contact_firstname},

Esperamos que esta mensagem o(a) encontre bem. Gostaríamos de lembrar sobre a pendência de pagamento associada à sua conta em nome de {companyname}, referente à fatura de número {invoice_number}.

Detalhes da Fatura:

Número da Fatura: {invoice_number}
Valor Total: {invoice_total}
Data de Vencimento: {invoice_duedate}
Dias até o Vencimento: {qtd_dias_vencida}
A fatura encontra-se pendente há {qtd_dias_vencida} dias, e o prazo para pagamento está prestes a expirar em {invoice_duedate}. Caso a fatura não seja quitada até essa data, lamentavelmente, seremos forçados a suspender temporariamente os seus serviços.

Para evitar qualquer interrupção indesejada, por favor, considere efetuar o pagamento o quanto antes. Você pode visualizar e pagar a fatura de maneira conveniente através deste link: {invoice_link}.

Estamos à disposição para esclarecer qualquer dúvida ou fornecer assistência adicional. Por favor, entre em contato conosco pelo número {phonenumber} ou pelo e-mail {cliente_email}.

Agradecemos a sua atenção a esta questão e esperamos continuar a fornecer serviços de qualidade.

Atenciosamente,

Equipe {companyname}');

        add_option('sms_trigger_notifications_zap_engine_servicos_suspensos', 'Assunto: *Aviso de Suspensão de Serviço - Ação Necessária*

Prezado(a) {contact_firstname},

Olá, esperamos que esteja bem. Gostaríamos de informar que a sua conta em nome de {companyname} está prestes a ser suspensa devido a um pagamento pendente.

Detalhes da Fatura:

Número da Fatura: {invoice_number}
Valor Total: {invoice_total}
Data de Vencimento: {invoice_duedate}
Dias até o Vencimento: {qtd_dias_vencida}
A fatura em questão está pendente há {qtd_dias_vencida} dias e precisa ser paga até {invoice_duedate} para evitar a suspensão dos serviços. Você pode visualizar e pagar a fatura através deste link: {invoice_link}.

Caso já tenha efetuado o pagamento, por favor, desconsidere este aviso.

Pedimos que entre em contato conosco o mais breve possível pelo número {phonenumber} ou pelo e-mail {cliente_email} para confirmar o pagamento ou discutir alternativas.

Agradecemos a sua compreensão e esperamos resolver essa questão prontamente para que você possa continuar a desfrutar dos nossos serviços.

Atenciosamente,

Equipe {companyname}');
echo 'Ok';
    }

    public function test_send(){
        $this->load->library("sms_connect_api/enviar_servico_suspenso");

        dd(1, $this->enviar_servico_suspenso->send_test());

    }


    public function test_ddi(){
        $this->load->library("sms_mpwa_gateway");
        dd(1, $this->sms_mpwa_gateway->formatar_telefone('+55 55 9637-2282'));
        dd(1, $this->sms_mpwa_gateway->formatar_telefone('+55 55 8126-9514'));
        dd(1, $this->sms_mpwa_gateway->formatar_telefone('+55 55 9932-5227'));
        dd(1, $this->sms_mpwa_gateway->formatar_telefone('+55 55 9134-9253'));
        dd(1, $this->sms_mpwa_gateway->formatar_telefone('+55 55 9905-9449'));
    }
}
