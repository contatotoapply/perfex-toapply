<h1>Imprimir a Carteirinha</h1>

<?php
if($client){
    $cpf = preg_replace('/\D+/', '', $client->vat);
    echo $cpf;
}
?>
<hr />
<a href="#" class="btn btn-primary">Imprimir</a>
