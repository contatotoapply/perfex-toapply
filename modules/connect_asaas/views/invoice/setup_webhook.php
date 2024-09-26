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
          <div class="panel-body">
            <?php //var_dump($webhook) ?>
            <form class="" action="" method="post">
              <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>"/>
              <div class="form-group">
                <label>Endereço do webhook</label>
                <input type="disabled" class="form-control" value="<?php echo $webhook["url"] ?>" />
              </div>
              <div class="form-group">
                <label>Endereço de email</label>
                <input type="text" class="form-control " name="email" id="email" placeholder="" value="<?php echo $webhook["email"] ?>" required>
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit" >Salvar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
