<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (has_permission('perfex_saas_companies', '', 'create')) { ?>
                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="<?php echo admin_url(SAAS_SUPER_ASSISTANT_MODULE_NAME . '/manage'); ?>"
                        class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('saas_super_assistant_new'); ?>
                    </a>
                </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable([
                            _l('staff'),
                            _l('saas_super_assistant_companies'),
                            _l('perfex_saas_last_updated'),
                            _l('perfex_saas_options'),
                        ], 'assistants'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
"use strict";
$(function() {
    initDataTable('.table-assistants', window.location.href, undefined, [2], undefined, [2, "desc"]);
});
</script>
</body>

</html>