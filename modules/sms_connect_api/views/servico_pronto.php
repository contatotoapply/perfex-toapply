
<?php
$lembretes = $this->db->select('t1.*')
    ->from(db_prefix().'central_notificacoes_lembretes t1')
    ->where('reminder_type','servico_pronto')
    ->where('t1.rel_id', $client->userid)->get()->result();
?>
<a href="<?= admin_url('sms_connect_api/lembretes/enviarServicoPronto')."?clientid=".$client->userid; ?>" class="btn btn-primary">Enviar Notificação</a>
<h4>Log de Serviço Pronto</h4>
  <table class="table dt-table" data-order-col="0" data-order-type="desc">
      <thead>
          <tr>
              <th id="url">TIPO</th>
              <th id="url">ENVIADO EM</th>
          </tr>
      </thead>
      <tbody>
        <?php
        foreach($lembretes as $lembrete):
        ?>
        <tr>
            <td><?= $lembrete->reminder_type; ?></td>
            <td><?= $lembrete->created_at; ?></td>
        </tr>
        <?php
        endforeach;
        ?>
      </tbody>
  </table>
