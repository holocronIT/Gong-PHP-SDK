# Gong PHP SDK

![Versione](https://img.shields.io/badge/versione-1.0-green.svg "versione")

Classe per l'integrazione semplificata tra qualsiasi applicativo PHP 5+ e GONG

## Per iniziare

Necessita solamente l'inclusione della classe e inzializzarla passando l'apikey generata dal sistema 

### Prerequisiti

Basta solo includere la classe

```php
require "gong.php";
```

### Installazione e primo utilizzo



```php
require "gong.php";

$Gong 	= new Gong( '[your-api-key]' );

if($Gong->isReady()){
	
	var_dump($Gong->getAvalableCrawler()); 

	// Posso quindi inviare i pacchetti al server

}else{

	die('Error connecting to gong, check host and port');

}

```


## Metodi

Elenco dei metodi esposti dalla classe

### isReady()

Metodo per verificare che la classe sia pronta a ricevere pacchetti da inviare. 

```php
if($Gong->isReady()){
	// Ok
}else{
	// Non OK
}
```

### getAvalableCrawler()

Metodo che ritorna la lista dei __Crawler__ accettati dall'apikey passata. 

```php
var_dump($Gong->getAvalableCrawler()); 
```

### pushData( $crawler , $logName , $packet )

Metodo per accodare un pacchetto alla coda dei dati da inviare a GONG. Il crawler deve essere uno di quelli associati all'apikey altrimenti il $packet verrà scartato. 
I dati _$packet_ saranno aggregati a seconda della chiave _$crawler-$logName_

```php
$packetToSend = array(
	'ip' => '192.168.1.1',
	'code' => 200,
	'path' => '/',
	'agent' => 'Mozilla 1.0'
);

$Gong->pushData( 'apache-2' , 'miodominio.it' , $packetToSend );
```

Ritorna _true_ se il pacchetto è stato correttamente inserito nei dataset da inviare. Altrimenti _false_


### send()

Metodo che invia i dati a GONG precedentemente raccolti tramite __pushData(...)__. Ritorna _false_ se fallisce altrimenti un report dei dati inviati

```php
$packetToSend = array(
	'ip' => '192.168.1.1',
	'code' => 200,
	'path' => '/',
	'agent' => 'Mozilla 1.0'
);

$Gong->pushData( 'apache-2' , 'miodominio.it' 	, $packetToSend );
$Gong->pushData( 'apache-2' , 'miodominio.it' 	, $packetToSend );
$Gong->pushData( 'apache-2' , 'miodominio.it' 	, $packetToSend );
$Gong->pushData( 'apache-2' , 'miodominio.com' 	, $packetToSend );

$r = $Gong->send(); // Invio i dati

// $r contiene:

/*

array(2) {
  [0]=>
  array(3) {
    ["craler"]=> string(8) "apache-2"
    ["logName"]=> string(8) "miodominio-it"
    ["rows"]=> int(3) 
  }
  [1]=>
  array(3) {
    ["craler"]=> string(8) "apache-2"
    ["logName"]=> string(7) "miodominio-com"
    ["rows"]=> int(1) 
  }
}

 */

```




## Dipendenze

Solo CURL

## Contribuzione

Si prega di leggere [CONTRIBUTING.md](https://github.com/holocronIT/CONTRIBUTING.md) per i dettagli sul nostro codice di condotta, e il processo per la presentazione di richieste di pull a noi.

## Versionamento

Noi usiamo [SemVer](http://semver.org/) per il versionamento.

## Autori

* **Pasqui Andrea** - *Initial work* 


## Licenze

Questo progetto è concesso in licenza con la licenza MIT - guarda la [LICENSE.md](LICENSE.md) per maggiorni dettagli


