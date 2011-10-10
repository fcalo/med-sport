<?
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');
include("../admin/config/database.php");

$sql="select nom_torneo v, id_torneo k from t_torneos ";
$sql.=" where id_equipo=".$_GET['e'];
$sql.=" and temporada=".$_GET['t'];

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