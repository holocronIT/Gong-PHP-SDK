<?php 

/**
 * Integration class to send data to gong log system
 *
 * This class provide e easy-to-use set of function for sending data to gong's server
 *
 * @category   Gong SDK
 * @package    gong-php-sdk
 * @author     Pasqui Andrea <a.pasqui@holocron.it>
 * @copyright  2017-2018 Holocron
 * @version    1.0
 * @link       http://holocron.it/gong/sdk/
 */

class Gong
{


	/**
	 * Contain the host url without port
	 * @var string
	 */
	private $host 				= 'http://gong.holocron.it';

	/**
	 * Contain the port of the server
	 * @var integer
	 */
	private $port 				= 1977;   // Il numero della porta risale all'anno di uscita del primo film di Star Wars 

	/**
	 * Contain the apikey for grant access to the server
	 * @var boolean
	 */
	private $apikey				= '';

	/**
	 * Contain a list of avalable crawler. If false the apikey is invalid or no crawler are connected to it
	 * @var boolean/array
	 */
	private $avalableCrawler 	= FALSE;

	/**
	 * Contain data read to send over http to the server
	 * @var boolean/object
	 */
	private $dataReadyToSend	= array();

	/**
	 * check variable
	 * @var boolean
	 */
	private $ready 				= FALSE;


	/**
	 * @param Apikey - the apikey of your user. Required
	 * @param 
	 * @param boolean
	 */
	function __construct( $apikey , $host = FALSE , $port = FALSE ) {
       
		if(empty($apikey) || !is_string($apikey) ){
			return FALSE;
		}

		if ( $host !== FALSE && !empty($host) && filter_var($host, FILTER_VALIDATE_URL)  ) {
		    $this->host 	= trim($host);
		}

		if ( $port !== FALSE &&  !empty($port) && is_int($port)  ) {
		    $this->port 	= intval($port);
		}


		$this->apikey 	= trim($apikey);
		$this->ready 	= TRUE; 

		$this->getAvalableCrawler();

   	}


   	/*----------  START PUBLIC FUNCTIONS  ----------*/


   	/**
   	 * @return Return TRUE if the class is ready to send data to server
   	 */
   	public function isReady(){

   		return $this->ready && $this->avalableCrawler ? TRUE : FALSE;

   	}



   	public function getAvalableCrawler(){

   		if(!$this->ready) return FALSE;

   		$endpointUrl = $this->getApikeyCheckendpoint();

   		$r = $this->doRequest( $endpointUrl );

   		if( $r = json_decode($r, TRUE) ){
   			$this->avalableCrawler = $r;
   			return $r;
   		}else return FALSE;


   	}


   	/**
   	 * @param  $crawler : The server side crawler name
   	 * @param  $logName : your 'friendly' log name
   	 * @param  $packet : an associative array containing data
   	 * @return booloean
   	 */
   	public function pushData( $crawler , $logName , $packet ){

   		if( empty($crawler) || empty($logName) || empty($packet) ) return FALSE;

   		if( !is_string($crawler) || !is_string($logName) ) return FALSE;

   		if(!$this->isAssoc($packet)) return FALSE;

   		$crawlerOk = FALSE;

   		foreach ($this->avalableCrawler as $k => $c) {
   			if($c['name'] == $crawler) $crawlerOk = TRUE;
   		}

   		if( !$crawlerOk ) return FALSE;

   		if(empty($packet['date'])) $packet['date'] = date(DATE_ISO8601);

   		$dataPushed = FALSE;

   		foreach ($this->dataReadyToSend as $k => $wrapper) {
   			if( $wrapper['cn'] == $crawler && $wrapper['ln'] == $logName ){
   				if(!isset($wrapper['rows'])){
   					$wrapper['rows'] = array();
   				}

   				$this->dataReadyToSend[$k]['rows'][] = $packet;
   				$dataPushed = TRUE;

   			}
   		}

   		if( !$dataPushed ){

   			$this->dataReadyToSend[] = array(
   										'cn' => $crawler,
   										'ln' => $logName,
   										'rows' => array( $packet )
   									);

   		}


   	}


   	/**
   	 * @return bool on fail, array on success
   	 */
   	public function send(){

   		if(empty($this->dataReadyToSend) || count($this->dataReadyToSend) == 0 ) return FALSE;

   		$endpointUrl = $this->getLogCheckendpoint();

   		$r = $this->doRequest( $endpointUrl , TRUE , $this->dataReadyToSend , TRUE );
   			
   		if( $r = json_decode($r, TRUE) ){

   			if( $r['status'] === TRUE ){
   				return $r['data'];
   			}else return FALSE;

   		}	

   		return $r;
   	}




   	/*----------  START PRIVATE FUNCTIONS  ----------*/
   	
   	

   	/**
   	 * Function for building check api endopoint url
   	 */
   	private function getApikeyCheckendpoint(){

   		return $this->host.'/check/'.$this->apikey.'/';

   	}


   	/**
   	 * Function for building log api endopoint url
   	 */
   	private function getLogCheckendpoint(){

   		return $this->host.'/log/'.$this->apikey.'/';

   	}



   	/**
   	 * @param  $url : URL of the request to do
   	 * @param  $isPost : true for POST HTTP request
   	 * @param  $payload : data to send inside POST HTTP request
   	 * @param  $jsonPayload : true if $payload must be converted in JSON
   	 * @return Request Response
   	 */
   	private function doRequest( $url , $isPost = FALSE , $payload = FALSE , $jsonPayload = FALSE ){

   		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_PORT, $this->port );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		if( !empty($payload) && $jsonPayload === TRUE ){
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			       'Content-Type: application/json'
			   ));
		}
		
		if( $isPost === TRUE ){
			curl_setopt($ch, CURLOPT_POST, 1);
		}
		
		if( !empty($payload) && $isPost === TRUE ){

			if( $jsonPayload === TRUE ) $payload = json_encode($payload);

			curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
		}


		
		$r = curl_exec( $ch );
		curl_close($ch);

		return $r;

   	}


   	/**
   	 * @param  Array to test to check that is associative
   	 * @return boolean
   	 */
   	private function isAssoc(array $arr)
	{
	    if (array() === $arr) return false;
	    return array_keys($arr) !== range(0, count($arr) - 1);
	}




}





?>