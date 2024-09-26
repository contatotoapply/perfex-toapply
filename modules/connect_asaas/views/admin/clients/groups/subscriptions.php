<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="panel_s">
                    <div class="panel-body">

                        <!-- INÍCIO -->

                        <?php if (isset($client)) { ?>
                            <h4 class="customer-profile-group-heading"><?php echo _l('subscriptions'); ?></h4>
                            <?php if (has_permission('subscriptions', '', 'create')) { ?>
                                <a type="button" class="btn btn-primary mbot15" data-toggle="modal" data-target="#exampleModal">
                                    <i class="fa-regular fa-plus tw-mr-1"></i>
                                    <?php echo _l('new_subscription'); ?>
                                </a>
                            <?php } ?>
                            <div class="clearfix"></div>
                        <?php } ?>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog">
                                <?php echo form_open_multipart(admin_url('connect_asaas/subscriptions/store'), ['id' => 'subscriptionForm']); ?>
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">
                                            <span class="add-title"><?php echo _l('Nova Assinatura'); ?></span>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">

                                                <!-- INÍCIO DO FORMULÁRIO -->
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="tw-bg-neutral-50 tw-rounded-md tw-p-6 tw-border tw-border-solid tw-border-neutral-200 tw-mb-4">
                                                            <div class="form-group">
                                                                <label for="itemid"> <small class="req text-danger">*</small>Itens</label>
                                                                <div class="dropdown bootstrap-select bs3" style="width: 100%;">
                                                                    <select id="itemid" name="itemid" class="selectpicker" data-live-search="true" data-width="100%" required data-none-selected-text="Selecione o plano do saude viva mais">
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($items as $item) {
                                                                        ?>
                                                                            <option value="<?= $item->id; ?>">
                                                                                <?= $item->description; ?></option>
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

                                                        <!-- ID DO CLIENTE -->
                                                        <input type="hidden" name="clientid" value="<?= $client->userid; ?>" />
                                                        <input type="hidden" name="customer" value="<?= $client->asaas_customer_id; ?>" />
                                                        <input type="hidden" name="vat" value="<?= $client->vat; ?>" />
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
                                <!-- /.modal-content -->
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                        <!-- FIM -->


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
</body>

</html>
