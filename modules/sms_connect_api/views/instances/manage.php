<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons tw-mb-2 sm:tw-mb-4">
                     <?php
                    if(staff_can('create', 'sms_connect_api')){
                    ?>
                    <a data-toggle="modal" data-target="#exampleModal" class="btn btn-primary mright5 pull-left display-block">
                        <i class="fa-regular fa-plus tw-mr-1"></i><?= _l('sms_zap_new_instance') ?></a>
                        <?php
                    }
                        if(is_admin()){
                        ?>
                    <a href="<?= admin_url('settings?group=sms') ?>" class="btn btn-info mright5 pull-left display-block">
                        <i class="fa-regular fa-plus tw-mr-1"></i><?= _l('sms_zap_new_settings') ?></a>
                        <?php
                        }
                        ?>
                    <div class="clearfix"></div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="alert alert-warning" role="alert">
                            <b><?= _l('notifications_zap_engine_info_checked_radio') ?></b>
                        </div>
                      <table class="table dt-table" data-order-col="1" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $current_instance_name = removerAcentosECedilha(get_option('sms_notifications_zap_engine_library_whatsapp_api_instance_name_selected'));
                                    foreach ($instances as $instance) {
                                        $instanceName = $instance->instance->instanceName;
                                        $checked      = removerAcentosECedilha($instanceName) == $current_instance_name ? 'checked' : '';
                                    ?>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input <?= $checked ?> value="<?= $instanceName ?>" type="radio" name="instance_name" /> <?= $instanceName ?></label>
                                                <div class="row-options">
                                                    <a href="javascript:void(0)" data-info='<?= json_encode($instance->instance) ?>'
                                                     class="text-link open-modal-info"><?= _l('sms_zap_instance_info') ?></a>
                                                    |
                                                    <a href="<?= admin_url('sms_connect_api/instances') . "/delete?instanteName=" . $instanceName ?>" class="text-danger _delete">Excluir</a>
                                                    <?php
                                                    if (in_array($instance->instance->status, ['close', 'connecting'])) {
                                                    ?>
                                                        |
                                                        <a href="<?= admin_url('sms_connect_api/instances') . "/restart?instanteName=" . $instanceName ?>">
                                                            <?= _l('sms_zap_instance_restart') ?></a>
                                                        |
                                                        <a class="open-connect-modal" href="javascript:void(0)">
                                                            <?= _l('sms_zap_instance_connect') ?></a>
                                                        </a>
                                                    <?php } ?> <?php
                                                                if ($instance->instance->status == 'open') {
                                                                ?>
                                                        | <a href="<?= admin_url('sms_connect_api/instances') . "/logout?instanteName=" . $instanceName ?>" class="text-danger _delete"><?= _l('notifications_zap_engine_logout') ?></a>
                                                    <?php
                                                                }
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($instance->instance->status == 'open') { ?>
                                                    <span class="label label-success"><?= _l('sms_zap_active') ?></span>
                                                <?php } elseif ($instance->instance->status == 'connecting') { ?>
                                                    <span class="label label-warning"><?= _l('sms_zap_connecting') ?></span>
                                                <?php } elseif ($instance->instance->status == 'close') { ?>
                                                    <span class="label label-danger"><?= _l('sms_zap_close') ?></span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view('sms_connect_api/instances/connect_modal');
?>
<?php init_tail(); ?>

<script>
    $(function() {

        function getStatus(instanceName) {
            $.ajax({
                url: '<?= admin_url('sms_connect_api/instances/get_status_connection_instance') ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    instanceName,
                },
                success: function(response) {
                    if (response.status == 'success') {
                        if (response.state == 'open') {
                            window.location.reload();
                        }
                    }
                }
            });
        }

        $('#btnCreateInstance').click(function() {
            const $self = $(this);

            $self.attr('disabled', true).html(
                '<i class="fa fa-spinner fa-spin"></i> <?= _l('notifications_zap_engine_creating') ?>');

            var name = $('input[name="name"]').val();

            if (name) {
                $.ajax({
                    url: '<?= admin_url('sms_connect_api/instances/create_instance') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        instanceName: name
                    },
                    success: function(response) {
                        if (response.status == 'SUCCESS') {
                            $self.attr('disabled', false).html('<?= _l('submit') ?>');
                            $('#getQrCode').attr('src', response.data.base64);
                            getStatus(name);
                            setInterval(() => {
                                getStatus(name);
                            }, 5000);
                        } else {
                            $self.attr('disabled', false).html('<?= _l('submit') ?>');
                            alert_float("danger", response.message);
                        }
                    }
                });
            }
        });

        $('input[name="instance_name"]').on('change', function() {
            const $self = $(this);
            const $value = $self.val();
            $.ajax({
                url: admin_url + 'sms_connect_api/instances/update_instance_name',
                method: 'POST',
                data: {
                    instance_name: $value
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        alert_float("success",
                            "<?= _l('sms_zap_instance_update_instance_name') ?>");
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Erro na requisição:', error);
                }
            });
        });

        $('.open-connect-modal').on('click', function() {
            const $self = $(this);
            const $instanceName = $self.parent().parent().find('input[name="instance_name"]').val();
            $.ajax({
                url: admin_url + 'sms_connect_api/instances/connect?instanteName=' + $instanceName,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'SUCCESS') {
                        $('#getQrCodeConnect').attr('src', response.data.base64);
                        getStatus($instanceName);
                        setInterval(() => {
                            getStatus($instanceName);
                        }, 5000);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Erro na requisição:', error);
                }
            });
            $('#connectModal').modal('show');
        });

        $('.open-modal-info').on('click', function() {

            const $self = $(this);

            const $info = $self.data('info');

            $('#infoModal').find('img').attr('src', $info.profilePictureUrl);
            $('#infoModal').find('td:eq(1)').html('<b>' + $info.instanceName + '</b>');
            $('#infoModal').find('td:eq(3)').html('<b>' + $info.owner + '</b>');
            $('#infoModal').find('td:eq(5)').html('<b>' + $info.profileName + '</b>');
            $('#infoModal').find('td:eq(7)').html('<b>' + $info.profileStatus + '</b>');
            $('#infoModal').find('td:eq(9)').html('<b>' + $info.serverUrl + '</b>');
            $('#infoModal').find('td:eq(11)').html('<b>' + $info.apikey + '</b>');
            $('#infoModal').find('td:eq(13)').html('<b>' + $info.status + '</b>');
            $('#infoModal').modal('show');
        });
    });
</script>
</body>

</html>
