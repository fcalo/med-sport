<?
include("./back/config/database.php");

$sql="select id_partido from t_partidos where sha1(id_partido)='".$_GET['a']."'";
$row=$db->get_row($sql,ARRAY_A);
$idPartido=$row['id_partido'];


$sql="select id_plantilla id from t_plantilla where sha1(concat(id_plantilla,'".$idPartido."'))='".$_GET['b']."'";
$row=$db->get_row($sql,ARRAY_A);
$idPlantilla=$row['id'];

if(isset($_GET['c']))
	$asistencia='S';
if(isset($_GET['d']))
	$asistencia='N';
	
$sql="insert into t_partidos_asistencia(id_partido, id_plantilla, ind_asistencia)";
$sql.=" values ($idPartido, $idPlantilla, '$asistencia')";
$sql.=" on duplicate key update ind_asistencia='$asistencia' ";
$row=$db->query($sql);



header('Location: /partido/'.$idPartido);
end;
?>