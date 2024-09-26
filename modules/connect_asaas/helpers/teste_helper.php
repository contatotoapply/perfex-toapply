<?php

function checkFileChanges($filePath)
{
    $currentHash = hash_file('sha256', $filePath);
    echo $currentHash;
}

$filePath = '/home/acer-note/dev/perfex_pmc/modules/connect_asaas/helpers/teste_helper.php';
checkFileChanges($filePath);
echo "\n";
