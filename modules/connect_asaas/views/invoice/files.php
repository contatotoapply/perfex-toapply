<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div role="tabpanel" class="tab-pane" id="customer_admins">
           
              <table class="table dt-table">
              
              <thead>
                  <tr>
                    <th> Nome</th>
              
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($reeducando_users as $reeducando_user){ ?>


   <?php } ?>
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
</body></html>