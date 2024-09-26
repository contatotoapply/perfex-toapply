<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$serach_history = $this->db->where('client_id', $client->userid)
    ->get(db_prefix() . 'saudevivamais_search_history')->result();
?>
<?php if (isset($client)) { ?>
    <h4 class="customer-profile-group-heading">
        <?php echo 'Histórico de Consultas' ?></h4>
<?php
} ?>
<table class="table dt-table" data-order-col="1" data-order-type="desc">
    <thead>
        <tr>
            <th><?= _l('NOME DO CLIENTE') ?></th>
            <th><?= _l('CPF') ?></th>
            <th><?= _l('CELULAR') ?></th>
            <th><?= _l('STATUS') ?></th>
            <th><?= _l('COLABORADOR') ?></th>
            <th><?= _l('DATA DA CONSULTA') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($serach_history as $row) { ?>
            <tr>
                <td><?= $row->client_name ?></td>
                <td><?= $row->client_vat ?></td>
                <td><?= $row->client_phonenumber ?></td>
                <td><?= $row->status ?></td>
                <td><?= $row->staff_name ?></td>
                <td><?= _d($row->created_at) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
