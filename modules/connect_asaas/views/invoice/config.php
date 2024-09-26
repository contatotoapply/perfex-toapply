<?php

$retainIss = get_option('asaas_invoice_retainIss');
$iss       = get_option('asaas_invoice_iss');
$cofins    = get_option('asaas_invoice_cofins');
$csll      = get_option('asaas_invoice_csll');
$inss      = get_option('asaas_invoice_inss');
$ir        = get_option('asaas_invoice_ir');
$pis       = get_option('asaas_invoice_pis');

$asaas_invoice_on_event = get_option('asaas_invoice_on_event');

$CI = &get_instance();

$municipal_service_default = json_decode(get_option('municipal_service_default')) ?? '';
?>

<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active"> <a href="#taxes" aria-controls="taxes" role="tab" data-toggle="tab">Taxas</a> </li>
  <li role="options"> <a href="#options" aria-controls="options" role="tab" data-toggle="tab">Opçoes</a> </li>
</ul>
<div class="tab-content mtop30">
  <div role="tabpanel" class="tab-pane active" id="taxes">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="varchar" class="control-label clearfix"> Retem Iss </label>
          <div class="radio radio-primary radio-inline">
            <input type="radio" id="y_opt_1_asaas_invoice_retainIss" name="settings[asaas_invoice_retainIss]" value="1">
            <label for="y_opt_1_varchar"> Sim </label>
          </div>
          <div class="radio radio-primary radio-inline">
            <input type="radio" id="y_opt_2_asaas_invoice_retainIss" name="settings[asaas_invoice_retainIss]" value="0" checked="">
            <label for="y_opt_2_varchar"> Não </label>
          </div>
        </div>
        <div class="form-group">
          <label for="varchar" class="control-label clearfix">Alíquota ISS</label>
          <input type="text" class="form-control" name="settings[asaas_invoice_iss]" id="iss" placeholder="" value="<?php echo $iss; ?>" />
        </div>
        <div class="form-group">
          <label for="varchar">Alíquota COFINS</label>
          <input type="text" class="form-control" name="settings[asaas_invoice_cofins]" id="cofins" placeholder="" value="<?php echo $cofins; ?>" />
        </div>
        <div class="form-group">
          <label for="varchar">Alíquota CSLL</label>
          <input type="text" class="form-control" name="settings[asaas_invoice_csll]" id="csll" placeholder="" value="<?php echo $csll; ?>" />
        </div>
        <div class="form-group">
          <label for="varchar">Alíquota INSS</label>
          <input type="text" class="form-control" name="settings[asaas_invoice_inss]" id="inss" placeholder="" value="<?php echo $inss; ?>" />
        </div>
        <div class="form-group">
          <label for="varchar">Alíquota IR</label>
          <input type="text" class="form-control" name="settings[asaas_invoice_ir]" id="ir" placeholder="" value="<?php echo $ir; ?>" />
        </div>
        <div class="form-group">
          <label for="varchar">Alíquota PIS</label>
          <input type="text" class="form-control" name="settings[asaas_invoice_pis]" id="csll" placeholder="" value="<?php echo $pis; ?>" />
        </div>
        <div class="form-group">
          <div class="form-group">
            <label for="varchar">Código de serviço municipal</label>
            <select id="municipalServiceCode" required data-live-search="true" data-width="100%" class="selectpicker ajax-search">
              <?php
              if ($municipal_service_default) {
              ?>
                <option value="<?= $municipal_service_default->service_id ?>">
                  <?= $municipal_service_default->service_name ?></option>
              <?php
              }
              ?>
            </select>
            <input type="hidden" class="form-control" name="settings[municipal_service_default]" value='<?= json_encode($municipal_service_default) ?>' />
          </div>
          <button type="button" id="addOptions" class="btn btn-success">Add CNAE</button>
          <div class="row hide" id="painel-add-cnae">
            <div class="col-md-12">
              <div class="alert alert-info mtop15" role="alert">
                Ao adicionar um novo serviço, ele será criado somente no banco de dados do sistema, e não no Asaas.
              </div>
            </div>
            <hr />
            <div class="col-md-2">
              <div class="form-group">
                <label for="cnae_code">Código</label>
                <input type="number" class="form-control" id="cnae_code" placeholder="" />
              </div>
            </div>
            <div class="col-md-7">
              <div class="form-group">
                <label for="cnae_description">Descrição</label>
                <input type="text" class="form-control" id="cnae_description" placeholder="" />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="cnae_service_id">Id do Serviço</label>
                <input type="number" class="form-control" id="cnae_service_id" placeholder="" />
              </div>
            </div>
            <div class="col-md-12">
              <button type="button" id="saveCnae" class="btn btn-success">Salvar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div role="tabpanel" class="tab-pane" id="options">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="varchar" class="control-label clearfix"> Configuração da emissão</label>
          <div class="radio radio-primary radio-inline">
            <input type="radio" id="y_opt_1_asaas_invoice_itemfilter" name="settings[asaas_invoice_on_event]" value="1" <?php if ($asaas_invoice_on_event == "1") {
                                                                                                                          echo "checked";
                                                                                                                        } ?>>
            <label for="y_opt_1_varchar"> Emitir na criação da fatura </label>
          </div>
          <div class="radio radio-primary radio-inline">
            <input type="radio" id="y_opt_2_asaas_invoice_itemfilter" name="settings[asaas_invoice_on_event]" value="2" <?php if ($asaas_invoice_on_event == "2") {
                                                                                                                          echo "checked";
                                                                                                                        } ?>>
            <label for="y_opt_2_varchar"> Emitir na confirmação de pagamento </label>
          </div>
          <div class="radio radio-primary radio-inline">
            <input type="radio" id="y_opt_2_asaas_invoice_itemfilter" name="settings[asaas_invoice_on_event]" value="0" <?php if ($asaas_invoice_on_event == "0") {
                                                                                                                          echo "checked";
                                                                                                                        } ?>>
            <label for="y_opt_2_varchar"> Emissão avulsa </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
hooks()->add_action('app_admin_footer', 'add_script_after_footer');
function add_script_after_footer()
{
?>

  <script>
    init_ajax_search(
      undefined,
      "#municipalServiceCode",
      undefined,
      admin_url + "connect_asaas/servicos"
    );

    $('#municipalServiceCode').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
      const $this = this.options[this.selectedIndex];
      var service_id = $this.value;
      if (service_id) {
        console.log(service_id);
        var service_name = $this.text;
        $('input[name="settings[municipal_service_default]"]').val(JSON.stringify({
          service_id,
          service_name
        }));
      }
    });

    $('#addOptions').click(function() {
      $('#painel-add-cnae').removeClass('hide');
      $('#cnae_code').focus();
    });

    $('#saveCnae').click(function() {
      var cnae_code = $('#cnae_code').val();
      var cnae_description = $('#cnae_description').val();
      var cnae_service_id = $('#cnae_service_id').val();
      $.ajax({
        url: admin_url + "connect_asaas/servicos/store",
        method: 'POST',
        data: {
          cnae_code,
          cnae_service_id,
          cnae_description
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            alert('Serviço cadastrado com sucesso.');
            window.location.reload();
          } else {
            alert(response.message);
          }
        },
        error: function(xhr, status, error) {
          console.log('Erro na requisição:', error);
        }
      });

    })
  </script>
<?php
}
