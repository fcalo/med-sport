<?include("top.php");
$sql=" select count(*) c from t_partidos where ind_jugado='N' and (coalesce(goles_mios,0)=0 and coalesce(goles_rival,0)=0)";
$sql.=" and fecha>=now()";
$sql.=" and id_partido=".(isset($_GET['k'])?$_GET['k']:str_replace("/","",$_SERVER['PHP_SELF']));

$_GET['k']=(isset($_GET['k'])?$_GET['k']:str_replace("/","",$_SERVER['PHP_SELF']));
$row=$db->get_row($sql,ARRAY_A);
if($row['c']==0){?>
	<div id="bd"><div id="bd-nav"><?include('assets/load_partido.php');?></div></div>
<?}else{?>
	<div id="bd"><div id="bd-nav"><?include('assets/load_previa.php');?></div></div>
<?}
include("bottom.php");?>

