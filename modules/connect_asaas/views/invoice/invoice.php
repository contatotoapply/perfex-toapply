<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="text-right">
                <a href="../" class="btn btn-primary mright5">Voltar</a>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table p-0 m-0">
                <caption class="text-center p-0 m-0 text-2xl bold">DADOS DA NOTA FISCAL</caption>
                <tbody>
                  <tr>
                    <td style="width:200px !important">SITUAÇÃO:</td>
                    <td class="bold"><?= asaas_formatar_status($invoice->status) ?>
                      <?php
                      if ($invoice->status == 'ERROR') {
                      ?>
                        <br>
                        <br>
                        <span class="badge badge-error bold">Descrição do(s) Erro(s):</span>
                        <ul>
                          <?php
                          $errors = explode(';', $invoice->statusDescription);
                          foreach ($errors as $erro) {
                            echo '<li><span class="badge" style="color:red">' .  $erro . '</span></li>';
                          }
                          ?>
                        </ul>
                      <?php
                      }
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <td>TIPO:</td>
                    <td class="bold"><?= $invoice->type ?></td>
                  </tr>
                  <tr>
                    <td>VALOR:</td>
                    <td class="bold"><?= app_format_money($invoice->value, get_base_currency()->name) ?></td>
                  </tr>
                  <tr>
                    <td>TIPO:</td>
                    <td class="bold"><?= $invoice->type ?></td>
                  </tr>
                  <tr>
                    <td>DESCRIÇÃO:</td>
                    <td class="bold"><?= $invoice->serviceDescription ?></td>
                  </tr>
                  <tr>
                    <td>DATA DA EMISSÃO:</td>
                    <td class="bold"><?= _d($invoice->effectiveDate) ?></td>
                  </tr>
                  <tr>
                    <td>municipalServiceName:</td>
                    <td class="bold"><?= $invoice->municipalServiceName ?></td>
                  </tr>
                  <tr>
                    <td>XML e PDF:</td>
                    <td class="bold">
                     <a href="<?=$invoice->pdfUrl?>">PDF</a> &nbsp;|&nbsp;
                     <a href="<?=$invoice->xmlUrl?>">XML</a>
                     </td>
                  </tr>
                  <tr>
                    <td>TAXAS</td>
                    <td>
                      <table style="border:1px solid #ccc !important;width:20%;border-collapse: collapse !important;" class="p-4">
                        <tbody>
                          <?php
                          $taxas = json_decode(json_encode($invoice->taxes), TRUE);
                          foreach ($taxas as $key => $value) {
                            echo '<tr><td style="border:1px solid #ccc;" class="bold p-4">' . $key . '</td>
                              <td style="border:1px solid #ccc;" class="text-center p-4">' . $value . '</td></tr>';
                          }
                          ?>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table>
              <table class="table p-0 m-0">
                <caption class="text-center p-0 m-0 text-2xl bold">DADOS DO CLIENTE</caption>
                <tbody>
                  <tr>
                    <td style="width:200px !important">ID:</td>
                    <td class="bold"><a href="<?= $client_url ?>"><?= $customer->id ?></a></td>
                  </tr>
                  <tr>
                    <td>NOME:</td>
                    <td class="bold"><?= $customer->name ?></td>
                  </tr>
                  <tr>
                    <td>EMAIL:</td>
                    <td class="bold"><?= $customer->email ?></td>
                  </tr>
                  <tr>
                    <td>CELULAR:</td>
                    <td class="bold"><?= $customer->phone ?></td>
                  </tr>
                  <tr>
                    <td>ENDEREÇO:</td>
                    <td class="bold"><?= $customer->address ?></td>
                  </tr>
                  <tr>
                    <td>NÚMERO:</td>
                    <td class="bold"><?= $customer->addressNumber ?></td>
                  </tr>
                  <tr>
                    <td>CPF/CNPJ:</td>
                    <td class="bold"><?= $customer->cpfCnpj ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
