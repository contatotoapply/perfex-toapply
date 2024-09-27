<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'CONCAT(`firstname`, \' \', `lastname`) as name',
    'tenants',
    'updated_at',
];

$sTable       = perfex_saas_table('super_assistants');
$sIndexColumn = 'id';

$staffTable = db_prefix() . 'staff';
$join = ['LEFT JOIN ' . $staffTable . ' ON ' . $staffTable . '.staffid = ' . $sTable . '.staff_id'];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], [$sTable . '.id', 'staff_id', 'permissions']);

$output  = $result['output'];
$rResult = $result['rResult'];


foreach ($rResult as $aRow) {

    $tenants = empty($aRow['tenants']) || $aRow['tenants'] === '[]' ? 'all' : implode(',', json_decode($aRow['tenants']));
    $row = [
        '<a href="' . admin_url('staff/member/' . $aRow['staff_id']) . '">' . $aRow['name'] . '</a>',
        $tenants,
        $aRow['updated_at'],
    ];

    $editLink = admin_url(SAAS_SUPER_ASSISTANT_MODULE_NAME . '/manage/edit/' . $aRow['id']);

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';

    $options .= '<a href="' . $editLink . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
            <i class="fa-regular fa-pen-to-square fa-lg"></i>
        </a>';


    $options .= form_open(admin_url(SAAS_SUPER_ASSISTANT_MODULE_NAME . '/manage/delete/' . $aRow['id'])) .
        form_hidden('id', $aRow['id']) .
        '<button class="tw-bg-transparent tw-border-0 tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
            <i class="fa-regular fa-trash-can fa-lg"></i>
        </button>' . form_close();
    $options .= '</div>';

    $row[] = $options;

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
