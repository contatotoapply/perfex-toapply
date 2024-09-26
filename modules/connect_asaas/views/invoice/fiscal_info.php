<?php init_head(); ?>

<div id="wrapper">
<div class="content">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel_s">
        <div class="panel-body">
          <?php  // var_dump($response);  ?>
          <div class="row">
            <div class="col-md-6">
              <table class="table">
                <tr>
                  <td>simplesNacional</td>
                  <td><?php echo $response["simplesNacional"];  ?></td>
                </tr>
                <tr>
                  <td>rpsSerie</td>
                  <td><?php echo $response["rpsSerie"];  ?></td>
                </tr>
                <tr>
                  <td>rpsNumber</td>
                  <td><?php echo $response["rpsNumber"];  ?></td>
                </tr>
                <tr>
                  <td>loteNumber</td>
                  <td><?php echo $response["loteNumber"];  ?></td>
                </tr>
                <tr>
                  <td>username</td>
                  <td><?php echo (int)$response["username"];  ?></td>
                </tr>
                <tr>
                  <td>passwordSent</td>
                  <td><?php echo (int)$response["passwordSent"];  ?></td>
                </tr>
                <tr>
                  <td>accessTokenSent</td>
                  <td><?php echo (int)$response["accessTokenSent"];  ?></td>
                </tr>
                <tr>
                  <td>certificateSent</td>
                  <td><?php echo $response["certificateSent"];  ?></td>
                </tr>
                <tr>
                  <td>specialTaxRegime</td>
                  <td><?php echo $response["specialTaxRegime"];  ?></td>
                </tr>
                <tr>
                  <td>email</td>
                  <td><?php echo $response["email"];  ?></td>
                </tr>
                <tr>
                  <td>serviceListItem</td>
                  <td><?php echo $response["serviceListItem"];  ?></td>
                </tr>
                <tr>
                  <td>cnae</td>
                  <td><?php echo $response["cnae"];  ?></td>
                </tr>
                <tr>
                  <td>culturalProjectsPromoter</td>
                  <td><?php echo (int)$response["culturalProjectsPromoter"];  ?></td>
                </tr>
                <tr>
                  <td>municipalInscription</td>
                  <td><?php echo $response["municipalInscription"];  ?></td>
                </tr>
              </table>
            </div>
            <div class="col-md-6">
              <?php // var_dump($my_account);  ?>
              <table class="table">
                <tr>
                  <td>personType</td>
                  <td><?php echo $my_account["personType"];  ?></td>
                </tr>
                <tr>
                  <td>companyType</td>
                  <td><?php echo $my_account["companyType"];  ?></td>
                </tr>
                <tr>
                  <td>company</td>
                  <td><?php echo $my_account["company"];  ?></td>
                </tr>
                <tr>
                  <td>cpfCnpj</td>
                  <td><?php echo $my_account["cpfCnpj"];  ?></td>
                </tr>
                <tr>
                  <td>email</td>
                  <td><?php echo $my_account["email"];  ?></td>
                </tr>
                <tr>
                  <td>responsibleName</td>
                  <td><?php echo $my_account["responsibleName"];  ?></td>
                </tr>
                <tr>
                  <td>phone</td>
                  <td><?php echo $my_account["phone"];  ?></td>
                </tr>
                <tr>
                  <td>mobilePhone</td>
                  <td><?php echo $my_account["mobilePhone"];  ?></td>
                </tr>
                <tr>
                  <td>postalCode</td>
                  <td><?php echo $my_account["postalCode"];  ?></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
