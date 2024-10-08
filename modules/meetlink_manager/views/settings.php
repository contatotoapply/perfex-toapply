<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-6 col-md-offset-3">
            <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
               <?php echo _l('sketchboard_setting') ?>
            </h4>
            <?php echo form_open($this->uri->uri_string()); ?>
            <div class="panel_s">
               <div class="panel-body">
                    
   
   <?php render_yes_no_option('meetlink_manager_menu_disabled', 'meetlink_manager_menu_disabled', 'meetlink_manager_menu_disabled_tooltip'); ?>
<hr>

    <?php render_yes_no_option('meetlink_manager_menu_send_email', 'meetlink_manager_menu_send_email', 'meetlink_manager_menu_send_email_tooltip'); ?>
    <hr>
    <?php render_yes_no_option('meetlink_manager_menu_update_mail', 'meetlink_manager_menu_update_mail', 'meetlink_manager_menu_update_mail_tooltip'); ?>
    <hr>
    
    <!-- Campo de código de compra removido -->
    
    <!-- Este campo foi removido: render_input('settings[meetlink_manager_purchase_code]', 'purchase_code', get_option('meetlink_manager_purchase_code'), 'text',['required'=>'required']); -->
    
               </div>
               <div class="panel-footer text-right">
                  <button class="btn btn-primary"
                     type="submit"><?php echo _l('Save'); ?></button>
               </div>
               
            </div>
            
            <?php echo form_close(); ?>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
</html>
