<div id="enlaces">
<ul>
<?
if(!$noscript){
	include("../admin/config/database.php");
	$idEquipo=$_GET['k'];
}
	

$sql="select titulo, url from t_enlace l, t_equipos e ";
$sql.=" where id_equipo=".$idEquipo;
$sql.=" and l.user=e.user";

$rs=$db->get_results($sql,ARRAY_A);
$count=sizeof($rs);
for($i=0;$i<$count;$i++){
	$row=$rs[$i];
	$url=$row['url'];
	if(strpos("ll".$url,"http://")<=0)
		$url="http://".$url;
	$titulo=utf8_encode($row['titulo']);?>
	<li><a href='<?=$url?>' target='_blank'><?=$titulo?></a></li>
		
<?}?> 
</ul></div>
