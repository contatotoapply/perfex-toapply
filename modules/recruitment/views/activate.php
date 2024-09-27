<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
			   <h4>Module Activation</h4>
			   <hr class="hr-panel-heading">
			   Please activate your module using your purchase key.
			   <br><br>
			   <?php echo form_open('', ['autocomplete'=>'off', 'id'=>'verify-form']); ?>
                        <?php echo form_hidden('original_url', ''); ?> 
                  		<?php echo form_hidden('module_name', ''); ?> 
								<?php echo render_input('purchase_key', 'purchase_key', '', 'text', ['required'=>true]); ?>
                        <?php echo render_input('username', 'Username', '', 'text', ['required'=>true]); ?>
                  		<button id="submit" type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                  	<?php echo form_close(); ?>
               </div>
            </div>
         </div>
         <div class="col-md-6">
		 </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
   appValidateForm($('#verify-form'), {
        purchase_key: 'required',
        username: 'required'
    }, manage_verify_form);

   function manage_verify_form(form) {
      // Função de verificação simplificada sem lógica de ativação
   }
</script>
