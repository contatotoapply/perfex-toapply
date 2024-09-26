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
                        <?php
                        if (isset($client->userid)) {
                        ?>
                            <div class="alert alert-success" role="alert">
                                O cliente existe no Perfex CRM
                                <a href="<?= admin_url("clients/client/{$client->userid}") ?>"><?= $client->company ?></a>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                O cliente não existe no Perfex CRM
                            </div>
                        <?php
                        }
                        $keys = array_keys($customer);
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?= _l('Chave') ?></th>
                                    <th><?= _l('Valor') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($keys as $key => $value) {
                                ?>
                                    <tr>
                                        <td><?= $value ?></td>
                                        <td><?= $customer[$value] ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
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
