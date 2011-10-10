<div id="imagenes">
<ul>
<?
include("../admin/config/database.php");
include("util.php");
$sql="select titulo, imagen from t_imagenes l, t_equipos e ";
$sql.=" where id_equipo=".$_GET['k'];
$sql.=" and l.user=e.user";

$rs=$db->get_results($sql,ARRAY_A);
$count=sizeof($rs);
for($i=0;$i<$count;$i++){
	$row=$rs[$i];
	$titulo=utf8_encode($row['titulo']);?>
	<div><?=paintImg($row['imagen'],$titulo,280,"", "loadimagen('".$row['imagen']."')");?></div>
		
<?}?>
</ul></div>
<div id="imagen-detalle">
<a href="javascript:vertodas();">Ver todas</a>
<div id="imagen-detalle-imagen"></div>
</div>
