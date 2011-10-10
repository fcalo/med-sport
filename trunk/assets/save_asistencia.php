<?
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');
include("../admin/config/database.php");
include("../admin/libs/session.php");

$where=" where id_partido='".$_GET['p']."'";
$where.=" and id_plantilla='".$_GET['j']."'";

$sql="select count(*) c from t_partidos_asistencia ".$where;
$row=$db->get_row($sql,ARRAY_A);
if($row['c']==0){
	$sql="insert into t_partidos_asistencia (id_partido, id_plantilla, ind_asistencia)";
	$sql.=" values ('".$_GET['p']."','".$_GET['j']."','".$_GET['a']."')";
}else{
	$sql="update t_partidos_asistencia ";
	$sql.=" set ind_asistencia='".$_GET['a']."'";
	$sql.=$where;
}

$db->show_errors=false;
$db->query($sql);
if($db->last_error=="")
	echo "OK";
else
	echo $db->last_error;
	
$db->show_errors=true;
?>