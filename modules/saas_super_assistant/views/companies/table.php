<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    get_sql_select_client_company(),
    'status',
    'created_at',
];

$sTable       = perfex_saas_table('companies');
$sIndexColumn = 'id';

$clientTable = db_prefix() . 'clients';
$join = ['LEFT JOIN ' . $clientTable . ' ON ' . $clientTable . '.userid = ' . $sTable . '.clientid'];

$assistant = saas_super_assistant_get_assistant('', false);
if (!$assistant) show_404();

$where = [];

$slugs = $assistant->tenants;
$slugs = empty($slugs) || $slugs == '[]' ? [] : json_decode($slugs);
if (!empty($slugs))
    $where[] = " AND `slug` IN ('" . implode("', '", $slugs) . "')";

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [$sTable . '.id', 'userid', 'slug', 'clientid']);

$output  = $result['output'];
$rResult = $result['rResult'];
$CI = &get_instance();

$customFields = $aColumns;
$customFields[1] = "company";

$is_single_package = perfex_saas_is_single_package_mode();
$package_id_col = perfex_saas_column('packageid');

foreach ($rResult as $aRow) {

    $row = [];
    $aRow = (array) $CI->perfex_saas_model->parse_company((object)$aRow);
    $authLink = admin_url(PERFEX_SAAS_MODULE_NAME . '/companies/edit/' . $aRow['id']);
    $viewLink = perfex_saas_tenant_admin_url((object)$aRow);

    for ($i = 0; $i < count($customFields); $i++) {
        $_data = $aRow[$customFields[$i]];

        if ($customFields[$i] == 'name') {
            $_data = $_data . ' (' . $aRow['slug'] . ')';
            $_data .= '<div class="row-options tw-ml-9">';
            $_data .= '<a href="' . admin_url(SAAS_SUPER_ASSISTANT_MODULE_NAME . '/' . SAAS_SUPER_ASSISTANT_MODULE_NAME . '_auth/login_as_assistant/' . $aRow['slug']) . '" target="_blank"><i class="fa-regular fa-share-from-square"></i> ' . _l('login_as_assistant') . '</a>';
            $_data .= '</div>';
        } elseif ($customFields[$i] == 'created_at' || $customFields[$i] == 'updated_at') {
            $_data = _d($_data);
        } elseif ($customFields[$i] == 'status') {
            $className = $_data == 'active' ? 'success' : 'danger';
            $_data = '<span class="badge tw-bg-' . $className . '-200">' . $_data . '</span>';
        }
        $row[] = $_data;
    }

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
