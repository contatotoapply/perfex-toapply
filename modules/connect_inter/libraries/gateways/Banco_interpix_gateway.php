<?php

class Banco_interpix_gateway extends App_gateway
{
    public function __construct()
    {
        /**
        * Call App_gateway __construct function
        */
        parent::__construct();

        /**
         * Gateway unique id - REQUIRED
	 *
         * * The ID must be alphanumeric
         * * The filename (Example_gateway.php) and the class name must contain the id as ID_gateway
         * * In this case our id is "example"
         * * Filename will be Example_gateway.php (first letter is uppercase)
         * * Class name will be Example_gateway (first letter is uppercase)
         */
        $this->setId('connect_inter_pix');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Banco V3 Inter Pix');

        /**
         * Add gateway settings
         * You can add other settings here
         * to fit for your gateway requirements
         *
         * Currently only 3 field types are accepted for gateway
         *
         * 'type'=>'yes_no'
         * 'type'=>'input'
         * 'type'=>'textarea'
         *
         */
        $this->setSettings(array(
            // array(
            //     'name' => 'CCInter',
            //     'label' => 'Conta Corrente',
            //     'type'=>'input'
            // ),
            // array(
            //     'name' => 'cnpjCPFBeneficiario',
            //     'label' => 'CPF/CNPJ do beneficiário',
            //     'type'=>'input'
            // ),
            // array(
            //     'name' => 'clientId',
            //     'label' => 'Client ID',
            //     'type'=>'input'
            // ),
            // array(
            //     'name' => 'clientSecret',
            //     'label' => 'Client Secret',
            //     'type'=>'input'
            // ),
            // array(
            //     'name' => 'pixKey',
            //     'label' => 'Chave Pix (CNPJ sem caracteres)',
            //     'type'=>'input'
            // ),
            // array(
            //     'name' => 'beneficiario',
            //     'label' => 'Beneficiário (Razão social)',
            //     'type'=>'input'
            // ),
            // array(
            //     'name' => 'cidade',
            //     'label' => 'Cidade (Registro da conta)',
            //     'type'=>'input'
            // ),
            array(
                'name' => 'currencies',
                'label' => 'settings_paymentmethod_currencies',
                'default_value' => 'BRL'
            ),
        ));

    }

    /**
     * Each time a customer click PAY NOW button on the invoice HTML area, the script will process the payment via this function.
     * You can show forms here, redirect to gateway website, redirect to Codeigniter controller etc..
     * @param  array $data - Contains the total amount to pay and the invoice information
     * @return mixed
     */
    public function process_payment($data)
    {
        header("");
        /**chamar a função postPix */
        // include(__DIR__.'/../controllers/Pix.php');
        // $pix = new Pix();
        // $dados = [
        //     'id' => $data['invoiceid'],
        //     'data' => $data['invoice']
        // ];
        // $pix->postPix($dados);
        // //print_r(json_encode($data, true));
        die;
    }
}
