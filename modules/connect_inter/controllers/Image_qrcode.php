<?php

defined('BASEPATH') or exit('No direct script access allowed');

require FCPATH . "/modules/connect_inter/phpqrcode/qrlib.php";


class Image_qrcode extends ClientsController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $qrcode    = $this->input->get("qrcode");
        $invoiceid = $this->input->get("invoiceid");
        $hash      = $this->input->get("hash");
        check_invoice_restrictions($invoiceid , $hash);
        echo QRcode::png($qrcode);
    }
}
