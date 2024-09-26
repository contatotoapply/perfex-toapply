<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <h4>Clientes no Asaas</h4>
        <div class="panel_s">
          <div class="panel-body">
            <table class="table dt-table">
              <thead>
                <tr>
                  <td>id</td>
                  <td>name</td>
                  <td>cpfCnpj</td>
                  <td>dateCreated</td>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($response as $row) {
                  $url_base = admin_url('connect_asaas/customers/');
                ?>
                  <tr>
                    <td><?php echo  $row["id"];    ?>
                      <div class="row-options">
                        <a href="<?= $url_base . 'show/' . $row['id'] ?>" class="editar"><?= _l('asaas_view') ?></a>
                        <?php
                        if (is_admin()) {
                        ?>
                          | <a href="<?= $url_base . 'delete/' . $row['id'] ?>" class="text-danger _delete"><?= _l('delete') ?></a>
                          | <a href="<?= $url_base . 'set_customer_id/' . $row['id']. '/'. $row["cpfCnpj"] ?>" class="text-info _delete"><?= _l('Definir id no Perfex') ?></a>
                        <?php
                        }
                        ?>
                      </div>
                    </td>
                    <td><?php echo  $row["name"];   ?></td>
                    <td><?php echo  $row["cpfCnpj"];   ?></td>
                    <td><?php echo  $row["dateCreated"];   ?></td>
                  </tr>
                <?php   }  ?>
              </tbody>
            </table>
          </div>
        </div>
        <h4>Clientes no Banco de Dados do Perfex</h4>
        <div class="panel_s">
          <div class="panel-body">
            <table class="table dt-table" data-order-col="0" data-order-type="desc">
              <thead>
                <tr>
                  <th><?= _l('ID') ?></th>
                  <th><?= _l('NOME') ?></th>
                  <th><?= _l('CNPJ/CPF') ?></th>
                  <th><?= _l('ASAAS_CUSTOMER_ID') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                // userid,company,vat,asaas_customer_id
                foreach ($clients as $client) {
                ?>
                  <tr>
                    <td><?= $client->userid ?></td>
                    <td><?= $client->company ?>
                      <?php
                      if (is_admin()) {
                      ?>
                        <div class="row-options">
                          <a href="<?= admin_url("clients/client/{$client->userid}") ?>" target="_blank" class="editar"><?= _l('asaas_view') ?></a>
                        </div>
                      <?php
                      }
                      ?>
                    </td>
                    <td><?= preg_replace('/\D/', '', $client->vat) ?></td>
                    <td><?= $client->asaas_customer_id ?></td>
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
