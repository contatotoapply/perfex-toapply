<?php



defined('BASEPATH') or exit('No direct script access allowed');

class Emaill_notifica_contato_fatura_excluida_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Contact Firstname',
                'key'       => '{contact_firstname}',
                'available' => [
                    'notificar_contato_fatura_excluida',
                ],
                'templates' => [
                    'gdpr-removal-request',
                    'contract-expiration',
                    'send-contract',
                    'contract-comment-to-client',
                    'task-added-attachment-to-contacts',
                    'task-commented-to-contacts',
                    'task-status-change-to-contacts',
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Client Formatted Id',
                'key'       => '{client_formatted_id}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'gdpr-removal-request',
                    'contract-expiration',
                    'send-contract',
                    'contract-comment-to-client',
                    'task-added-attachment-to-contacts',
                    'task-commented-to-contacts',
                    'task-status-change-to-contacts',
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Contact Lastname',
                'key'       => '{contact_lastname}',
                'available' => [
                    'notificar_contato_fatura_excluida',
                ],
                'templates' => [
                    'gdpr-removal-request',
                    'contract-expiration',
                    'send-contract',
                    'contract-comment-to-client',
                    'task-added-attachment-to-contacts',
                    'task-commented-to-contacts',
                    'task-status-change-to-contacts',
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Contact Phone Number',
                'key'       => '{contact_phonenumber}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'gdpr-removal-request',
                    'contract-expiration',
                    'send-contract',
                    'contract-comment-to-client',

                ],
            ],
            [
                'name'      => 'Contact Title',
                'key'       => '{contact_title}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'contract-expiration',
                    'send-contract',
                    'contract-comment-to-client',
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Contact Email',
                'key'       => '{contact_email}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'gdpr-removal-request',
                    'contract-expiration',
                    'send-contract',
                    'contract-comment-to-client',
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1' ? 'Contact Public Consent URL' : '',
                'key'       => is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1' ? '{contact_public_consent_url}' : '',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'gdpr-removal-request',
                    'contract-expiration',
                    'send-contract',
                    'contract-comment-to-client',

                ],
            ],
            [
                'name'      => 'Client Company',
                'key'       => '{client_company}',
                'available' => [
                    'notificar_contato_fatura_excluida',
                ],
                'templates' => [
                    'gdpr-removal-request',
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Client Phone Number',
                'key'       => '{client_phonenumber}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'gdpr-removal-request',
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Client Country',
                'key'       => '{client_country}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'gdpr-removal-request',
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Client City',
                'key'       => '{client_city}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
            ],
            [
                'name'      => 'Client Zip',
                'key'       => '{client_zip}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'invoices-batch-payments',
                ]
            ],
            [
                'name'      => 'Client State',
                'key'       => '{client_state}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'invoices-batch-payments',
                ]
            ],
            [
                'name'      => 'Client Address',
                'key'       => '{client_address}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'template' => [
                    'invoices-batch-payments',
                ]
            ],
            [
                'name'      => 'Client Vat Number',
                'key'       => '{client_vat_number}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'template' => [
                    'invoices-batch-payments',
                ]
            ],
            [
                'name'      => 'Client ID',
                'key'       => '{client_id}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
            ],
            [
                'name'      => 'Statement To',
                'key'       => '{statement_to}',
                'available' => [],
                'templates' => [
                    'client-statement',
                ],
            ],
            [
                'name'      => 'Customer Files Admin Link',
                'key'       => '{customer_profile_files_admin_link}',
                'available' => [
                    'notificar_contato_fatura_excluida'
                ],
                'templates' => [
                    'new-customer-profile-file-uploaded-to-staff',
                ],
            ],
        ];
    }

    public function format($data)
    {
        $fields = [];
        $invoice = $data['invoice'];
        $client = $data['client'];

        unset($data['invoice']);
        unset($data['client']);
        unset($invoice['client']);

        $contact_id = $data['id'];
        $client_id  = $client['userid'];
        $invoice_id = $invoice['id'];

        $ids = ['invoice_id' => $invoice_id, 'client_id' => $client_id, 'contact_id' => $contact_id];

        $data = array_merge($data, $client, $invoice,  $ids);

        foreach ($data as $key => $value) {
            $fields["{{$key}}"] = $value;
        }

        $request_uri = $_SERVER['REQUEST_URI'];
        $hash = $invoice['hash'];
        if (strpos($request_uri, 'delete') !== false) {
            $fields['{invoice_link}']               = base_url('clients/invoices');
        } else {
            $fields['{invoice_link}']               = base_url('invoice/' . $invoice['id'] . '/' . $hash);
        }

        $fields['{invoice_duedate}']            = _d($invoice['duedate']);
        $fields['{invoice_number}']             = format_invoice_number($invoice['id']);

        //--
        $fields['{invoice_sale_agent}']         = get_staff_full_name($invoice['sale_agent']);
        $fields['{total_days_overdue}']         = get_total_days_overdue($invoice['duedate']);
        $fields['{invoice_date}']               = _d($invoice['date']);
        $fields['{invoice_status}']             = format_invoice_status($invoice['status'], '', false);
        $fields['{project_name}']               = get_project_name_by_id($invoice['project_id']);
        //--


        $fields['{contact_firstname}']          = $data['firstname'];
        $fields['{contact_lastname}']           = $data['lastname'];
        $fields['{contact_email}']              = $data['email'];
        $fields['{contact_phonenumber}']        = $data['phonenumber'];
        $fields['{contact_title}']              = $data['title'];
        $fields['{contact_public_consent_url}'] = contact_consent_url($data['id']);
        $fields['{email_verification_url}']     = site_url('verification/verify/' . $data['id'] . '/' . $data['email_verification_key']);


        if (!empty($client['vat'])) {
            $fields['{client_vat_number}'] = $client['vat'];
        }

        $fields['{customer_profile_files_admin_link}'] = admin_url('clients/client/' . $client['userid'] . '?group=attachments');
        $fields['{client_company}']                    = $client['company'];
        $fields['{client_phonenumber}']                = $client['phonenumber'];
        $fields['{client_country}']                    = get_country_short_name($client['country']);
        $fields['{client_city}']                       = $client['city'];
        $fields['{client_zip}']                        = $client['zip'];
        $fields['{client_state}']                      = $client['state'];
        $fields['{client_address}']                    = $client['address'];

        $media_folder                                  = $this->ci->app->get_media_folder();
        // if (isset($invoice['bi_pix']) && !is_null($invoice['bi_pix'])) {
        //     $pix = json_decode($invoice['bi_pix']);
        //     $fields['{pix_copia_e_cola}'] = $pix->pixCopiaECola;
        //     $image_qrcode                         = base_url($media_folder . "/banco_inter/invoices/invoice_{$invoice_id}_{$hash}_qrcode.png");
        //     $fields['{pix_qrcode}']       = $image_qrcode;
        // }

        return $fields;
    }
}
