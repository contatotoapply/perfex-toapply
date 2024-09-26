<!-- Criar Instância quando a APIKey For Global -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('Criar Nova Instância'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'name'); ?>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <img class="img-responsive text-center" style="margin:0px auto;" id="getQrCode" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" id="btnCreateInstance" class="btn btn-primary"><?php echo _l('save'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="connectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('sms_zap_instance_connect'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <img class="img-responsive text-center" style="margin:0px auto;" id="getQrCodeConnect" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" id="btnCreateInstance" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('sms_zap_instance_info'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- profilePictureUrl -->
                                <img src="" class="img-responsive img-circle" style="margin:0px auto;width:30%;" />
                                <table class="table dt-table" data-paginate="false" data-order-type="desc">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-right"><?= _l('sms_zap_instance_name') ?>:</td>
                                            <td><b></b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Dono:</td>
                                            <td><b></b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><?= _l('name') ?>:</td>
                                            <td><b></b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><?= _l('profileStatus') ?>:</td>
                                            <td><b></b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><?= _l('serverUrl') ?>:</td>
                                            <td><b></b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><?= _l('apikey') ?>:</td>
                                            <td><b></b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><?= _l('status') ?>:</td>
                                            <td><b></b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
