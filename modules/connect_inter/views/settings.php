<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons">
                    <h4><?= _l('connect_inter_settings_certs') ?></h4>
                    <div class="clearfix"></div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open_multipart(admin_url('connect_inter/v3/settings/upload'), ['id' => 'my_form']); ?>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id"><?= _l('Arquivo .crt') ?></label>
                                    <input type="file" name="crt_file" required size="20" accept=".crt" />
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id"><?= _l('Arquivo .key') ?></label>
                                    <input type="file" name="key_file" required size="20" accept=".key"/>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?= admin_url('settings?group=payment_gateways&tab=online_payments_connect_inter_tab') ?>" class="btn btn-warning"><?= _l('settings') ?></a>
                            <button type="submit" class="btn btn-primary"><?= _l('save') ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

</body>

</html>
