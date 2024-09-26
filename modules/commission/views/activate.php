<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
               <h4>Module License Activation</h4>
               <hr class="hr-panel-heading">
               <p>The module is successfully activated.</p>
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
   $(document).ready(function() {
       alert_float("success", "The module is successfully activated.");
   });
</script>
