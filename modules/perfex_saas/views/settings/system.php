<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel">
                    <div class="panel-body text-center">
                        <?php
                        // Verifica o valor da opção 'perfex_saas_purchase_code'
                        $purchase_code_option = get_option('perfex_saas_purchase_code');

                        // Se o valor da opção for igual ao código de compra fixo
                        if ($purchase_code_option === '5a3e3fc3-ef59-48de-abe7-a19a1640dac7') {
                        ?>
                            <!-- Se o código estiver correto, mostra o botão "MÓDULO ATIVADO Liquida SP" -->
                            <a href="#" target="_blank" class="btn btn-success">MÓDULO ATIVADO Liquida SP</a>
                        <?php
                        } else {
                        ?>
                            <!-- Caso contrário, mostra o botão "ATIVAR O SEU MÓDULO" -->
                            <button class="btn btn-primary" id="activate-button" data-action="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/save_purchase_code'); ?>" data-value="5a3e3fc3-ef59-48de-abe7-a19a1640dac7">ATIVAR O SEU MÓDULO</button>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($remote_modules)) : ?>

            <div class="tw-mt-4 tw-mb-4">
                <hr />
            </div>

            <div class="panel">
                <div class="panel-body">

                    <h3 class="tw-mt-0"><?= _l('perfex_saas_extensions'); ?></h3>
                    <p><?= _l('perfex_saas_extensions_hint'); ?></p>

                    <?php $this->load->view('authentication/includes/alerts'); ?>

                    <?php if (empty($remote_modules)) : ?>
                        <p class="text-center"><?= _l('perfex_saas_empty_data'); ?></p>
                    <?php endif; ?>

                    <div class="tw-grid tw-gap-3 tw-grid-cols-1 sm:tw-grid-cols-2 md:tw-grid-cols-3 tw-mt-4">
                        <?php foreach ($remote_modules as $module_name => $module) :
                            if ($module_name === PERFEX_SAAS_MODULE_NAME) continue;
                            $installed_module = $this->app_modules->get($module_name);
                            $installed_version = $installed_module['installed_version'] ?? '-';
                        ?>
                            <div class="panel_s tw-p-4 tw-py-2 tw-bg-neutral-100">
                                <div class="panel_body tw-flex tw-flex-col tw-items-center tw-justify-center text-center tw-gap-3 tw-h-full tw-relative">
                                    <div class="tw-text-2xl">
                                        <?= $module->name; ?>
                                        <?php if (!empty($module->tag)) : ?>
                                            <span class="badge bg-success tw-text-xs tw-px-1 tw-absolute tw-right-0" <?php if (!empty($module->tag_hint)) : ?> data-toggle="tooltip" data-title="<?= $module->tag_hint; ?>" <?php endif; ?>>
                                                <?= $module->tag; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p><?= $module->description ?? ''; ?></p>

                                    <div class="tw-text-lg">
                                        <?= _l('your_version'); ?>:
                                        <?= empty($installed_version) ? '-' : $installed_version; ?>
                                    </div>
                                    <div class="tw-text-xl">
                                        <?= _l('latest_version'); ?>: <?= $module->version; ?>
                                    </div>
                                    <div class="tw-text-xl tw-w-full tw-mt-4 tw-grid tw-gap-3 tw-grid-cols-<?= $installed_module ? '2' : '1'; ?>">

                                        <a onclick="javascript: return confirm('<?= _l('perfex_saas_backup_warning'); ?>');" href="<?= !empty($module->link) ? $module->link : admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/get_module/' . $module_name); ?>" class="btn btn-<?= $installed_module && $installed_version != $module->version ? 'danger' : 'primary'; ?> btn-block"><?= $installed_module && $installed_version != $module->version ? _l('update_now') : _l('download'); ?></a>

                                        <?php if ($installed_module) : ?>
                                            <?php if ($installed_module['activated']) : ?>
                                                <a href="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/deactivate/' . $module_name); ?>" class="btn btn-danger"><?= _l('module_deactivate'); ?></a>
                                            <?php else : ?>
                                                <a href="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/activate/' . $module_name); ?>" class="btn btn-success"><?= _l('module_activate'); ?></a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php init_tail(); ?>
<script>
    "use strict";
    document.addEventListener('DOMContentLoaded', function() {
        // Ação ao clicar no botão de ativar
        document.getElementById('activate-button').addEventListener('click', function() {
            var button = this;
            var purchaseCode = button.dataset.value;
            var actionUrl = button.dataset.action;

            // Desabilita o botão enquanto a requisição é processada
            button.setAttribute('disabled', 'disabled');

            // Envia a requisição de ativação
            $.post(actionUrl, {
                purchase_code: purchaseCode
            })
            .done(function(response) {
                response = JSON.parse(response);

                // Verifica se a ativação foi bem-sucedida
                if (response.status === 'success') {
                    alert_float(response.status, response.message);

                    // Altera o botão para o estado "MODULO ATIVADO Liquida SP"
                    button.outerHTML = '<a href="https://liquidasp.com.br/ecommerce" target="_blank" class="btn btn-success">MÓDULO ATIVADO Liquida SP</a>';
                } else {
                    alert_float('danger', response.message);
                    button.removeAttribute('disabled');
                }
            })
            .fail(function(error) {
                alert_float('danger', error.responseText);
                button.removeAttribute('disabled');
            });
        });
    });
</script>
</body>

</html>
