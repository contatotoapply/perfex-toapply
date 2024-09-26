<?php init_head(); ?>
<?php
// var_dump($response);	
?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
<pre>
          <?php
 var_dump($response);	
?>
</pre>
            <table class="table table-bordered">
              <tr>
                <th>#</th>
                <th>Descrição</th>
                <th>Taxa ISS</th>
              </tr>
              <?php  foreach ($response["data"] as $row) { ?>
              <tr>
                <td><?php   echo $row["id"]; ?></td>
                <td><?php   echo $row["description"]; ?></td>
                <td><?php   echo $row["issTax"]; ?></td>
              </tr>
              <?php   }
		 ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
