<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asaas_gateway extends App_gateway
{

    protected $ci;
    protected $api_key;
    protected $base_url;
    protected $user_agent;

    public function __construct()
    {
        parent::__construct();

        $this->ci = &get_instance();

        $this->setId('connect_asaas');
        $this->setName('Connnect Asaas');

        $this->setSettings(array(
            array(
                'name'      => 'api_key',
                'encrypted' => true,
                'label'     => 'Api key Produção',
                'type'      => 'input',
            ),
            array(
                'name'      => 'api_key_sandbox',
                'encrypted' => true,
                'label'     => 'Api key Sandbox',
                'type'      => 'input',
            ),
            array(
                'name'          => 'sandbox',
                'label'         => 'Sandbox',
                'type'          => 'yes_no',
                'default_value' => 1,
            ),
            array(
                'name'          => 'debug',
                'label'         => 'debug',
                'type'          => 'yes_no',
                'default_value' => 0,
            ),
            array(
                'name'          => 'currencies',
                'label'         => 'settings_paymentmethod_currencies',
                'default_value' => 'BRL'
            ),
            array(
                'name'          => 'description',
                'label'         => 'settings_paymentmethod_description',
                'type'          => 'textarea',
                'default_value' => 'Pagamento da Fatura {invoice_number}',
            ),
            array(
                'name'          => 'interest_value',
                'label'         => 'Valor juros',
                'type'          => 'input',
                'default_value' => '0.00',
            ),
            array(
                'name'          => 'fine_value',
                'label'         => 'Valor multa',
                'type'          => 'input',
                'default_value' => '0.00',
            ),
            array(
                'name'          => 'discount_type',
                'label'         => 'Tipo de desconto',
                'type'          => 'yes_no',
                'default_value' => 1,
                //'field_attributes' => ['id' => 'discount_type_row'],
                //  'after'            => '<p class="mbot15">Statement descriptors are limited to 22 characters, cannot use the special characters <, >, \', ", or *, and must not consist solely of numbers.</p>',
            ),
            array(
                'name'          => 'discount_value',
                'label'         => 'Valor desconto',
                'type'          => 'input',
                'default_value' => '0',
            ),
            array(
                'name'          => 'discount_days',
                'label'         => 'Dias para desconto',
                'type'          => 'input',
                'default_value' => 0,
            ),
            array(
                'name'          => 'installmentCount',
                'label'         => 'Limite de parcelas',
                'type'          => 'input',
                'default_value' => 1,
            ),
            array(
                'name'          => 'diasdevencimento',
                'label'         => 'Prazo para Pagamento em dias pós o cálculo de juros e multas',
                'type'          => 'input',
                'default_value' => 3,
            ),
            array(
                'name'          => 'billet_only',
                'label'         => 'Habilitar boleto',
                'type'          => 'yes_no',
                'default_value' => 1,
            ),
            array(
                'name'          => 'card_only',
                'label'         => 'Habilitar cartão de crédito',
                'type'          => 'yes_no',
                'default_value' => 1,
            ),
            array(
                'name'          => 'pix_only',
                'label'         => 'Habilitar PIX',
                'type'          => 'yes_no',
                'default_value' => 1,
            ),
            array(
                'name'          => 'delete_charge',
                'label'         => 'Deletar cobrança da fatura no Asaas',
                'type'          => 'yes_no',
                'default_value' => 0,
            ),

            array(
                'name'          => 'update_charge',
                'label'         => 'Atualizar cobrança da fatura no Asaas',
                'type'          => 'yes_no',
                'default_value' => 0,
            ),

            array(
                'name'          => 'disable_charge_notification',
                'label'         => 'Desativar notificações de cobrança',
                'type'          => 'yes_no',
                'default_value' => 1,
            ),
            array(
                'name'          => 'update_payment_invoice_asaas',
                'label'         => _l('connect_asaas_update_payment_invoice_asaas'),
                'type'          => 'yes_no',
                'default_value' => 1,
            ),
            array(
                'name'          => 'is_installment',
                'label'         => _l('Habilitar Carnê'),
                'type'          => 'yes_no',
                'default_value' => 0,
            )
        ));

        $this->ci->load->library("connect_asaas/base_api");
        $this->api_key  = $this->ci->base_api->getApiKey();
        $this->base_url = $this->ci->base_api->getUrlBase();
        $this->user_agent = 'Perfex CRM';
    }

    public function process_payment($data)
    {

        if (empty($data)) {
            return;
        }

        $invoice = $data['invoice']->id;
        $ci      = &get_instance();
        $ci->db->where('id', $invoice);
        $row = $ci->db->get(db_prefix() . 'invoices')->row_array();

        $ci->db->select('c.*, cc.email');
        $ci->db->from(db_prefix() . 'clients c');
        $ci->db->join(db_prefix() . 'contacts cc', 'cc.userid = c.userid AND cc.is_primary = 1', 'LEFT');
        $ci->db->where('c.userid', $row['clientid']);
        $client = $ci->db->get()->row_array();

        $api_key = $this->api_key;
        $api_url = $this->base_url;

        $billet_only      = $this->getSetting('billet_only');
        $card_only        = $this->getSetting('card_only');
        $pix_only         = $this->getSetting('pix_only');
        $interest         = $this->getSetting('interest_value');
        $fine             = $this->getSetting('fine_value');
        $discount_value   = $this->getSetting('discount_value');
        $dueDateLimitDays = $this->getSetting('discount_days');
        $discount_type    = $this->getSetting('discount_type');
        $description      = $this->getSetting('description');

        $search_charge = $this->search_charge($api_url, $api_key, $row["hash"]);

        if ($row['status'] == '4') {
            $row['total'] = $this->calculate_invoice($invoice, $row, $fine, $interest);
            if ($this->debug_enable()) {
                echo $row['total'];
                echo "<br>";
                echo $ci->db->last_query();
                echo "<br>";
                echo "<pre>";
                var_dump($search_charge);
                echo "</pre>";
            }
        }

        $ci->db->where('id', $invoice);
        $row = $ci->db->get(db_prefix() . 'invoices')->row_array();

        $disable_charge_notification = $this->getSetting('disable_charge_notification');

        if ($disable_charge_notification == '1') {
            $notificationDisabled = true;
        } else {
            $notificationDisabled = false;
        }

        $invoice_number = $row['prefix'] . str_pad($row['number'], 6, "0", STR_PAD_LEFT);
        $description    = mb_convert_encoding(str_replace("{invoice_number}", $invoice_number, $description), 'UTF-8', 'ISO-8859-1');

        $document   = str_replace('/', '', str_replace('-', '', str_replace('.', '', $client['vat'])));
        $postalCode = str_replace('-', '', str_replace('.', '', $client['zip']));

        $customer = $this->search_customer($api_url, $api_key, $document);
        if ($customer['totalCount'] == "0") {
            $post_data = json_encode([
                "name"                 => $client['company'],
                "email"                => $client['email'],
                "cpfCnpj"              => $document,
                "postalCode"           => $postalCode,
                "address"              => $client['address'],
                "addressNumber"        => $client['numero'],
                "complement"           => "",
                "phone"                => $client['phonenumber'],
                "mobilePhone"          => $client['phonenumber'],
                "externalReference"    => $invoice,
                "notificationDisabled" => $notificationDisabled,
            ]);

            $cliente_create = $this->create_customer($api_url, $api_key, $post_data);
            $cliente_id     = $cliente_create['id'];

            log_activity('Cliente cadastrado no Asaas [Cliente ID: ' . $cliente_id . ']');

            if ($this->debug_enable()) {
                echo "Campos cadastro";
                echo "<br>";
                echo "<pre>";
                var_dump($post_data);
                echo "</pre>";
                echo "Cliente cadastrado no Asaas ID" . $cliente_id;
                echo "<hr>";
            }
        } else {
            // se existir recupera os dados para cobranca
            $cliente_id = $customer['data'][0]['id'];
            if ($this->debug_enable()) {
                echo "Cliente já existente ID " . $cliente_id;
                echo "<hr>";
            }
        }

        $discount = NULL;

        $sem_desconto = strpos($row['adminnote'], "{sem_desconto}", 0);

        if ($discount_type == 1) {

            $type = 'FIXED';

            $discount = [
                'type'             => 'FIXED',
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if ($discount_type == 0) {

            $type = 'PERCENTAGE';

            $discount = [
                'type'             => 'PERCENTAGE',
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if (is_bool($sem_desconto)) {
            $discount = [
                'type'             => $type,
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if ($this->debug_enable()) {
            echo "Tipo desconto config " . $discount_type;
            echo "<br>";
            echo "Tipo desconto " . $type;
            echo "<br>";
            echo "Campos desconto";
            echo "<br>";
            echo "<pre>";
            var_dump($discount);
            echo "</pre>";
            echo "<hr>";
            echo "Sem desconto " .   var_dump($sem_desconto);
            echo "<br>";
        }

        $post_data = [
            "customer"          => $cliente_id,
            "billingType"       => "BOLETO",
            "dueDate"           => $row['duedate'],
            "value"             => $row['total'],
            "description"       => $description,
            "externalReference" => $row['hash'],
            "discount"          => $discount,
            "fine"              => [
                "value" => $fine,
            ],
            "interest" => [
                "value" => $interest,
            ],
            "postalService" => false
        ];

        if ($search_charge) {
            unset($post_data["discount"]);
            unset($post_data["fine"]);
            unset($post_data["interest"]);
        }

        $post_data = json_encode($post_data);

        if ($this->debug_enable()) {

            echo "Campos cobranca connect_asaas";
            echo "<br>";
            echo "<pre>";
            var_dump($post_data);
            echo "</pre>";
            echo "<hr>";
        }

        // não tem cobrança no connect_asaas
        if (!$search_charge) {
            $charge = $this->create_charge($api_url, $api_key, $post_data);

            log_activity('Cobrança Boleto/Pix Asaas [Fatura ID: ' . $invoice . ']');
        } else {
            $charge = $this->update_charge($search_charge->id, $post_data);

            log_activity('Cobrança atualizada Asaas [Fatura ID: ' . $invoice . ']');
        }

        if ($billet_only == 1 && $card_only == 0 && $pix_only == 0) {

            redirect(site_url('connect_asaas/checkout/boleto/' . $row['hash']));
        }

        if ($billet_only == 0 && $card_only == 1 && $pix_only == 0) {

            redirect(site_url('connect_asaas/checkout/cartao/' . $row['hash']));
        }

        if ($billet_only == 0 && $card_only == 0 && $pix_only == 1) {

            redirect(site_url('connect_asaas/checkout/qrcode/' . $row['hash']));
        }
        redirect(admin_url('connect_asaas/checkout/index/' . $row['hash']));
    }

    public function calculate_invoice($invoice, $row, $fine, $interest)
    {
        $ci = &get_instance();

        $now       = time();
        $duedate   = strtotime($row["duedate"]);
        $datediff  = $now - $duedate;
        $datevence = $this->getSetting('diasdevencimento');

        $row["subtotal"] = $row["subtotal"] + $row["adjustment"];

        $row["subtotal"] = get_invoice_total_left_to_pay($row["id"], $row["subtotal"]);

        $overdue_days = round($datediff / (60 * 60 * 24));

        $overdue_days_interest = $interest * (int)$overdue_days;

        $overdue_interest = $row["subtotal"] * $overdue_days_interest;

        $overdue_fine = $row["subtotal"] * $fine;

        $updated_total_overdue = number_format($overdue_interest, 2) + $overdue_fine;
        $updated_total         = $row["subtotal"] + number_format($updated_total_overdue / 100, 2);

        $adjustment = $row["adjustment"] + number_format($updated_total_overdue / 100, 2);

        if ($row['status'] != 4) {

            // $update_data = [
            //     'status' => 1,
            //     'adjustment' => $adjustment,
            //     'subtotal' => $updated_total,
            //     'total' => $updated_total,
            //     'duedate' => date('Y-m-d', strtotime("+" . $datevence, " day"))
            // ];

            // $ci->db->where('id', $invoice);
            // $ci->db->update(db_prefix() . 'invoices', $update_data);
        }

        return $updated_total;
    }

    public function get_charge($hash)
    {
        $api_key = $this->api_key;
        $api_url = $this->base_url;
        $charge = $this->search_charge($api_url, $api_key, $hash);
        return $charge;
    }

    public function get_charge2($hash)
    {
        $api_key = $this->api_key;
        $api_url = $this->base_url;
        $charge = $this->search_charge2($api_url, $api_key, $hash);
        return $charge;
    }

    public function search_charge($api_url, $api_key, $hash)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/payments?externalReference={$hash}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $api_key,
                'Content-Type: application/json',
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $payments = json_decode($response);
        $charges  = $payments->data;

        //	 return $charges;

        if ($charges) {
            foreach ($charges as $charge) {
                if ($charge->externalReference == $hash) {
                    return $charge;
                }
            }
        }
    }

    public function listar_todas_cobrancas_e_atualizar($api_url, $api_key, $hash = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/payments?externalReference=$hash",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $payments = json_decode($response);
        foreach ($payments->data as $billet) {
            $data = array(
                'asaas_cobranca_id' => $billet->id
            );
            $this->ci->db->where('hash', $billet->externalReference);
            $this->ci->db->update(db_prefix() . 'invoices', $data);
        }
    }

    public function search_charge2($api_url, $api_key, $hash)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/payments",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);

        $payments = json_decode($response);

        $charges = $payments->data;

        $response = [];

        if ($charges) {
            foreach ($charges as $charge) {
                if ($charge->externalReference == $hash) {
                    $response[] = $charge;
                }
            }
        }

        return $response;
    }

    public function debug_enable()
    {
        return $this->getSetting('debug') == '1';
    }


    function recuperar_uma_unica_cobranca($fatura_id_asaas)
    {
        $api_key = $this->api_key;
        $api_url = $this->base_url;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/payments/$fatura_id_asaas",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);

        $payment = json_decode($response);

        return $payment;
    }

    public function atualizar_cobranca_existente($fatura_id_asaas)
    {

        $api_key = $this->api_key;
        $api_url = $this->base_url;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/payments/$fatura_id_asaas",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);

        $payment = json_decode($response);

        return $payment;
    }

    public function charge_billet($invoice)
    {
        if (empty($invoice)) {
            return;
        }
        $api_key = $this->api_key;
        $api_url = $this->base_url;
        $client = $invoice->client;

        $description                 = $this->getSetting('description');
        $interest                    = $this->getSetting('interest_value');
        $fine                        = $this->getSetting('fine_value');
        $discount_value              = $this->getSetting('discount_value');
        $dueDateLimitDays            = $this->getSetting('discount_days');
        $discount_type               = $this->getSetting('discount_type');
        $disable_charge_notification = $this->getSetting('disable_charge_notification');

        if ($disable_charge_notification == '1') {
            $notificationDisabled = true;
        } else {
            $notificationDisabled = false;
        }

        $invoice_number = format_invoice_number($invoice->id);
        $description    = mb_convert_encoding(str_replace("{invoice_number}", $invoice_number, $description), 'UTF-8', 'ISO-8859-1');

        $document   = str_replace('/', '', str_replace('-', '', str_replace('.', '', $client->vat)));
        $postalCode = str_replace('-', '', str_replace('.', '', $client->zip));

        if (!$client->asaas_customer_id) {

            $email_client   = $this->ci->asaas_gateway->get_customer_customfields($client->userid, 'customers', 'customers_email_principal');
            $address_number = $this->ci->asaas_gateway->get_customer_customfields($client->userid, 'customers', 'customers_numero');

            $post_data = json_encode([
                "name"                 => $client->company,
                "email"                => $email_client,
                "cpfCnpj"              => $document,
                "postalCode"           => $postalCode,
                "address"              => $client->address,
                "addressNumber"        => $address_number,
                "complement"           => "",
                "phone"                => $client->phonenumber,
                "mobilePhone"          => $client->phonenumber,
                "externalReference"    => $invoice->hash,
                "notificationDisabled" => $notificationDisabled,
            ]);

            $cliente_create = $this->create_customer($api_url, $api_key, $post_data);

            $cliente_id = $cliente_create['id'];

            log_activity('Cliente cadastrado no Asaas [Cliente ID: ' . $cliente_id . ']');

            $this->ci->db->where('userid', $client->userid)->update(db_prefix() . 'clients', ['asaas_customer_id' => $cliente_id]);

            if ($this->debug_enable()) {
                echo "Campos cadastro";
                echo "<br>";
                echo "<pre>";
                var_dump($post_data);
                echo "</pre>";
                echo "Cliente cadastrado no Asaas ID " . $cliente_id;
                echo "<hr>";
            }
        } else {
            // se existir recupera os dados para cobranca
            $cliente_id = $client->asaas_customer_id;
            if ($this->debug_enable()) {
                echo "Cliente já existente ID " . $cliente_id;
                echo "<hr>";
            }
        }

        $discount = NULL;

        $sem_desconto = strpos($invoice->adminnote, "{sem_desconto}", 0);

        if ($discount_type == 1) {

            $type = 'FIXED';

            $discount = [
                'type'             => 'FIXED',
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if ($discount_type == 0) {

            $type = 'PERCENTAGE';

            $discount = [
                'type'             => 'PERCENTAGE',
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if (is_bool($sem_desconto)) {
            $discount = [
                'type'             => $type,
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if ($this->debug_enable()) {
            echo "Tipo desconto" . $discount_type;
            echo "<br>";
            echo "Campos desconto";
            echo "<br>";
            echo "<pre>";
            var_dump($discount);
            echo "</pre>";
            echo "<hr>";
            echo "Sem desconto" . $sem_desconto;
            echo "<br>";
            echo "<pre>";
            var_dump($discount);
            echo "</pre>";
            echo "<hr>";
        }

        $post_data = json_encode([
            "customer"          => $cliente_id,
            "billingType"       => "BOLETO",
            "dueDate"           => $invoice->duedate,
            "value"             => $invoice->total,
            "description"       => $description,
            "externalReference" => $invoice->hash,
            "discount"          => $discount,
            "fine"              => [
                "value" => $fine,
            ],
            "interest" => [
                "value" => $interest,
            ],
            "postalService" => false
        ]);

        log_activity('Payload: ' . $post_data);

        $response = $this->create_charge($api_url, $api_key, $post_data);

        $charge   = json_decode($response, TRUE);

        if (isset($charge['errors'])) {
            log_activity('Erro ao criar cobrança no Asaas [Fatura ID: ' . $invoice->id . ']. Erro: ' . $response);
            return false;
        }

        log_activity('Cobrança Boleto Criada no Asaas [Fatura ID: ' . $invoice->id . ']');

        if ($this->debug_enable()) {
            echo "Cobrança Boleto";
            echo "<br>";
            echo "<pre>";
            var_dump($charge);
            echo "</pre>";
            echo "<hr>";
        }

        return $charge;
    }

    public function charge_credit_card($data)
    {
        if (empty($data)) {
            return;
        }

        $invoice_id = $data['invoice']->id;

        $ci = &get_instance();
        $ci->db->where('id', $invoice_id);
        $invoice = $ci->db->get(db_prefix() . 'invoices')->row();

        $ci->db->select('c.*, cc.email');
        $ci->db->from(db_prefix() . 'clients c');
        $ci->db->join(db_prefix() . 'contacts cc', 'cc.userid = c.userid AND cc.is_primary = 1', 'LEFT');
        $ci->db->where('c.userid', $invoice->clientid);

        $client           = $ci->db->get()->row();
        $description      = $this->getSetting('description');
        $interest         = $this->getSetting('interest_value');
        $fine             = $this->getSetting('fine_value');
        $discount_value   = $this->getSetting('discount_value');
        $dueDateLimitDays = $this->getSetting('discount_days');
        $discount_type    = $this->getSetting('discount_type');

        $api_key  = $this->ci->base_api->getApiKey();
        $api_url  = $this->ci->base_api->getUrlBase();

        $disable_charge_notification = $this->getSetting('disable_charge_notification');

        if ($disable_charge_notification == '1') {
            $notificationDisabled = true;
        } else {
            $notificationDisabled = false;
        }

        $invoice_number = $invoice->prefix . str_pad($invoice->number, 6, "0", STR_PAD_LEFT);
        $description    = mb_convert_encoding(str_replace("{invoice_number}", $invoice_number, $description), 'UTF-8', 'ISO-8859-1');
        $email          = $client->email;
        $address_number = $this->get_customer_customfields($client->userid, 'customers', 'customers_numero');
        $document       = str_replace('/', '', str_replace('-', '', str_replace('.', '', $client->vat)));
        $postalCode     = str_replace('-', '', str_replace('.', '', $client->zip));

        if (!$client->asaas_customer_id) {

            $post_data = json_encode([
                "name"                 => $client->company,
                "email"                => $email,
                "phone"                => $client->phonenumber,
                "cpfCnpj"              => $document,
                "postalCode"           => $postalCode,
                "address"              => $client->address,
                "addressNumber"        => $address_number,
                "complement"           => "",
                "phone"                => $client->phonenumber,
                "mobilePhone"          => $client->phonenumber,
                "externalReference"    => $invoice,
                "notificationDisabled" => $notificationDisabled,
            ]);


            $cliente_create = $this->create_customer($api_url, $api_key, $post_data);

            $cliente_id = $cliente_create['id'];

            $this->ci->db->where('userid', $client->userid)->update(db_prefix() . 'clients', ['asaas_customer_id' => $cliente_id]);

            log_activity('Cliente cadastrado no Asaas [Cliente ID: ' . $cliente_id . ']');

            if ($this->debug_enable()) {
                echo "Campos cadastro";
                echo "<br>";
                echo "<pre>";
                var_dump($post_data);
                echo "</pre>";
                echo "Cliente cadastrado no Asaas ID" . $cliente_id;
                echo "<hr>";
            }
        } else {
            // se existir recupera os dados para cobranca
            $cliente_id = $client->asaas_customer_id;
            if ($this->debug_enable()) {
                echo "Cliente jÁ existente ID" . $cliente_id;
                echo "<hr>";
            }
        }

        if ($invoice->status == 4) { // 4= indica que está vencido

            $now                   = time();
            $duedate               = strtotime($invoice->duedate);
            $datediff              = $now - $duedate;
            $invoice->subtotal     = $invoice->subtotal + $invoice->adjustment;
            $invoice->subtotal     = get_invoice_total_left_to_pay($invoice->id, $invoice->subtotal);
            $overdue_days          = round($datediff / (60 * 60 * 24));
            $overdue_days_interest = $interest * (int)$overdue_days;
            $overdue_interest      = $invoice->subtotal * $overdue_days_interest;
            $overdue_fine          = $invoice->subtotal * $fine;
            $updated_total_overdue = number_format($overdue_interest, 2) + $overdue_fine;
            $updated_total         = $invoice->subtotal + number_format($updated_total_overdue / 100, 2);
            $adjustment            = $invoice->adjustment + number_format($updated_total_overdue / 100, 2);
            $update_data           = [
                'status'     => 1,
                'adjustment' => $adjustment,
                'subtotal'   => $updated_total,
                'total'      => $updated_total,
                'duedate'    => date('Y-m-d', strtotime("+10 day"))
            ];


            $ci->db->where('id', $invoice_id);
            $ci->db->update(db_prefix() . 'invoices', $update_data);

            $search_charge = $this->search_charge($api_url, $api_key, $invoice->hash);

            $discount = NULL;

            $sem_desconto = strpos($invoice->adminnote, "{sem_desconto}", 0);

            if ($discount_type == 1) {

                $type = 'FIXED';

                $discount = [
                    'type'             => 'FIXED',
                    "value"            => $discount_value,
                    "dueDateLimitDays" => $dueDateLimitDays,
                ];
            }

            if ($discount_type == 0) {

                $type = 'PERCENTAGE';

                $discount = [
                    'type'             => 'PERCENTAGE',
                    "value"            => $discount_value,
                    "dueDateLimitDays" => $dueDateLimitDays,
                ];
            }

            if (is_bool($sem_desconto)) {
                $discount = [
                    'type'             => $type,
                    "value"            => $discount_value,
                    "dueDateLimitDays" => $dueDateLimitDays,
                ];
            }

            if ($this->debug_enable()) {
                echo "Tipo desconto" . $discount_type;
                echo "<br>";
                echo "Campos desconto";
                echo "<br>";
                echo "<pre>";
                var_dump($discount);
                echo "</pre>";
                echo "<hr>";
                echo "Sem desconto" . $sem_desconto;
                echo "<br>";
                echo "<pre>";
                var_dump($discount);
                echo "</pre>";
                echo "<hr>";
            }

            $post_data = json_encode([
                "customer"          => $search_charge->customer,
                "billingType"       => $search_charge->billingType,
                "dueDate"           => date('Y-m-d', strtotime("+10 day")),
                "value"             => $updated_total,
                "description"       => $search_charge->description,
                "externalReference" => $invoice->hash,
                "discount"          => $discount,
                "fine"              => [
                    "value" => $fine,
                ],
                "interest" => [
                    "value" => $interest,
                ],
                "postalService" => false
            ]);

            $charge = $this->update_charge($search_charge->id, $post_data);

            log_activity('Cobran�a atualizada Asaas [Fatura ID: ' . $search_charge->id . ']');

            return $charge;
        }


        $installmentValue = number_format($invoice->total / intval($data["card"]["installmentCount"]), 2);

        $discount = NULL;

        $sem_desconto = strpos($invoice->adminnote, "{sem_desconto}", 0);

        if ($discount_type == 1) {

            $type = 'FIXED';

            $discount = [
                'type'             => 'FIXED',
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if ($discount_type == 0) {

            $type = 'PERCENTAGE';

            $discount = [
                'type'             => 'PERCENTAGE',
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if (is_bool($sem_desconto)) {
            $discount = [
                'type'             => $type,
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        $invoice->total = get_invoice_total_left_to_pay($invoice->id, $invoice->subtotal);

        $post_data = json_encode([
            "customer"          => $cliente_id,
            "billingType"       => 'CREDIT_CARD',
            "dueDate"           => date('Y-m-d'),
            "value"             => $invoice->total,
            "description"       => $description,
            "externalReference" => $invoice->hash,
            "installmentCount"  => $data["card"]["installmentCount"],
            "installmentValue"  => $installmentValue,
            "creditCard"        => [
                "holderName"  => $data["card"]["holderName"],
                "number"      => $data["card"]["number"],
                "expiryMonth" => $data["card"]["expiryMonth"],
                "expiryYear"  => $data["card"]["expiryYear"],
                "ccv"         => $data["card"]["cvv"]
            ],
            "creditCardHolderInfo" => [
                "name"              => $client->company,
                "email"             => $email,
                "cpfCnpj"           => $document,
                "postalCode"        => $postalCode,
                "addressNumber"     => $address_number,
                "addressComplement" => "",
                "phone"             => $client->phonenumber,
                "mobilePhone"       => $client->phonenumber
            ],
            "discount" => $discount,
            "fine"     => [
                "value" => $fine,
            ],
            "interest" => [
                "value" => $interest,
            ],
            "postalService" => false
        ]);

        $charge = $this->create_charge($api_url, $api_key, $post_data);

        log_activity('Cobrança cartão de credito Asaas [Fatura ID: ' . $invoice_id . ']');

        return $charge;
    }

    public function charge_pix($data)
    {
        if (empty($data)) {
            return;
        }

        $ci      = &get_instance();
        $invoice = $data['invoice']->id;
        $ci->db->where('id', $invoice);
        $row = $ci->db->get(db_prefix() . "invoices")->row_array();

        $ci->db->select('c.*, cc.email');
        $ci->db->from(db_prefix() . 'clients c');
        $ci->db->join(db_prefix() . 'contacts cc', 'cc.userid = c.userid AND cc.is_primary = 1', 'LEFT');
        $ci->db->where('c.userid', $row['clientid']);
        $client = $ci->db->get()->row_array();

        $api_key  = $this->ci->base_api->getApiKey();
        $api_url  = $this->ci->base_api->getUrlBase();

        $description                 = $this->getSetting('description');

        $interest                    = $this->getSetting('interest_value');

        $fine                        = $this->getSetting('fine_value');

        $discount_value              = $this->getSetting('discount_value');

        $dueDateLimitDays            = $this->getSetting('discount_days');

        $discount_type               = $this->getSetting('discount_type');

        $disable_charge_notification = $this->getSetting('disable_charge_notification');

        if ($disable_charge_notification == '1') {
            $notificationDisabled = false;
        } else {
            $notificationDisabled = true;
        }

        $invoice_number = $row['prefix'] . str_pad($row['number'], 6, "0", STR_PAD_LEFT);

        $description    = mb_convert_encoding(str_replace("{invoice_number}", $invoice_number, $description), 'UTF-8', 'ISO-8859-1');

        $document       = str_replace('/', '', str_replace('-', '', str_replace('.', '', $client['vat'])));

        $postalCode     = str_replace('-', '', str_replace('.', '', $client->zip));

        $customer       = $this->search_customer($api_url, $api_key, $document);

        if ($customer['totalCount'] == "0") {

            $post_data = [
                "name"                 => $client['company'],
                "email"                => $client['email'],
                "cpfCnpj"              => $document,
                "postalCode"           => $postalCode,
                "address"              => $client['address'],
                "addressNumber"        => $client['numero'],
                "phone"                => $client['phonenumber'],
                "mobilePhone"          => $client['phonenumber'],
                "complement"           => "",
                "externalReference"    => $invoice,
                "notificationDisabled" => $notificationDisabled,
            ];

            $post_data      = json_encode($post_data);

            $cliente_create = $this->create_customer($api_url, $api_key, $post_data);

            $cliente_id     = $cliente_create['id'];

            log_activity('Cliente cadastrado no Asaas [Cliente ID: ' . $cliente_id . ']');

            if ($this->debug_enable()) {
                echo "Campos cadastro";
                echo "<br>";
                echo "<pre>";
                var_dump($post_data);
                echo "</pre>";
                echo "Cliente cadastrado no Asaas ID" . $cliente_id;
                echo "<hr>";
            }
        } else {
            // se existir recupera os dados para cobranca
            $cliente_id = $customer['data'][0]['id'];
            if ($this->debug_enable()) {
                echo "Cliente já existente ID" . $cliente_id;
                echo "<hr>";
            }
        }


        if ($row['status'] == '4') {

            $now                   = time();
            $duedate               = strtotime($row["duedate"]);
            $datediff              = $now - $duedate;
            $datevence             = $this->getSetting('diasdevencimento');;
            $row["subtotal"]       = $row["subtotal"] + $row["adjustment"];
            $row["subtotal"]       = get_invoice_total_left_to_pay($row["id"], $row["subtotal"]);
            $overdue_days          = round($datediff / (60 * 60 * 24));
            $overdue_days_interest = $interest * (int)$overdue_days;
            $overdue_interest      = $row["subtotal"] * $overdue_days_interest;
            $overdue_fine          = $row["subtotal"] * $fine;
            $updated_total_overdue = number_format($overdue_interest, 2) + $overdue_fine;
            $updated_total         = $row["subtotal"] + number_format($updated_total_overdue / 100, 2);
            $adjustment            = $row["adjustment"] + number_format($updated_total_overdue / 100, 2);

            $update_data = [
                'status'     => 1,
                'adjustment' => $adjustment,
                'subtotal'   => $updated_total,
                'total'      => $updated_total,
                'duedate'    => date('Y-m-d', strtotime("+" . $datevence, " day"))
            ];

            $ci->db->where('id', $invoice);
            $ci->db->update(db_prefix() . 'invoices', $update_data);

            $search_charge = $this->search_charge($api_url, $api_key, $row["hash"]);

            $discount = NULL;

            $sem_desconto = strpos($row["adminnote"], "{sem_desconto}", 0);

            if ($discount_type == 1) {

                $type = 'FIXED';

                $discount = [
                    'type'             => 'FIXED',
                    "value"            => $discount_value,
                    "dueDateLimitDays" => $dueDateLimitDays,
                ];
            }

            if ($discount_type == 0) {

                $type = 'PERCENTAGE';

                $discount = [
                    'type'             => 'PERCENTAGE',
                    "value"            => $discount_value,
                    "dueDateLimitDays" => $dueDateLimitDays,
                ];
            }

            if (is_bool($sem_desconto)) {
                $discount = [
                    'type'             => $type,
                    "value"            => $discount_value,
                    "dueDateLimitDays" => $dueDateLimitDays,
                ];
            }

            $post_data = json_encode([
                "customer"          => $search_charge->customer,
                "billingType"       => $search_charge->billingType,
                "dueDate"           => date('Y-m-d', strtotime("+10 day")),
                "value"             => $updated_total,
                "description"       => $search_charge->description,
                "externalReference" => $row['hash'],
                "discount"          => $discount,
                "fine"              => [
                    "value" => $fine,
                ],
                "interest" => [
                    "value" => $interest,
                ],
                "postalService" => false
            ]);

            $charge = $this->update_charge($search_charge->id, $post_data);

            return $charge;
        }

        $discount = NULL;

        $sem_desconto = strpos($row["adminnote"], "{sem_desconto}", 0);

        if ($discount_type == 1) {

            $type = 'FIXED';

            $discount = [
                'type'             => 'FIXED',
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if ($discount_type == 0) {

            $type = 'PERCENTAGE';

            $discount = [
                'type'             => 'PERCENTAGE',
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        if (is_bool($sem_desconto)) {
            $discount = [
                'type'             => $type,
                "value"            => $discount_value,
                "dueDateLimitDays" => $dueDateLimitDays,
            ];
        }

        $row["total"] = get_invoice_total_left_to_pay($row["id"], $row["subtotal"]);

        $post_data    = json_encode([
            "customer"          => $cliente_id,
            "billingType"       => "PIX",
            "dueDate"           => $row['duedate'],
            "value"             => $row['total'],
            "description"       => $description,
            "externalReference" => $row['hash'],
            "discount"          => $discount,
            "fine"              => [
                "value" => $fine,
            ],
            "interest" => [
                "value" => $interest,
            ],
            "postalService" => false
        ]);

        $charge = $this->create_charge($api_url, $api_key, $post_data);

        $charge = json_decode($charge, TRUE);

        log_activity('Cobrança PIX Asaas [Fatura ID: ' . $invoice . ']');

        return $charge;
    }

    public function create_charge($api_url, $api_key, $post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/payments");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent,
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function create_qrcode($payment_id)
    {
        $api_key = $this->api_key;
        $api_url = $this->base_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/payments/" . $payment_id . "/pixQrCode");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent,
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function get_customer($cpfCnpj)
    {
        $api_key = $this->api_key;
        $api_url = $this->base_url;
        $customer = $this->search_customer($api_url, $api_key, $cpfCnpj);
        return $customer;
    }

    public function search_customer($api_url, $api_key, $cpfCnpj)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/customers?cpfCnpj=" . $cpfCnpj,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $customer = json_decode($response, TRUE);
        return $customer;
    }

    public function update_charge($charge_id, $post_data)
    {
        $api_key = $this->api_key;
        $api_url = $this->base_url;
        $curl    = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/payments/" . $charge_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",

            CURLOPT_POSTFIELDS => $post_data,

            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, TRUE);
        return $response;
    }


    public function delete_charge($charge_id)
    {
        $api_key = $this->api_key;
        $api_url = $this->base_url;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/payments/" . $charge_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "DELETE",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, TRUE);
        return $response;
    }

    public function create_customer($api_url, $api_key, $post_data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/customers",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $post_data,
            CURLOPT_HTTPHEADER     => array(
                "Content-Type: application/json",
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $customer = json_decode($response, TRUE);
        return $customer;
    }

    public function get_webhook($api_key, $api_url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent,
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, TRUE);
    }

    public function create_webhook($api_key, $api_url, $post_data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent,
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, TRUE);
    }

    public function get_webhook_invoice($api_key, $api_url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook/invoice");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent,
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, TRUE);
    }

    public function create_webhook_invoice($api_key, $api_url, $post_data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook/invoice");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent,
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, TRUE);
    }

    public function get_webhook_transfer($api_key, $api_url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook/transfer");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent,
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function create_webhook_transfer($api_key, $api_url, $post_data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/webhook/transfer");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent,
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, TRUE);
    }

    public function get_customers($api_key, $api_url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url . "/v3/customers?limit=100");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "access_token: " . $api_key,
            "User-Agent: " . $this->user_agent,
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, TRUE);
    }

    public function charges($api_key, $api_url, $offset = NULL)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $api_url . "/v3/payments?limit=100",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    function get_state_abbr()
    {
        $estadosBrasileiros = [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins'
        ];
        return $estadosBrasileiros;
    }

    public function get_customer_customfields($id, $fieldto, $slug)
    {
        $ci = &get_instance();
        $ci->db->where('fieldto', $fieldto);
        $ci->db->where('slug', $slug);
        $customfields = $ci->db->get(db_prefix() . 'customfields')->result();
        foreach ($customfields as $row) {
            $ci->db->where('fieldto', $fieldto);
            $ci->db->where('relid', $id);
            $ci->db->where('fieldid', $row->id);
            $customfieldsvalues = $ci->db->get(db_prefix() . 'customfieldsvalues')->row();
        }
        if (isset($customfieldsvalues)) {
            return $customfieldsvalues->value;
        } else {
            return NULL;
        }
    }


    public function receive_in_cash($id, $post_data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $this->base_url . "/v3/payments/{$id}/receiveInCash",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POSTFIELDS     => $post_data,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $this->api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, TRUE);

        return $response;
    }

    public function undo_received_in_cash($id)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $this->base_url . "/v3/payments/{$id}/undoReceivedInCash",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $this->api_key,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, TRUE);

        return $response;
    }



    public function addSubscription($post_data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_url . "/v3/subscriptions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                'access_token: ' . $this->api_key,
                "User-Agent: " . $this->user_agent,
            ],
        ]);

        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $errorMessage = curl_error($curl);
            curl_close($curl); // Sempre feche o curl antes de lançar a exceção
            throw new \Exception('Curl error: ' . $errorMessage);
        }

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);
            return ['status_code' => $httpCode, 'response' => $response];
        }
    }
    /**
     * @param mixed $post_data
     * @param mixed $id
     *
     * @return [type]
     */
    public function updateSubscription($post_data, $id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_url . "/v3/subscriptions/{$id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                'access_token: ' . $this->api_key,
                "User-Agent: " . $this->user_agent,
            ],
        ]);

        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $errorMessage = curl_error($curl);
            curl_close($curl); // Sempre feche o curl antes de lançar a exceção
            throw new \Exception('Curl error: ' . $errorMessage);
        }

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);
            return ['status_code' => $httpCode, 'response' => $response];
        }
    }

    public function getPayment($subscriptionId)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_url . "/v3/subscriptions/{$subscriptionId}/payments",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                'access_token: ' . $this->api_key,
                "User-Agent: " . $this->user_agent,
            ],
        ]);

        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $errorMessage = curl_error($curl);
            curl_close($curl); // Sempre feche o curl antes de lançar a exceção
            throw new \Exception('Curl error: ' . $errorMessage);
        }

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);
            return ['status_code' => $httpCode, 'response' => $response];
        }
    }

    public function removeSubscription($subscriptionId)
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_url . "/v3/subscriptions/{$subscriptionId}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                'access_token: ' . $this->api_key,
                "User-Agent: " . $this->user_agent,
            ],
        ]);

        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $errorMessage = curl_error($curl);
            curl_close($curl); // Sempre feche o curl antes de lançar a exceção
            throw new \Exception('Curl error: ' . $errorMessage);
        }

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);
            return ['status_code' => $httpCode, 'response' => $response];
        }
    }

    /**
     * Get the value of user_agent
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * Set the value of user_agent
     *
     * @return  self
     */
    public function setUser_agent($user_agent)
    {
        $this->user_agent = $user_agent;

        return $this;
    }
}
