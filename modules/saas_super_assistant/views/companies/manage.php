<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable([
                            _l('saas_super_assistance_tenant'),
                            _l('perfex_saas_clients_list_company'),
                            _l('perfex_saas_company_status'),
                            _l('perfex_saas_date_created'),
                        ], 'companies'); ?>
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
        initDataTable('.table-companies', window.location.href, undefined, [2], undefined, [3, "desc"]);
    });
</script>
</body>

</html>