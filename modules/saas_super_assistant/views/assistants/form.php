<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $select_attr = ['multiple' => 'true', 'class' => 'selectpicker display-block', 'data-width' => '100%']; ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo isset($member) ? $member->firstname : _l('saas_super_assistant_new'); ?>
                    </span>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">

                        <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                        <?php $this->load->view('authentication/includes/alerts'); ?>

                        <?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'assistants_form']); ?>

                        <?php if (isset($assistant)) echo form_hidden('id', $assistant->id); ?>

                        <!-- assistant -->
                        <?php
                        $selected = $assistant->staff_id ?? '';
                        echo render_select('staff_id', $staff, ['staffid', ['firstname', 'lastname']], 'staff', $selected);
                        ?>

                        <!-- Select tenant -->
                        <?php
                        $selected = !empty($assistant->tenants) ? json_decode($assistant->tenants) : '';
                        echo render_select('tenants[]', $tenants, ['slug', ['name', 'slug']], 'perfex_saas_tenants', $selected, $select_attr);
                        ?>
                        <small class="-tw-mt-2 tw-block text-warning"><?= _l('saas_super_assistant_tenants_hint'); ?></small>

                        <!-- check permission table --->
                        <hr />
                        <h4 class="font-medium mbot15 bold"><?php echo _l('staff_add_edit_permissions'); ?></h4>
                        <?php
                        $permissionsData = ['permissions' => isset($assistant) ? json_decode($assistant->permissions, true) : []];
                        $this->load->view('assistants/permissions', $permissionsData);
                        ?>

                        <div class="text-right">
                            <button type="submit" data-loading-text="<?= _l('perfex_saas_saving...'); ?>" data-form="#companies_form" class="btn btn-primary mtop15 mbot15"><?php echo _l('perfex_saas_submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    "use strict";
    appValidateForm($("#assistants_form"), {
        staff_id: "required",
        permissions: "required",
    });
</script>
</body>

</html>