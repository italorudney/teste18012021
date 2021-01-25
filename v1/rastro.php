<?php 

require_once( 'controller.php' ) ;

$_params = array( 'user' => 'ECT', 'pass' => 'SRO', 'tipo' => 'L', 'resultado' => 'T', 'idioma' => 101 );


Rastrear::init( $_params );


$obj = Rastrear::get( 'OA016913717BR' );


if(isset($obj->erro))
    die( $obj->erro );

echo "NUMERO: "    . $obj -> numero . "<br>" ;
echo "SIGLA: "     . $obj -> sigla . "<br>" ;
echo "NOME: "      . $obj -> nome . "<br>" ;
echo "CATEGORIA: " . $obj -> categoria . "<br>" ;



if( is_object($obj->evento) ){
    $tmp = Array();
    $tmp[] = $obj->evento ;
    $obj->evento = $tmp;
}

print_r($obj);die;