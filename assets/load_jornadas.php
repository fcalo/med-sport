<?
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');
include("../admin/config/database.php");

$sql="select distinct jornada v, jornada k from t_partidos ";
$sql.=" where id_torneo=".$_GET['r'];
$sql.=" and (ind_jugado='S' or goles_mios>0 or goles_rival>0)";
$sql.=" union distinct select distinct jornada v, jornada k from t_otros_partidos ";
$sql.=" where id_torneo=".$_GET['r'];
$sql.=" order by 1 desc";

$rs=$db->get_results($sql,ARRAY_A);
$count=sizeof($rs);
for($i=0;$i<$count;$i++){
	$count2=sizeof($rs[$i]);
	foreach($rs[$i] as $k=>$v){
		$rs[$i][$k]=utf8_encode($rs[$i][$k]);
	}
}
$rt.=json_encode($rs);
echo $rt;
?>