<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons">
                    <?php if (has_permission('subscriptions', '', 'create')) { ?>
                        <a type="button" class="btn btn-primary mbot15" data-toggle="modal" data-target="#exampleModal">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('new_subscription'); ?>
                        </a>
                    <?php } ?>
                    <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right width300">
                            <li>
                                <a href="#" data-cview="all" onclick="dt_custom_view('','.table-subscriptions',''); return false;">
                                    <?php echo _l('all'); ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li class="<?php if (!$this->input->get('status') || $this->input->get('status') && $this->input->get('status') == 'not_subscribed') {
                                            echo 'active';
                                        } ?>">
                                <a href="#" data-cview="not_subscribed" onclick="dt_custom_view('not_subscribed','.table-subscriptions','not_subscribed'); return false;">
                                    <?php echo _l('subscription_not_subscribed'); ?>
                                </a>
                            </li>
                            <?php foreach (get_subscriptions_statuses() as $status) { ?>
                                <li class="<?php if ($status['filter_default'] == true && !$this->input->get('status') || $this->input->get('status') == $status['id']) {
                                                echo 'active';
                                            } ?>">
                                    <a href="#" data-cview="<?php echo 'subscription_status_' . $status['id']; ?>" onclick="dt_custom_view('subscription_status_<?php echo $status['id']; ?>','.table-subscriptions','subscription_status_<?php echo $status['id']; ?>'); return false;">
                                        <?php echo _l('subscription_' . $status['id']); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="panel_s tw-mt-2 sm:tw-mt-4">
                    <div class="_filters _hidden_inputs">
                        <?php
                        foreach (get_subscriptions_statuses() as $status) {
                            $val = '';
                            if (!$this->input->get('status') || $this->input->get('status') && $this->input->get('status') == $status['id']) {
                                $val = $status['id'];
                            }
                            if (!$this->input->get('status') && $status['id'] == 'canceled') {
                                $val = '';
                            }
                            echo form_hidden('subscription_status_' . $status['id'], $val);
                        }
                        echo form_hidden('not_subscribed', !$this->input->get('status') || $this->input->get('status') && $this->input->get('status') == 'not_subscribed' ? 'not_subscribed' : '');
                        ?>
                    </div>
                    <div class="panel-body">

                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg">
                            <i class="fa-brands fa-stripe" aria-hidden="true"></i>
                            <?php echo _l('subscriptions_summary'); ?>
                        </h4>

                        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-8 tw-gap-2 tw-mt-2 sm:tw-mt-4">
                            <?php foreach (subscriptions_summary() as $summary) { ?>
                                <div class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center lg:last:tw-border-r-0">
                                    <span class="tw-font-semibold tw-mr-3 tw-text-lg">
                                        <?php echo $summary['total']; ?>
                                    </span>
                                    <span style="color:<?php echo $summary['color']; ?>">
                                        <?php echo _l('subscription_' . $summary['id']); ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                        <hr class="hr-panel-separator" />
                        <div class="panel-table-full">
                            <?php hooks()->do_action('before_subscriptions_table'); ?>
                            <?php
                            $this->load->view('connect_asaas/admin/subscriptions/table_html', ['url' => admin_url('subscriptions/table')]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('connect_asaas/subscriptions/store'), ['id' => 'subscriptionForm']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('Nova Assinatura'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <!-- INÍCIO DO FORMULÁRIO -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tw-bg-neutral-50 tw-rounded-md tw-p-6 tw-border tw-border-solid tw-border-neutral-200 tw-mb-4">

                                    <div class="form-group select-placeholder f_client_id">
                                        <label for="client_id" class="control-label"><span class="text-danger">*
                                            </span><?php echo _l('contract_client_string'); ?></label>
                                        <select id="client_id" required name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" <?php echo isset($contract) && $contract->signed == 1 ? ' disabled' : ''; ?>>

                                            <?php
                                            foreach ($clients as $cl) {
                                                echo '<option value="' . $cl->userid . '">' . $cl->company . '</option>';
                                            }
                                            ?>
                                            <?php
                                            if (isset($client)) {
                                            ?>
                                                <option selected value="<?= $client->userid; ?>"><?= $client->company; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="itemid"> <small class="req text-danger">*</small>Itens</label>
                                        <div class="dropdown bootstrap-select bs3" style="width: 100%;">
                                            <select id="itemid" name="itemid" class="selectpicker" data-live-search="true" data-width="100%" required data-none-selected-text="Selecione o plano do saude viva mais">
                                                <option value=""></option>
                                                <?php
                                                foreach ($items as $item) {
                                                ?>
                                                    <optgroup label="<?= $item->description . ' - ' . app_format_money($item->rate, get_base_currency()->name);; ?>">
                                                        <option value="<?= $item->id; ?>">
                                                            <?= preg_replace('/\n/', "\n", $item->long_description); ?></option>
                                                    </optgroup>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="first_billing_date_wrapper"><i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-placement="right" data-title="O campo nextDueDate define quando será feito a primeira cobrança da assinatura, que irá seguir o ciclo conforme configurado."></i>
                                        <div class="form-group" app-field-wrapper="date"><label for="nextDueDate" class="control-label"><small class="req text-danger">*</small> Primeira
                                                data de faturamento</label>
                                            <div class="input-group date">
                                                <input type="text" id="nextDueDate" name="nextDueDate" class="form-control datepicker" data-lazy="false" required data-date-min-date="<?= date('Y-m-d') ?>" value="" autocomplete="off">
                                                <div class="input-group-addon">
                                                    <i class="fa-regular fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-primary" data-loading-text="Por favor, aguarde ..." data-form="#subscriptionForm">
                                Salvar </button>
                        </div>
                        <!-- FIM DO FORMULÁRIO -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <input id="assinatura_asaas_id" name="assinatura_asaas_id"/>
        <?php echo form_close(); ?>
    </div>
</div>
<!-- FIM -->

<?php init_tail(); ?>

<script>
    function editar_assinatura(assinatura) {
        console.log(assinatura);
        const $modal = $('#exampleModal');

        $modal.find('#nextDueDate').val(assinatura.date);

        $modal.find('#assinatura_asaas_id').val(assinatura.asaas_subscription_id);

        $modal.find('#itemid').val(assinatura.asaas_item_id);

        $modal.find('form').attr('action', '<?= admin_url('connect_asaas/subscriptions/update') ?>/' + assinatura.id);

        $modal.find('.edit-title').text('Editar servidor');

        $modal.find('#client_id').html('');
        $html = '';
        $html += '<option value="' + assinatura.clientid + '" selected>' + assinatura.company + '</option>';
        $modal.find('#client_id').append($html);
        $modal.find('#client_id').selectpicker('refresh');
        $('.selectpicker').selectpicker('refresh');

        $modal.modal({
            backdrop: 'static',
            keyboard: false
        });
        $('.selectpicker').selectpicker('refresh');

    }
    $(document).ready(function() {
        const hasClient = <?= isset($client) ? 1 : 0 ?>;
        if (hasClient) {
            $('#exampleModal').modal('show');
        }
    });
</script>
</body>

</html>
