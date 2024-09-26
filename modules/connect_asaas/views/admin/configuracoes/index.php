<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons tw-mb-2 sm:tw-mb-4">
                    <div class="clearfix"></div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open_multipart(admin_url('connect_asaas/configuracoes/update')); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class='form-group'>
                                    <label><?= _l('Email do cliente') ?></label>
                                    <select id="asaas_campo_personalisado_email_padrao" required
                                        name="settings[asaas_campo_personalisado_email_padrao]" data-actions-box="true"
                                        data-live-search="true" data-width="100%" class="selectpicker ajax-search">
                                        <option value="">Selecione</option>
                                        <?php
                                        foreach ($campos_personalizados as $cf) {
                                            $selected = '';
                                            if ($opcoes_selecionadas['asaas_campo_personalisado_email_padrao'] == $cf->slug) {
                                                $selected = 'selected';
                                            }
                                            echo '<option ' . $selected . ' data-subtext="' . $cf->slug . '" value="' . $cf->slug . '">' . $cf->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class='form-group'>
                                    <label><?= _l('Número do endereço') ?></label>
                                    <select id="asaas_campo_personalisado_numero_endereco_padrao" required
                                        name="settings[asaas_campo_personalisado_numero_endereco_padrao]"
                                        data-actions-box="true" data-live-search="true" data-width="100%"
                                        class="selectpicker ajax-search">
                                        <option value="">Selecione</option>
                                        <?php
                                        foreach ($campos_personalizados as $cf) {
                                            $selected = '';
                                            if ($opcoes_selecionadas['asaas_campo_personalisado_numero_endereco_padrao'] == $cf->slug) {
                                                $selected = 'selected';
                                            }
                                            echo '<option ' . $selected . ' data-subtext="' . $cf->slug . '" value="' . $cf->slug . '">' . $cf->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class='form-group'>
                                    <label><?= _l('Bairro do cliente') ?></label>
                                    <select id="asaas_campo_personalisado_bairro_padrao" required
                                        name="settings[asaas_campo_personalisado_bairro_padrao]" data-actions-box="true"
                                        data-live-search="true" data-width="100%" class="selectpicker ajax-search">
                                        <option value="">Selecione</option>
                                        <?php
                                        foreach ($campos_personalizados as $cf) {
                                            $selected = '';
                                            if ($opcoes_selecionadas['asaas_campo_personalisado_bairro_padrao'] == $cf->slug) {
                                                $selected = 'selected';
                                            }
                                            echo '<option ' . $selected . ' data-subtext="' . $cf->slug . '" value="' . $cf->slug . '">' . $cf->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="btn-bottom-toolbar text-right">
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

<script>
$(document).ready(function() {
    console.log('Is cool!');
});
</script>
</body>

</html>
