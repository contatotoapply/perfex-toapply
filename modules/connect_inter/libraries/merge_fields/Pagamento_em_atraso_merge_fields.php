<?php



defined('BASEPATH') or exit('No direct script access allowed');

class Pagamento_em_atraso_merge_fields extends App_merge_fields
{
    public function build()
    {
        return  [
            [
                'name'      => 'Order Id',
                'key'       => '{order_id}',
                'available' => ['order'],
            ]
        ];
    }

    public function format($data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields["{{$key}}"] = $value;
        }

        $this->ci->load->model('invoices_model');

        $invoice = $this->ci->invoices_model->get($data['invoice_id']);

        $media_folder                                  = $this->ci->app->get_media_folder();
        if ($invoice->bi_pix != null) {
            $pix = json_decode($invoice->bi_pix);
            $fields['{pix_copia_e_cola}'] = $pix->pixCopiaECola;
            $image_qrcode                 = base_url($media_folder . "/banco_inter/invoices/invoice_{$invoice->id}_{$invoice->hash}_qrcode.png");
            $fields['{pix_qrcode}']       = $image_qrcode;
        }


        return $fields;
    }
}
