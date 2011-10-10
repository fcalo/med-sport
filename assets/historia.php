<?
include("../admin/config/database.php");
include("util.php");

if (isset($_GET['tt']))
	$historia="No hay historia registrada para este equipo";
else{
	$sql="select logo, presentacion, nom_equipo from t_equipos where id_equipo=".$_GET['k'];
	$row=$db->get_row($sql,ARRAY_A);
	if($row['presentacion']!="")
		$historia=utf8_encode($row['presentacion']);
	else	
		$historia="No hay historia registrada para este equipo";
}	

?>

<div style="float:left;margin-right:10px;"><?=paintImg($row['logo'],$row['nom_equipo'],120,"/img/shield.jpg");?></div>
<div><?=$historia?></div>
