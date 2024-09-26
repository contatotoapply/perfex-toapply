<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Customer
{
    protected $apiKey;
    protected $apiUrl;
    protected $ci;
    protected $user_agent;
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('connect_asaas/base_api');
        $this->apiKey     = $this->ci->base_api->getApiKey();
        $this->apiUrl     = $this->ci->base_api->getUrlBase();
        $this->user_agent = 'Perfex CRM';
    }

    public function get_by_id($customerId)
    {
        if (!$customerId) return false;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . "/v3/customers/$customerId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "access_token: " . $this->apiKey,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $customer = json_decode($response);
        return $customer;
    }

    /**
     * @param mixed $post_data
     *
     * @return [type]
     */
    public function create($post_data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $this->apiUrl . "/v3/customers",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode($post_data),
            CURLOPT_HTTPHEADER     => array(
                "Content-Type: application/json",
                "access_token: " .  $this->apiKey,
                "User-Agent: " . $this->user_agent,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $customer = json_decode($response, TRUE);
        return $customer;
    }

    /**
     * @param mixed $customerId
     * @param mixed $post_data
     *
     * @return [type]
     */
    public function update($customerId, $post_data)
    {
        if (!$customerId) return false;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . '/v3/customers/' . $customerId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'access_token: ' . $this->apiKey,
                "User-Agent: " . $this->user_agent,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);

        return $response;
    }

    public function delete($customer_id)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '/v3/customers/' . $customer_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Access_token: ' . $this->apiKey;
        $headers[] = 'User-Agent: ' . $this->user_agent;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $response = json_decode($response, true);

        if (isset($response['deleted'])) {
            return true;
        }
        return false;
    }

    /**
     * @param mixed $cpfCnpj
     *
     * @return [type]
     */
    public function search_customer($cpfCnpj)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $this->apiUrl . "/v3/customers?cpfCnpj=" . $cpfCnpj,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "access_token: " . $this->apiKey,
                "User-Agent: " . 'Perfex CRM',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $customer = json_decode($response, TRUE);

        if (isset($customer['data']) && count($customer['data']) > 0) {
            return $customer['data'][0];
        }
        return false;
    }
}
