<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="mb-10 relative clear">
              <a href="<?= admin_url('connect_asaas/asaas_invoice/create') ?>" class="btn btn-primary mright5">
                <i class="fa-regular fa-plus tw-mr-1"></i>Criar Nota Avulsa</a>
            </div>
            <br />

            <div class="table-responsive">

              <table class="table dt-table" data-info="false" data-paging="false" data-order-col="0" data-order-type="desc">
                <thead>
                  <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Descrição da cobrança</th>
                    <th>Emissão</th>
                    <th>Situação</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  foreach ($response["data"] as $asaas_invoice) { ?>
                    <tr>
                      <td>
                        <a href="<?= admin_url("connect_asaas/asaas_invoice/invoices/{$asaas_invoice["id"]}") ?>"><?php echo $asaas_invoice["id"];  ?></a>
                      </td>
                      <td><?php echo $asaas_invoice["client"]->name  ?>
                        <div class="row-options">
                          <a href="<?= $asaas_invoice["pdfUrl"] ?>">PDF</a> |
                          <a href="<?= $asaas_invoice["xmlUrl"] ?>">XML</a>
                        </div>
                      </td>
                      <td><?php echo app_format_money($asaas_invoice["value"], get_base_currency()->name);  ?></td>
                      <td><?php echo $asaas_invoice["serviceDescription"];  ?></td>
                      <td><?php echo _d($asaas_invoice["effectiveDate"]);  ?></td>
                      <td><?php echo asaas_formatar_status($asaas_invoice["status"]);  ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>

              <?php
              $totalCount   = $response["totalCount"];
              $itemsPerPage = 10;
              $totalPages   = ceil($totalCount / $itemsPerPage);
              $currentPage  = $response["offset"];
              ?>
              <div class="tw-flex tw-items-center tw-justify-between">
                <div style="color:#64748b">
                  Mostrar 1 a <?= $itemsPerPage ?> de <?= $totalCount   ?> entradas
                </div>
                <ul class="pagination">
                  <?php

                  if ($currentPage > 1) {
                    $previousOffset = $currentPage - 1;
                    echo '<li class="paginate_button"><a href="?offset=' . $previousOffset . '" aria-controls="DataTables_Table_0">Anterior</a></li>';
                  }

                  for ($i = 1; $i <= $totalPages; $i++) {
                    $isActive = ($currentPage == $i || ($currentPage == 0 && $i == 1)) ? 'active' : '';
                    $offset = ($i == 1) ? 0 : $i;
                    echo '<li class="paginate_button ' . $isActive . '"><a href="?offset=' . $offset . '">' . $i . '</a></li>';
                  }

                  if ($currentPage < $totalPages) {
                    $nextOffset = $currentPage + 1;
                    echo '<li class="paginate_button next"><a href="?offset=' . $nextOffset . '" aria-controls="DataTables_Table_0" data-dt-idx="2" tabindex="0">Próximo</a></li>';
                  }
                  ?>
                </ul>
              </div>


            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
