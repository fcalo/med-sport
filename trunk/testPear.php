<?php
echo "1";
//set_include_path(get_include_path() . PATH_SEPARATOR . "/home/miequipodeportivo/pear/php/");
echo get_include_path ();
require_once "Mail.php";
require_once ('Mail/mime.php'); 
$from="miequipodeportivo@miequipodeportivo.com";
$host = "localhost";
$username = "miequipodeportivo@miequipodeportivo.com";
$password = "kulgfn";

$asunto="test";


$smtp = Mail::factory('smtp',
array ('host' => $host,
'auth' => true,
'port' => 25,
'username' => $username,
'password' => $password));

if (PEAR::isError($mail))
	die("&".$mail->getMessage());
$headers = array ('From' => $from,
			  	'MIME-Version' => '1.0',
			  	'Content-type' => 'text/html',
			  	'charset' => 'UTF-8',
			  	'Subject' => $asunto);
	

/*$headerss = 'From: miequipodeportivo@miequipodeportivo.com' . "\r\n" .
    'Reply-To: miequipodeportivo@miequipodeportivo.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();	
	
mail('fercalo@hotmail.com','test','test', $headerss);
mail('fernando.calo.sanchez@gmail.com','test','test', $headerss);*/
echo ' a enviar \n';
$mail = $smtp->send('fercalo@hotmail.com', $headers, "test");

echo "m";
?> 