<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Added at: 2024-06-27 20:23:13
class Migration_Version_136 extends App_module_migration
{

    public function up()
    {
        update_option('asaas_campo_personalisado_email_padrao', 'customers_email_principal');
        update_option('asaas_campo_personalisado_numero_endereco_padrao', 'customers_numero');
        update_option('asaas_campo_personalisado_bairro_padrao', 'customers_bairro');
    }

}
