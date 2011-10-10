<?

//require_once "./PEAR/PEAR/Mail.php";
$server="http://".$_SERVER['HTTP_HOST'];
if(strpos($server,".dev")>0){
    $RUTA = "/home/fer/NetBeansProjects/med/PEAR/PEAR";
    ini_set("include_path",ini_get("include_path").":".$RUTA);
}

	require_once "Mail.php";
	require_once ('Mail/mime.php'); 


function sendMail($para,$asunto,$cuerpo){

        $from="Mi Equipo Deportivo <miequipodeportivo@miequipodeportivo.com>";
        $cabeceras = 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $cabeceras.='From: '.$from;
        return mail($para, $asunto,$cuerpo, $cabeceras);
	
	/*$server="http://".$_SERVER['HTTP_HOST'];
	if(strpos($server,".dev")>0){
		$host = "smtp.miequipodeportivo.com";
	}
	else
		$host = "localhost";
	$from="Mi Equipo Deportivo <miequipodeportivo@miequipodeportivo.com>";
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

	$mail = $smtp->send($para, $headers, $cuerpo);
	if (PEAR::isError($mail)){
		die($mail->getMessage());
	}else{
		return true;
	}*/
}


if(isset($_GET['test'])){
	if (sendmail("fernando.calo.sanchez@gmail.com","test","test"))
		echo "Enviado";
        else
            echo "no";
}
?>