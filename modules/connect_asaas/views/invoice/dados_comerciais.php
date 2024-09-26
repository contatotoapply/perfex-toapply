<?php init_head(); ?>
<?php
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
	$uuid = $this->uri->segment(4);
?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php   var_dump($response);  ?>
            <table class="table table-bordered">
              <tr>
                <th>Descrição</th>
                <td><?php   echo $response["status"]; ?></td>
              </tr>
              <tr>
                <th>Taxa ISS</th>
                <td><?php   echo $response["denialReason"]; ?></td>
              </tr>
              <tr>
                <th>Descrição</th>
                <td><?php   echo $response["personType"]; ?></td>
              </tr>
              <tr>
                <th>Descrição</th>
                <td><?php   echo $response["companyType"]; ?></td>
              </tr>
              <tr>
                <th>Descrição</th>
                <td><?php   echo $response["company"]; ?></td>
              </tr>
              <tr>
                <th>Descrição</th>
                <td><?php   echo $response["cpfCnpj"]; ?></td>
              </tr>
              <tr>
                <td><?php   echo $response["email"]; ?></td>
              </tr>
              <tr>
                <th>Descrição</th>
                <td><?php   echo $response["responsibleName"]; ?></td>
              </tr>
              <tr>
                <th>Descrição</th>
                <td><?php   echo $response["phone"]; ?></td>
              </tr>
              <tr>
                <th>Descrição</th>
                <td><?php   echo $response["mobilePhone"]; ?></td>
              </tr>
              <tr>
                <td><?php   echo $response["postalCode"]; ?></td>
              </tr>
              <tr>
                <th>Descrição</th>
                <td><?php   echo $response["address"]; ?></td>
              </tr>
              <tr>
                <td><?php   echo $response["addressNumber"]; ?></td>
              </tr>
            </table>
            <!--
    ["addressNumber"]=> string(2) "30" ["complement"]=> NULL ["province"]=> string(12) "Vista Alegre" ["city"]=> array(7) { ["object"]=> string(4) "city" ["id"]=> int(10072) ["ibgeCode"]=> string(7) "3106200" ["name"]=> string(14) "Belo Horizonte" ["districtCode"]=> string(2) "05" ["district"]=> string(14) "Belo Horizonte" ["state"]=> string(2) "MG" } ["inscricaoEstadual"]=> NULL ["name"]=> string(19) "Bruno Sampaio Murer" ["birthDate"]=> string(10) "1983-12-27" ["status"]=> string(8) "APPROVED" ["denialReason"]=> NULL --> 
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
