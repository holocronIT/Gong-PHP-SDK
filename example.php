<?php  

/**
 * Example for using gong PHP SDK
 *
 *
 * @category   Gong SDK Example
 * @package    gong-php-sdk
 * @author     Pasqui Andrea <a.pasqui@holocron.it>
 * @copyright  2017-2018 Holocron
 * @link       http://holocron.it/gong/sdk/
 */


// Include the class
require "gong.php";

echo 'Starting gong SDK...';

$Gong 	= new Gong( 'gdlZD3RjcsqJk2B05qbstUtIrpP1IIJ0dl7NgPIzCcdSF0ZztB' ); // Use default host and port
//$Gong 	= new Gong( 'myapikey' , 'myCustomHost' , mycustomPort ); 


if($Gong->isReady()){
	echo '<pre>';
	var_dump($Gong->getAvalableCrawler());
	echo '</pre>';
}else{
	die('Error connecting to gong, check host and port');
}

$packetToSend = array(
	'ip' => '192.168.1.1',
	'code' => 200,
	'path' => '/',
	'agent' => 'Mozilla 1.0'
);

$Gong->pushData( 'apache-2' , 'test.com' , $packetToSend );
$Gong->pushData( 'apache-2' , 'test.com' , $packetToSend );
$Gong->pushData( 'apache-2' , 'test.com' , $packetToSend );
$Gong->pushData( 'apache-2' , 'test.it' , $packetToSend );


$r = $Gong->send();
echo '<pre>';
var_dump($r);
echo '</pre>';



?>