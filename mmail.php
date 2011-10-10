<?

$server="http://".$_SERVER['HTTP_HOST'];
if(strpos($server,".dev")>0){
	require_once "Mail.php";
	require_once ('Mail/mime.php'); 
}else{
	require_once "./PEAR/PEAR/Mail.php";
}





function sendMail($para,$asunto,$cuerpo){
	
	/*$host = "mail.miequipodeportivo.com";
	$from="miequipodeportivo@miequipodeportivo.com";
	$username = "miequipodeportivo@miequipodeportivo.com";
	$password = "kulgfn";
	$port=25;
	

	$smtp = Mail::factory('smtp',
	array ('host' => $host,
	'auth' => true,
	'port' => $port,
	'username' => $username,
	'password' => $password));

	if (PEAR::isError($mail))
		die("error");
	$headers = array ('From' => $from,
					'MIME-Version' => '1.0',
					'Content-type' => 'text/html',
					'charset' => 'UTF8',
					'Subject' => $asunto);
		
	*/
	$headerss='MIME-Version: 1.0' . "\r\n";
	$headerss.='Content-type: text/html; charset=UTF8' . "\r\n";
	$headerss.= 'From: Mi Equipo Deportivo <miequipodeportivo@miequipodeportivo.com>' . "\r\n";

    //'X-Mailer: PHP/' . phpversion();	
	
	return mail($para,$asunto,$cuerpo, $headerss);
	/*$mail = $smtp->send($para, $headers, $cuerpo);
	die("???");
	if (PEAR::isError($mail)){
		die($mail->getMessage());
	}else{
		return true;
	}*/
}

if(isset($_GET['test'])){
	if (sendmail("fercalo@hotmail.com","test","test"))
		echo "Enviado";
}
?>