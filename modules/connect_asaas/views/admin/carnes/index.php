<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">

            <div class="col-md-12 animated fadeIn">
                <div class="_buttons">
                    <a type="button" onclick="edit_carnes()"
                        class="btn btn-primary pull-left display-block tw-mb-2 sm:tw-mb-4">
                        <i class="fa-regular fa-plus tw-mr-1"></i><?= _l("asaas_carne_create") ?></a>
                </div>
            </div>

            <div class="col-md-12 animated fadeIn">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Asaas Carne > carnes</h4>
                    </div>
                    <div class="panel-body">
                        <div class="">
                            <div class="panel-table-full">
                                <div class="">
                                    <table class="table dt-table" data-order-col="0" data-order-type="desc">
                                        <thead>
                                            <tr>
                                                <th id="col_clientid"><?= _l('asaas_carne_carnes_clientid') ?></th>
                                                <th id="col_date"><?= _l('asaas_carne_carnes_date') ?></th>
                                                <th id="col_duedate"><?= _l('asaas_carne_carnes_duedate') ?></th>
                                                <th id="col_item_id"><?= _l('asaas_carne_carnes_item_id') ?></th>
                                                <th id="col_installment_quantity"><?= _l('asaas_carne_carnes_installment_quantity') ?></th>
                                                <th id="col_total"><?= _l('asaas_carne_carnes_total') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($carnes as $row) {
                                                $url = admin_url('asaas_carne/carnes');
                                                echo '<tr>';
                                                echo '<td>' . $row->clientid;
                                                echo '<div class="row-options">';
                                                echo '<a href="#" onclick="edit_carnes(' . $row->id . ')" class="editar">' . _l('edit') . '</a> |';
                                                echo '<a href="' . $url . '/delete/' . $row->id . '" class="text-danger _delete">' . _l("delete") . '</a>';
                                                echo '</div>';
                                                echo '</td>';
                                                echo '<td>' . $row->date . '</td>';
                                                echo '<td>' . _d($row->duedate) . '</td>';
                                                echo '<td>' . $row->item_id . '</td>';
                                                echo '<td>' . $row->installment_quantity . '</td>';
                                                echo '<td>' . $row->total . '</td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- Modal -->
<div class="modal fade" id="modal_carnes" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url("asaas_carne/carnes/store")); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l("Asaas Carne"); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class='form-group' app-field-wrapper='clientid'>
                            <label for='clientid' class='control-label'><?= _l('asaas_carne_carnes_clientid') ?></label>
                            <input type='text' id='clientid' data-input-type='text' name='clientid' class='form-control' />
                        </div>
                        <div class='form-group' app-field-wrapper='date'>
                            <label for='date' class='control-label'><?= _l('asaas_carne_carnes_date') ?></label>
                            <input type='date' id='date' data-input-type='date' name='date' class='form-control' />
                        </div>
                        <div class='form-group' app-field-wrapper='duedate'>
                            <label for='duedate' class='control-label'><?= _l('asaas_carne_carnes_duedate') ?></label>
                            <input type='date' id='duedate' data-input-type='date' name='duedate' class='form-control' />
                        </div>
                        <div class='form-group' app-field-wrapper='item_id'>
                            <label for='item_id' class='control-label'><?= _l('asaas_carne_carnes_item_id') ?></label>
                            <input type='number' data-input-type='int' id='item_id' name='item_id' class='form-control' />
                        </div>
                        <div class='form-group' app-field-wrapper='installment_quantity'>
                            <label for='installment_quantity' class='control-label'><?= _l('asaas_carne_carnes_installment_quantity') ?></label>
                            <input type='number' data-input-type='int' id='installment_quantity' name='installment_quantity' class='form-control' />
                        </div>
                        <div class='form-group' app-field-wrapper='total'>
                            <label for='total' class='control-label'><?= _l('asaas_carne_carnes_total') ?></label>
                            <input type='text' id='total' data-input-type='text' name='total' class='form-control' />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l("close"); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l("submit"); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
</div>
<!-- /.modal -->
<script>
    function edit_carnes(id) {
        const carnes = $("#modal_carnes");
        if (id == undefined) {
            carnes.find('.edit-title').text('<?= _l('asaas_carne_create') ?>');
            carnes.find('form')[0].reset();
            carnes.modal("show");
            return;
        }
        requestGetJSON("asaas_carne/carnes/show/" + id).done(function(response) {
            var inputs = carnes.find(":input");
            var inputsWithName = inputs.filter('[name]');
            inputsWithName.each(function() {
                const input = $(this);
                const name = input.attr('name');
                const inputType = input.data('input-type');
                if (inputType == 'select_multiple') {
                    const nameTest = name.replace('[]', '');
                    const parsedValue = JSON.parse(response.data[nameTest]);
                    if (Array.isArray(parsedValue)) {
                        input.val(parsedValue);
                    }
                    return;
                }
                if (inputType == 'checkbox') {
                    const nameTest = name.replace('[]', '');
                    const parsedValue = JSON.parse(response.data[nameTest]);
                    if (Array.isArray(parsedValue)) {
                        input.val(parsedValue);
                    }
                    return;
                }
                if (inputType == 'radio') {
                    input.filter('[value="' + response.data[name] + '"]').prop('checked', true);
                    return;
                }
                if (inputType == 'editor_html') {
                    tinymce.get(name).setContent(response.data[name]);
                    return;
                }
                if (inputType == 'file' || inputType == 'image') {
                    return;
                }
                if (inputType) {
                    input.val(response.data[name]);
                }
            });
            carnes.modal("show");
            carnes.find("form").attr("action", "<?= admin_url("asaas_carne/carnes/update") ?>/" + id);
            carnes.find('.edit-title').text('<?= _l('asaas_carne_edit') ?>');
            $('.selectpicker').selectpicker('refresh');
        });
    }
</script>
</body>

</html>
