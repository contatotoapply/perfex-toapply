<?php

$CI = &get_instance();

// $array = json_decode(get_option('assas_helpers'), TRUE);

// if ($array) {

//     function readFilesRecursively($folderPath)
//     {
//         $directoryIteratorModule = module_dir_path('connect_asaas');

//         $files = [];

//         $directoryIterator = new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS);

//         $recursiveIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::SELF_FIRST);

//         foreach ($recursiveIterator as $file) {
//             if ($file->isFile()) {
//                 $filename = $file->getFilename();
//                 if ($filename != 'install.php') {
//                     $filePath              = $file->getPathname();
//                     $currentHash           = hash_file('sha256', $filePath);
//                     $fileExtracted         = str_replace($directoryIteratorModule, '', $filePath);
//                     $files[$fileExtracted] = $currentHash;
//                 }
//             }
//         }
//         return $files;
//     }

//     $arquivos = readFilesRecursively(module_dir_path('connect_asaas'));

//     foreach ($arquivos as $key => $arquivo) {
//         try {
//             if (isset($array[$key]) && $array[$key] !== $arquivo) {
//                 // update_option("assas_helpers_files_{$arquivo}", $arquivo);
//                 update_option('assas_helpers_status', 0);
//                 return;
//             } else {
//                 update_option('assas_helpers_status', 1);
//             }
//         } catch (\Exception $th) {
//             echo 'error';
//         }
//     }
// }

if (!function_exists('asaas_formatar_status')) {
    function asaas_formatar_status($status)
    {

        switch ($status) {
            case 'AUTHORIZED':
                echo '<span class="badge badge-success" style="color: #00D071;">EMITIDA</span>';
                break;
            case 'ERROR':
                echo '<span class="badge badge-error" style="color: #0000008a">ERRO NA EMISSÃO</span>';
                break;
            case 'SCHEDULED':
                echo '<span class="badge badge-error" style="color:#ff0a0a; ">AGENDADA</span>';
                break;
            case 'SYNCHRONIZED':
                echo '<span class="badge badge-error" style="color: #001074;">ENVIADA PARA PREFEITURA</span>';
                break;
            case 'CANCELED':
                echo '<span class="badge badge-error" style="color: #001074;">CANCELADA</span>';
                break;
            case 'CANCELLATION_DENIED':
                echo '<span class="badge badge-defakult" style="color: #ff0a0a;">CANCELAMENTO NEGADO</span>';
                break;
            default:
                echo '<span class="badge badge-success" style="color: #00D071;">' . $status . '</span>';
                break;
        }
    }
}


if (!function_exists('replace_accents')) {
    function replace_accents($str)
    {
        $unwanted_array = array(
            'Š' => 'S',
            'š' => 's',
            'Ž' => 'Z',
            'ž' => 'z',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'Þ' => 'B',
            'ß' => 'Ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y'
        );
        return  strtr($str, $unwanted_array);
    }
}

if (!function_exists('moeda2float')) {

    function moeda2float($value)
    {
        if (empty($value)) {
            return null;
        }

        $new = str_replace('.', '', $value);

        return str_replace(',', '.', $new);
    }
}


if (!function_exists('soNumeros')) {
    /**
     * @param mixed $str
     *
     * @return [type]
     */
    function soNumeros($str)
    {
        return preg_replace('/\D/', '', $str);
    }
}

if (!function_exists('is_emissao_empty')) {
    // asaas_invoice_on_event: 0
    function is_emissao_empty($clientid)
    {
        $value = get_custom_field_value($clientid, 'customers_configuracao_da_emissao', 'customers');
        return empty($value);
    }
}

if (!function_exists('is_emissao_avulsa')) {
    // asaas_invoice_on_event: 0
    function is_emissao_avulsa($clientid)
    {
        $value = get_custom_field_value($clientid, 'customers_configuracao_da_emissao', 'customers');
        return $value  == 'Emissão avulsa';
    }
}

if (!function_exists('is_criacao_fatura')) {
    // asaas_invoice_on_event: 1
    function is_criacao_fatura($clientid)
    {
        $value = get_custom_field_value($clientid, 'customers_configuracao_da_emissao', 'customers');
        return $value  == 'Emitir na criação da fatura';
    }
}

if (!function_exists('is_confirmacao_pagamento')) {
    // asaas_invoice_on_event: 2
    function is_confirmacao_pagamento($clientid)
    {
        $value = get_custom_field_value($clientid, 'customers_configuracao_da_emissao', 'customers');
        return $value  == 'Emitir na confirmação de pagamento';
    }
}

if (!function_exists('mapShippingColumns')) {

    function mapShippingColumns($data, $expense = false)
    {
        $shipping_fields = [
            'shipping_street',
            'shipping_city',
            'shipping_city',
            'shipping_state',
            'shipping_zip',
            'shipping_country',
        ];

        if (!isset($data['include_shipping'])) {
            foreach ($shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = null;
                }
            }
            $data['show_shipping_on_invoice'] = 1;
            $data['include_shipping']         = 0;
        } else {
            // We dont need to overwrite to 1 unless its coming from the main function add
            if (!DEFINED('CRON') && $expense == false) {
                $data['include_shipping'] = 1;
                // set by default for the next time to be checked
                if (isset($data['show_shipping_on_invoice']) && ($data['show_shipping_on_invoice'] == 1 || $data['show_shipping_on_invoice'] == 'on')) {
                    $data['show_shipping_on_invoice'] = 1;
                } else {
                    $data['show_shipping_on_invoice'] = 0;
                }
            }
            // else its just like they are passed
        }

        return $data;
    }
}



if (!function_exists('addFatura')) {

    function addFatura($data, $expense = false)
    {
        $CI = &get_instance();

        $CI->load->model('invoices_model');

        $data['prefix'] = get_option('invoice_prefix');

        $data['number_format'] = get_option('invoice_number_format');

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = !DEFINED('CRON') ? get_staff_user_id() : 0;

        $data['cancel_overdue_reminders'] = isset($data['cancel_overdue_reminders']) ? 1 : 0;

        $data['allowed_payment_modes'] = isset($data['allowed_payment_modes']) ? serialize($data['allowed_payment_modes']) : serialize([]);

        $billed_tasks = isset($data['billed_tasks']) ? array_map('unserialize', array_unique(array_map('serialize', $data['billed_tasks']))) : [];

        $billed_expenses = isset($data['billed_expenses']) ? array_map('unserialize', array_unique(array_map('serialize', $data['billed_expenses']))) : [];

        $tags = isset($data['tags']) ? $data['tags'] : '';

        if (isset($data['save_as_draft'])) {
            $data['status'] = $CI->invoices_model::STATUS_DRAFT;
            unset($data['save_as_draft']);
        } elseif (isset($data['save_and_send_later'])) {
            $data['status'] = $CI->invoices_model::STATUS_DRAFT;
            unset($data['save_and_send_later']);
        }

        if (isset($data['recurring'])) {
            if ($data['recurring'] == 'custom') {
                $data['recurring_type']   = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
                $data['recurring']        = $data['repeat_every_custom'];
            }
        } else {
            $data['custom_recurring'] = 0;
            $data['recurring']        = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['hash'] = app_generate_hash();

        $items = [];

        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }

        $data = mapShippingColumns($data, $expense);

        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        if (isset($data['status']) && $data['status'] == $CI->invoices_model::STATUS_DRAFT) {
            $data['number'] = $CI->invoices_model::STATUS_DRAFT_NUMBER;
        }

        $data['duedate'] = isset($data['duedate']) && empty($data['duedate']) ? null : $data['duedate'];

        $hook = hooks()->apply_filters('before_invoice_added', [
            'data'  => $data,
            'items' => $items,
        ]);

        $data  = $hook['data'];
        $items = $hook['items'];

        $CI->db->insert(db_prefix() . 'invoices', $data);
        $insert_id = $CI->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            handle_tags_save($tags, $insert_id, 'invoice');

            foreach ($billed_tasks as $key => $tasks) {
                foreach ($tasks as $t) {
                    $CI->db->select('status')->where('id', $t);

                    $_task = $CI->db->get(db_prefix() . 'tasks')->row();

                    $taskUpdateData = [
                        'billed'     => 1,
                        'invoice_id' => $insert_id,
                    ];

                    if ($_task->status != Tasks_model::STATUS_COMPLETE) {
                        $taskUpdateData['status']       = Tasks_model::STATUS_COMPLETE;
                        $taskUpdateData['datefinished'] = date('Y-m-d H:i:s');
                    }

                    $CI->db->where('id', $t);
                    $CI->db->update(db_prefix() . 'tasks', $taskUpdateData);
                }
            }

            foreach ($billed_expenses as $key => $val) {
                foreach ($val as $expense_id) {
                    $CI->db->where('id', $expense_id);
                    $CI->db->update(db_prefix() . 'expenses', [
                        'invoiceid' => $insert_id,
                    ]);
                }
            }

            update_invoice_status($insert_id);

            // Update next invoice number in settings if status is not draft
            if (!$CI->invoices_model->is_draft($insert_id)) {
                $CI->invoices_model->increment_next_number();
            }

            foreach ($items as $key => $item) {
                if ($itemid = add_new_sales_item_post($item, $insert_id, 'invoice')) {
                    if (isset($billed_tasks[$key])) {
                        foreach ($billed_tasks[$key] as $_task_id) {
                            $CI->db->insert(db_prefix() . 'related_items', [
                                'item_id'  => $itemid,
                                'rel_id'   => $_task_id,
                                'rel_type' => 'task',
                            ]);
                        }
                    } elseif (isset($billed_expenses[$key])) {
                        foreach ($billed_expenses[$key] as $_expense_id) {
                            $CI->db->insert(db_prefix() . 'related_items', [
                                'item_id'  => $itemid,
                                'rel_id'   => $_expense_id,
                                'rel_type' => 'expense',
                            ]);
                        }
                    }
                    _maybe_insert_post_item_tax($itemid, $item, $insert_id, 'invoice');
                }
            }

            update_sales_total_tax_column($insert_id, 'invoice', db_prefix() . 'invoices');

            if (!DEFINED('CRON') && $expense == false) {
                $lang_key = 'invoice_activity_created';
            } elseif (!DEFINED('CRON') && $expense == true) {
                $lang_key = 'invoice_activity_from_expense';
            } elseif (DEFINED('CRON') && $expense == false) {
                $lang_key = 'invoice_activity_recurring_created';
            } else {
                $lang_key = 'invoice_activity_recurring_from_expense_created';
            }

            $CI->invoices_model->log_invoice_activity($insert_id, $lang_key);

            hooks()->do_action('after_invoice_added', $insert_id);

            return $insert_id;
        }

        return false;
    }
}


if (!function_exists('prepararFatura')) {

    function prepararFatura($invoice_id)
    {
        $CI = &get_instance();

        $new_recurring_invoice_action = get_option('new_recurring_invoice_action');

        $CI->load->model('invoices_model');

        $_invoice                             = $CI->invoices_model->get($invoice_id);

        $qtd_faturas_para_gerar                 = get_custom_field_value($invoice_id, 'invoice_quantidade_de_parcelas_para_gerar', 'invoice');

        if ($qtd_faturas_para_gerar && $qtd_faturas_para_gerar > 1) {

            $qtd_para_gerar = $qtd_faturas_para_gerar;

            $data_inicial   = $_invoice->duedate;

            $data = new \DateTime($data_inicial);

            $total = $_invoice->total;

            $parcela_valor = $total / $qtd_para_gerar;

            $CI->db->where('id', $invoice_id)
                ->update(db_prefix() . 'invoices', ['subtotal' => $parcela_valor, 'total' => $parcela_valor]);

            $tags      = get_tags_in($invoice_id, 'invoice');
            $prefix    = get_option('invoice_prefix');
            $tags[]    = $prefix . 'MÃE'; // TODO
            handle_tags_save($tags, $invoice_id, 'invoice');
            $invoice_number = format_invoice_number($invoice_id);

            for ($i = 1; $i <= $qtd_para_gerar - 1; $i++) {
                $data->modify('+1 month');
                $last_duedate = $data->format('Y-m-d');
                $new_invoice_data                     = [];
                $new_invoice_data['tags']             = [$prefix . 'FILHA-' . $invoice_number]; // TODO
                $new_invoice_data['clientid']         = $_invoice->clientid;
                $new_invoice_data['number']           = get_option('next_invoice_number');
                $new_invoice_data['date']             = $_invoice->date;
                $new_invoice_data['duedate']          = $last_duedate;

                $new_invoice_data['project_id']       = $_invoice->project_id;
                $new_invoice_data['show_quantity_as'] = $_invoice->show_quantity_as;
                $new_invoice_data['currency']         = $_invoice->currency;
                $new_invoice_data['subtotal']         = $parcela_valor;
                $new_invoice_data['total']            = $parcela_valor;
                $new_invoice_data['adjustment']       = $_invoice->adjustment;
                $new_invoice_data['discount_percent'] = $_invoice->discount_percent;
                $new_invoice_data['discount_total']   = $_invoice->discount_total;
                $new_invoice_data['discount_type']    = $_invoice->discount_type;
                $new_invoice_data['terms']            = clear_textarea_breaks($_invoice->terms);
                $new_invoice_data['sale_agent']       = $_invoice->sale_agent;
                // Since version 1.0.6
                $new_invoice_data['billing_street']               = clear_textarea_breaks($_invoice->billing_street);
                $new_invoice_data['billing_city']                 = $_invoice->billing_city;
                $new_invoice_data['billing_state']                = $_invoice->billing_state;
                $new_invoice_data['billing_zip']                  = $_invoice->billing_zip;
                $new_invoice_data['billing_country']              = $_invoice->billing_country;
                $new_invoice_data['shipping_street']              = clear_textarea_breaks($_invoice->shipping_street);
                $new_invoice_data['shipping_city']                = $_invoice->shipping_city;
                $new_invoice_data['shipping_state']               = $_invoice->shipping_state;
                $new_invoice_data['shipping_zip']                 = $_invoice->shipping_zip;
                $new_invoice_data['shipping_country']             = $_invoice->shipping_country;
                $new_invoice_data['asaas_slip_invoice_parent_id'] = $invoice_id;
                if ($_invoice->include_shipping == 1) {
                    $new_invoice_data['include_shipping'] = $_invoice->include_shipping;
                }
                $new_invoice_data['include_shipping']         = $_invoice->include_shipping;
                $new_invoice_data['show_shipping_on_invoice'] = $_invoice->show_shipping_on_invoice;
                // Determine status based on settings
                if ($new_recurring_invoice_action == 'generate_and_send' || $new_recurring_invoice_action == 'generate_unpaid') {
                    $new_invoice_data['status'] = 1;
                } elseif ($new_recurring_invoice_action == 'generate_draft') {
                    $new_invoice_data['save_as_draft'] = true;
                }
                $new_invoice_data['clientnote']            = clear_textarea_breaks($_invoice->clientnote);
                $new_invoice_data['adminnote']             = '';
                $new_invoice_data['allowed_payment_modes'] = unserialize($_invoice->allowed_payment_modes);
                $new_invoice_data['is_recurring_from']     = $_invoice->id;
                $new_invoice_data['newitems']              = [];
                $key                                       = 1;
                $custom_fields_items                       = get_custom_fields('items');
                foreach ($_invoice->items as $item) {
                    $new_invoice_data['newitems'][$key]['description']      = $item['description'];
                    $new_invoice_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
                    $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
                    $new_invoice_data['newitems'][$key]['unit']             = $item['unit'];
                    $new_invoice_data['newitems'][$key]['taxname']          = [];
                    $taxes                                                  = get_invoice_item_taxes($item['id']);
                    foreach ($taxes as $tax) {
                        // tax name is in format TAX1|10.00
                        array_push($new_invoice_data['newitems'][$key]['taxname'], $tax['taxname']);
                    }
                    $new_invoice_data['newitems'][$key]['rate']  = $item['rate'];
                    $new_invoice_data['newitems'][$key]['order'] = $item['item_order'];

                    foreach ($custom_fields_items as $cf) {
                        $new_invoice_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                        if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                            define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                        }
                    }
                    $key++;
                }
                addFatura($new_invoice_data);
            }
        }
    }
}


if (!function_exists('deleteIvoiceChildren')) {

    function deleteIvoiceChildren($invoice_id)
    {
        $CI = &get_instance();
        $CI->load->model('invoices_model');
        $children_invoies = $CI->db
            ->select('id,status')
            ->where('asaas_slip_invoice_parent_id', $invoice_id)->get(db_prefix() . 'invoices')->result();
        foreach ($children_invoies as $invoice) {
            if ($invoice->status != 2) {
                $CI->invoices_model->delete($invoice->id);
            }
        }
    }
}
