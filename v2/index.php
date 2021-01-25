<?php

require_once 'vendor/autoload.php';
require_once 'rastro.php';

try {
    $rastreador = new rastro;
    
    $data = $rastreador->rastrearObjeto($_REQUEST["rastreamento"]);
    $rastreador->enviaEmail($data["cabecalho"], $data["corpo"], $data["pdf"]);
} catch (\Throwable $th) {
    print "Erro: $th";
}
