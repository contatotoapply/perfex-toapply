<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Subscriptions extends AdminController
{
    private $apiKey;
    private $apiUrl;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('stripe_subscriptions');

        $this->load->model('currencies_model');

        $this->load->model('taxes_model');

        $this->load->model('clients_model');

        $this->load->model('connect_asaas/subscriptions_model');

        $this->load->library("connect_asaas/base_api");

        $this->load->library("connect_asaas/asaas_gateway");

        $this->apiUrl = $this->base_api->getUrlBase();

        $this->apiKey =  $this->base_api->getApiKey();
    }

    public function index()
    {
        $clientid = $this->input->get('clientid');

        $client = $this->clients_model->get($clientid);

        $client = $clientid ? $client : null;

        $items = $this->db->get(db_prefix() . 'items')->result();

        $clients = $this->db->select('userid,company')->get(db_prefix().'clients')->result();

        $data = ['title' => _l('subscriptions'), 'client' => $client, 'items' => $items,
        'clients' => $clients];

        $this->load->view("connect_asaas/admin/subscriptions/manage", $data);
    }

    public function create()
    {
        $customer_id = $this->input->get('customer_id');

        $client = $this->clients_model->get($customer_id);

        $items = $this->db->get(db_prefix() . 'items')->result();

        $data = ['title' => _l('subscriptions'), 'client' => $client, 'items' => $items];

        $this->load->view("connect_asaas/admin/clients/groups/subscriptions", $data);
    }

    public function store()
    {
        $body                  = $this->input->post(['clientid', 'itemid', 'customer', 'nextDueDate', 'vat']);

        $clientid              = $body['clientid'];

        $client   = $this->clients_model->get($clientid);

        $base_url_subscription = admin_url("connect_asaas/subscriptions");

        if (!$client) {
            set_alert("danger", "Cliente não encontrado");
            redirect($base_url_subscription);
        }

        $vat         = $client->vat;
        $customer    = $client->asaas_customer_id;
        $itemid      = $body['itemid'];
        $nextDueDate = $body['nextDueDate'];
        $nextDueDate = to_sql_date($nextDueDate);

        $item = $this->db->select('rate as value,long_description, description')->where('id', $itemid)
            ->get(db_prefix() . 'items')->row();

        $base_url_subscription = admin_url("connect_asaas/subscriptions?clientid={$clientid}");

        if (!$item) {
            set_alert("danger", "Item não encontrado");
            redirect($base_url_subscription);
        }

        if (!$vat) {
            set_alert("danger", "O CPF do cliente é inválido.");
            redirect($base_url_subscription);
        }

        if ($nextDueDate == '' || $nextDueDate < date('Y-m-d')) {
            set_alert("danger", "Data de vencimento inválida");
            redirect($base_url_subscription);
        }

        if (!$customer) {
            // Tenta cadastrar o usuário se ele não existir.
            $response = $this->asaas_gateway->search_customer($this->apiUrl, $this->apiKey, $vat);

            if (isset($response['data'])) {
                $data = $response['data'];

                if (!$data) {
                    $client = $this->db->where('userid', $clientid)->get(db_prefix() . 'clients')->row();

                    $email_client = $this->asaas_gateway->get_customer_customfields($client->userid, 'customers', 'customers_email_principal');

                    $email_client = 'emailtest@gmail.com';

                    if (!$email_client) {
                        set_alert("danger", "O email do cliente não foi encontrado.");
                        redirect(admin_url('connect_asaas/clients/client/' . $clientid . '?group=subscriptions'));
                    }

                    $address_number = $this->asaas_gateway->get_customer_customfields($client->userid, 'customers', 'customers_numero');

                    $disableChargeNotification = $this->asaas_gateway->getSetting('disable_charge_notification');

                    $post_data = json_encode([
                        "name"                 => $client->company,
                        "email"                => $email_client,
                        "cpfCnpj"              => $client->vat,
                        "postalCode"           => $client->zip,
                        "address"              => $client->address,
                        "addressNumber"        => $address_number,
                        "complement"           => "",
                        "phone"                => $client->phonenumber,
                        "mobilePhone"          => $client->phonenumber,
                        "externalReference"    => $client->userid,
                        "notificationDisabled" => $disableChargeNotification,
                    ]);

                    try {

                        // code...
                        $cliente_create = $this->asaas_gateway->create_customer($this->apiUrl, $this->apiKey, $post_data);

                        $customer = $cliente_create['id'];

                        $this->db->where('userid', $clientid)->update(db_prefix() . 'clients', ['asaas_customer_id' => $customer]);

                        log_activity('(Assinatura) Cliente cadastrado no Asaas [Cliente ID: ' . $client->userid . ']');
                    } catch (\Throwable $th) {
                        log_activity('(Assinatura) Erro ao cadastrar cliente no Asaas [Cliente ID: ' . $client->userid . ']' . $th->getMessage());
                    }
                }
            }
        }

        $payload = [
            "customer"    => $customer,
            "billingType" => "CREDIT_CARD",
            "nextDueDate" => $nextDueDate,
            "value"       => $item->value,
            "cycle"       => "MONTHLY",
            "description" => $item->description
        ];

        $response = $this->asaas_gateway->addSubscription($payload);

        if (isset($response['response'])) {

            $subscriptionId = $response['response']->id;

            $response = $this->asaas_gateway->getPayment($subscriptionId);

            $response = $response['response']->data[0];

            $subscription = [
                'name'                    => $item->description,
                'description'             => $item->long_description,
                'clientid'                => $clientid,
                'date'                    => $nextDueDate,
                'currency'                => 1,
                'status'                  => 'future',
                'quantity'                => 1,
                'asaas_subscription_id'   => $subscriptionId,
                'asaas_subscription_link' => $response->invoiceUrl,
                'asaas_item_id'           => $itemid,
            ];

            $inserted_id = $this->subscriptions_model->create($subscription);

            if ($inserted_id) {
                set_alert("success", "Incrição realizada com sucesso.");
                redirect($base_url_subscription);
            } else {
                log_activity('Asaas: Erro ao criar assinatura (1).');
                set_alert("success", "Erro ao criar assinatura (1).");
                redirect($base_url_subscription);
            }
        } else {
            log_activity('Asaas: Erro ao criar assinatura (2).');
            set_alert("success", "Erro ao criar assinatura (2).");
            redirect($base_url_subscription);
        }
    }

    public function update($id)
    {

        $body                  = $this->input->post(['clientid', 'itemid', 'customer', 'nextDueDate', 'vat', 'assinatura_asaas_id']);

        $clientid              = $body['clientid'];

        $client   = $this->clients_model->get($clientid);

        $base_url_subscription = admin_url("connect_asaas/subscriptions");

        if (!$client) {
            set_alert("danger", "Cliente não encontrado");
            redirect($base_url_subscription);
        }

        $vat         = $client->vat;
        $customer    = $client->asaas_customer_id;
        $itemid      = $body['itemid'];
        $nextDueDate = $body['nextDueDate'];
        $nextDueDate = to_sql_date($nextDueDate);


        $item = $this->db->select('rate as value,long_description, description')->where('id', $itemid)
            ->get(db_prefix() . 'items')->row();

        $base_url_subscription = admin_url("connect_asaas/subscriptions?clientid={$clientid}");

        if (!$item) {
            set_alert("danger", "Item não encontrado");
            redirect($base_url_subscription);
        }

        if (!$vat) {
            set_alert("danger", "O CPF do cliente é inválido.");
            redirect($base_url_subscription);
        }

        if ($nextDueDate == '' || $nextDueDate < date('Y-m-d')) {
            set_alert("danger", "Data de vencimento inválida");
            redirect($base_url_subscription);
        }

        if (!$customer) {
            // Tenta cadastrar o usuário se ele não existir.
            $response = $this->asaas_gateway->search_customer($this->apiUrl, $this->apiKey, $vat);

            if (isset($response['data'])) {
                $data = $response['data'];

                if (!$data) {
                    $client = $this->db->where('userid', $clientid)->get(db_prefix() . 'clients')->row();

                    $email_client = $this->asaas_gateway->get_customer_customfields($client->userid, 'customers', 'customers_email_principal');

                    $email_client = 'emailtest@gmail.com';

                    if (!$email_client) {
                        set_alert("danger", "O email do cliente não foi encontrado.");
                        redirect(admin_url('connect_asaas/clients/client/' . $clientid . '?group=subscriptions'));
                    }

                    $address_number = $this->asaas_gateway->get_customer_customfields($client->userid, 'customers', 'customers_numero');

                    $disableChargeNotification = $this->asaas_gateway->getSetting('disable_charge_notification');

                    $post_data = json_encode([
                        "name"                 => $client->company,
                        "email"                => $email_client,
                        "cpfCnpj"              => $client->vat,
                        "postalCode"           => $client->zip,
                        "address"              => $client->address,
                        "addressNumber"        => $address_number,
                        "complement"           => "",
                        "phone"                => $client->phonenumber,
                        "mobilePhone"          => $client->phonenumber,
                        "externalReference"    => $client->userid,
                        "notificationDisabled" => $disableChargeNotification,
                    ]);

                    try {

                        // code...
                        $cliente_create = $this->asaas_gateway->create_customer($this->apiUrl, $this->apiKey, $post_data);

                        $customer = $cliente_create['id'];

                        $this->db->where('userid', $clientid)->update(db_prefix() . 'clients', ['asaas_customer_id' => $customer]);

                        log_activity('(Assinatura) Cliente cadastrado no Asaas [Cliente ID: ' . $client->userid . ']');
                    } catch (\Throwable $th) {
                        log_activity('(Assinatura) Erro ao cadastrar cliente no Asaas [Cliente ID: ' . $client->userid . ']' . $th->getMessage());
                    }
                }
            }
        }

        $payload = [
            "billingType" => "CREDIT_CARD",
            "value"       => $item->value,
            "status"      => "ACTIVE",
            "nextDueDate" => $nextDueDate,
            "cycle"       => "MONTHLY",
            "description" => $item->description
        ];

        if(!$body['assinatura_asaas_id']){
            set_alert("danger", "Assinatura não encontrada.");
            redirect($base_url_subscription);
        }

        $response = $this->asaas_gateway->updateSubscription($payload, $body['assinatura_asaas_id']);

        if (isset($response['response'])) {

            $subscriptionId = $response['response']->id;

            $response = $this->asaas_gateway->getPayment($subscriptionId);

            $response = $response['response']->data[0];

            $subscription = [
                'name'                    => $item->description,
                'description'             => $item->long_description,
                'clientid'                => $clientid,
                'date'                    => $nextDueDate,
                'currency'                => 1,
                'status'                  => 'future',
                'quantity'                => 1,
                'asaas_item_id'           => $itemid,
            ];

            $inserted_id = $this->subscriptions_model->update($id, $subscription);

            if ($inserted_id) {
                set_alert("success", "Incrição atualizada com sucesso.");
                redirect($base_url_subscription);
            } else {
                log_activity('Asaas: Erro ao criar assinatura (1).');
                set_alert("success", "Erro ao criar assinatura (1).");
                redirect($base_url_subscription);
            }
        } else {
            log_activity('Asaas: Erro ao criar assinatura (2).');
            set_alert("success", "Erro ao criar assinatura (2).");
            redirect($base_url_subscription);
        }
    }

    public function table()
    {
        if (!has_permission('subscriptions', '', 'view') && !has_permission('subscriptions', '', 'view_own')) {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_dir_path(CONNECT_ASAAS_MODULE_NAME) . 'views/admin/tables/subscriptions');
    }

    public function delete($id)
    {
        if (!has_permission('subscriptions', '', 'delete')) {
            access_denied('Subscriptions Delete');
        }

        $subscription = $this->db->select('id,asaas_subscription_id')
            ->where('id', $id)->get(db_prefix() . 'subscriptions')->row();

        if (!$subscription) {
            access_denied('Subscriptions Delete');
        }

        if ($subscription->asaas_subscription_id) {
            $this->asaas_gateway->removeSubscription($subscription->asaas_subscription_id);
        }

        // removeSubscription
        if ($subscription = $this->subscriptions_model->delete($id)) {
            if (!empty($subscription->stripe_subscription_id)) {
                try {
                    // In case already deleted in Stripe
                    $this->stripe_subscriptions->cancel($subscription->stripe_subscription_id);
                } catch (Exception $e) {
                }
            }
            set_alert('success', _l('deleted', _l('subscription')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('subscription')));
        }

        if (strpos($_SERVER['HTTP_REFERER'], 'clients/') !== false) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('connect_asaas/subscriptions'));
        }
    }
}
