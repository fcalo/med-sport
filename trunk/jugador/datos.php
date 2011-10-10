<?
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');
include("../back/config/database.php");
include("../admin/libs/session.php");

$sql="update t_plantilla ";
$sql.=" set nombre='".$_POST['n']."'";
$sql.=" ,puesto='".$_POST['p']."'";
$sql.=" ,numero='".$_POST['nu']."'";
$sql.=" ,fec_nacimiento='".$_POST['a']."-".$_POST['m']."-".$_POST['d']."'";
$sql.=" where id_plantilla=(select id_plantilla from t_solicitudes where email='".$_SESSION["uusSerRmed_player"]."')";
$db->show_errors=false;
$db->query($sql);
if($db->last_error=="")
	echo "OK";
else
	echo $db->last_error;
	
$db->show_errors=true;
?>