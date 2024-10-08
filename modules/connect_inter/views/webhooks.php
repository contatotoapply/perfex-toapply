<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="<?php echo base_url('modules/api/assets/main.css'); ?>" rel="stylesheet" type="text/css" />
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 style="padding:0;margin:0px;">Webhooks</h4>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php echo form_open(admin_url('connect_inter/v3/webhooks/update')); ?>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Webhook</label>
                            <?php
                            if ($webhook) {
                                if(isset($webhook_cadastrado)){
                                    echo '<pre>';
                                    print_r($webhook_cadastrado);
                                    echo '</pre>';
                                }
                            ?>
                                <input type="text" name="web_hook_url" class="form-control" id="exampleInputEmail1"
                                    value="<?= $webhook ?>" aria-describedby="emailHelp"
                                    placeholder="Digite o webhook"> <br />
                                Data do cadastro: <?= date('d/m/Y \à\s H:i:s', strtotime($webhook_created_at)) ?> <br>
                            <?php
                            } else {
                            ?>
                                <input type="text" name="web_hook_url" class="form-control" id="web_hook_url"
                                    value="<?= base_url('connect_inter/v3/callback/index') ?>" aria-describedby="web_hook_url"
                                    placeholder="Digite o webhook">
                            <?php
                            }
                            ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        </form>
                        <Br />
                        <a href="<?= admin_url('settings') ?>?group=payment_gateways&tab=online_payments_connect_inter_tab">Abrir Configurações do módulo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
