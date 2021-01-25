<?php
class Rastrear
{
    
    private static $wsdl = null ; 

    private static $user = null ; 

    private static $pass = null ; 

    private static $tipo = null ; 

    private static $resultado = null ; 

    private static $idioma = null ; 

    private static $inicializado = false ;

    public static function init( $_params = array() )
    {
        self::$wsdl        = isset($_params['wsdl'])      ? $_params['wsdl']      : "http://webservice.correios.com.br/service/rastro/Rastro.wsdl" ; 
        self::$user        = isset($_params['user'])      ? $_params['user']      : "ECT" ;
        self::$pass        = isset($_params['pass'])      ? $_params['pass']      : "SRO" ;
        self::$tipo        = isset($_params['tipo'])      ? $_params['tipo']      : "L" ;
        self::$resultado   = isset($_params['resultado']) ? $_params['resultado'] : "T" ;
        self::$idioma      = isset($_params['idioma'])    ? $_params['idioma']    : "101" ;
        self::$inicializado= true;
    }

    public static function get( $__codigo__ = null )
    {
        if(!self::$inicializado)
            return self::erro( "Primeiro acesse o metodo Rastrear::init() com os devidos parametros." );

        if( is_null( $__codigo__ ) )
            return self::erro( "Nenhum código de rastreamento recebido." );

        if( ! self::soapExists() )
            return self::erro( "Parece que o Modulo SOAP não esta ativo em seu servidor." );

        $_evento = array(
            'usuario'   => self::$user,
            'senha'     => self::$pass,
            'tipo'      => self::$tipo,
            'resultado' => self::$resultado,
            'lingua'    => self::$idioma,
            'objetos'   => trim($__codigo__)
        );

        $client = new SoapClient( self::$wsdl );
        $eventos = $client->buscaEventos( $_evento );
        return ($eventos->return->qtd == 1) ? 
        	$eventos->return->objeto:
        	$eventos->return;
    }

    private static function soapExists() {
		return extension_loaded('soap') && class_exists("SOAPClient") ;
    }


    private static function erro( $__mensagem = null ){
        $obj = new stdClass;
        $obj -> erro = $__mensagem ; 
        return $obj ;
    }

} 