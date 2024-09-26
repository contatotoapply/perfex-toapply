<?php init_head(); ?>
<?php
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4> Informações para emissão da Nota Fiscal </h4>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open(); ?>
                        <input type="hidden" name="municipal_service_code" />
                        <input type="hidden" name="municipal_service_description" />
                        <div class="row mbot5">
                            <div class="col-md-4">
                                <div class="radio radio-primary radio-inline">
                                    <input type="radio" name="effectiveNow" id="radios-0" value="1" checked="">
                                    <label for="radios-0"> Emitir agora </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="radio radio-primary radio-inline">
                                    <input type="radio" name="effectiveNow" id="radios-1" value="0">
                                    <label for="radios-1"> Agendar emissão </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="varchar">Data</label>
                            <input type="date" class="form-control" name="effectiveDate" id="effectiveDate" required value="<?php echo date('Y-m-d'); ?>" />
                        </div>
                        <div class="form-group">
                            <label for="varchar">Cliente</label>
                            <select id="clientid" name="clientid" required data-live-search="true" data-width="100%" class="ajax-search">
                                <?php echo $customer; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="varchar"> Serviço:</label>
                            <div style="display:flex">
                                <select id="municipalServiceCode" name="municipalServiceCode" required data-live-search="true" data-width="100%" class="selectpicker">
                                </select>
                                <button type="button" tile="Adicionar Serviço" class="btn btn-primary mright5 pull-left display-block" data-toggle="modal" data-target="#exampleModal">
                                    <i class="fa-regular fa-plus tw-mr-1"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="varchar"> Descrição do serviço:</label>
                            <input type="text" class="form-control" required name="serviceDescription" id="serviceDescription" value="<?php echo isset($serviceDescription) ? $serviceDescription : ''; ?>" />
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"> Cliente deve reter ISS? </label>
                                    <select class="form-control" id="retainIss" name="retainIss">
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"> Alíquota ISS </label>
                                    <div class="input-group"><span class="input-group-addon">%</span>
                                        <input id="iss" type="number" class="form-control" name="iss" placeholder="0,00" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="varchar"> Observações adicionais</label>
                            <textarea class="form-control" name="observations" id="observations"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="varchar"> Valor da nota fiscal</label>
                            <input type="number" class="form-control" name="amount" id="amount" required value="<?php echo isset($value) ? $value : ''; ?>" />
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">COFINS</label>
                                    <div class="input-group"><span class="input-group-addon">%</span>
                                        <input id="cofins" type="text" class="form-control" name="cofins" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"> Valor COFINS </label>
                                    <div class="input-group"><span class="input-group-addon">R$</span>
                                        <input id="cofins_tax" type="text" class="form-control" placeholder="0.00" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">CSLL</label>
                                    <div class="input-group"><span class="input-group-addon">%</span>
                                        <input id="csll" type="text" class="form-control" name="csll" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"> Valor CSLL </label>
                                    <div class="input-group"><span class="input-group-addon">R$</span>
                                        <input id="csll_tax" type="text" class="form-control" placeholder="0.00" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">INSS</label>
                                    <div class="input-group"><span class="input-group-addon">%</span>
                                        <input id="inss" type="text" class="form-control" name="inss" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"> Valor INSS </label>
                                    <div class="input-group"><span class="input-group-addon">R$</span>
                                        <input id="inss_tax" type="text" class="form-control" placeholder="0.00" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"> IR</label>
                                    <div class="input-group"><span class="input-group-addon">%</span>
                                        <input id="ir" type="text" class="form-control" name="ir" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"> Valor IR </label>
                                    <div class="input-group"><span class="input-group-addon">R$</span>
                                        <input id="ir_tax" type="text" class="form-control" placeholder="0.00" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"> PIS</label>
                                    <div class="input-group"><span class="input-group-addon">%</span>
                                        <input id="pis" type="text" class="form-control" name="pis" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"> Valor PIS </label>
                                    <div class="input-group"><span class="input-group-addon">R$</span>
                                        <input id="pis_tax" type="text" class="form-control" placeholder="0.00" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="varchar"> Outras deduções</label>
                            <input type="text" class="form-control" name="deductions" id="deductions" placeholder="" value="0.00" />
                        </div>
                        <div class="form-group">
                            <label for="varchar"> Valor líquido</label>
                            <input type="text" class="form-control" name="liquid_amount" id="liquid_amount" placeholder="" value="" />
                        </div>
                        <div class="form-group">
                            <label for="varchar"> (impostos descontados)</label>
                            <input type="text" class="form-control" id="total_tax" placeholder="" value="" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-tr btn btn-info">Avançar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Button trigger modal -->

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title">Adicionar Novo Serviço</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row" id="painel-add-cnae">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cnae_code">Código</label>
                            <input type="number" class="form-control" id="cnae_code" placeholder="" />
                        </div>
                    </div>
                    <div class="col-md-6">
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" id="saveCnae" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php
init_tail();
?>
<script>
    $(document).ready(function() {
        init_ajax_search(
            undefined,
            "#municipalServiceCode",
            undefined,
            admin_url + "connect_asaas/servicos"
        );

        $('#municipalServiceCode').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
            const $this = this.options[this.selectedIndex];
            var value = $this.value;
            var text = $this.text;
            $('input[name="municipal_service_code"]').val(value);
            $('input[name="municipal_service_description"]').val(text);
        });

        // Append the option to select

        $('#saveCnae').click(function() {
            var cnae_code = $('#cnae_code').val();
            var cnae_description = $('#cnae_description').val();
            var cnae_service_id = $('#cnae_service_id').val();

            const payload = {
                cnae_code,
                cnae_service_id,
                cnae_description
            }

            $.ajax({
                url: admin_url + "connect_asaas/servicos/store",
                method: 'POST',
                data: payload,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Serviço cadastrado com sucesso.');
                        $('#exampleModal').modal('hide');
                        setTimeout(() => {
                            // $('#municipalServiceCode').append(`<option value="${response.data.id}">${response.data.description}</option>`);
                            // $("#municipalServiceCode").val(response.data.id);
                            // $("#municipalServiceCode").selectpicker("refresh");
                            // $('.selectpicker').selectpicker('refresh');
                        },1000);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Erro na requisição:', error);
                }
            });

        })

    });
</script>
