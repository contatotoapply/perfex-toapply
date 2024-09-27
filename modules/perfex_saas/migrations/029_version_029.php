<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_029 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $CI = &get_instance();

        perfex_saas_install();

        $companies = $CI->perfex_saas_model->companies();
        foreach ($companies as $company) {
            $dsn_array = false;
            try {
                $dsn_array = perfex_saas_get_company_dsn($company);
            } catch (\Throwable $th) {
                //throw $th;
            }
            if ($dsn_array !== false)
                perfex_saas_sync_tenant_with_seed($company);
        }
    }

    public function down()
    {
    }
}