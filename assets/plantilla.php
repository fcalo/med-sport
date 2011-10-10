<?
include("../admin/config/database.php");
include("util.php");
?>


<div><select id="temporada" onchange="loadPlantilla()">
	<option value=""></option>
	<?
	$sql="select distinct e.id_temporada, e.temporada from t_temporadas e, t_torneos o where e.id_temporada=o.temporada and o.id_equipo=".$_GET['k'];
        $sql.=" order by e.id_temporada desc";
	$rs=$db->get_results($sql,ARRAY_A);
	$count=sizeof($rs);
	if($count==0){
		$sql="select distinct t.id_temporada, t.temporada from t_plantilla p inner join t_temporadas t on t.id_temporada=p.temporada where p.id_equipo=".$_GET['k'];
		$rs=$db->get_results($sql,ARRAY_A);
		$count=sizeof($rs);
	}
	for($i=0;$i<$count;$i++){
		if($i==0){?>
			<option selected value="<?=$rs[$i]['id_temporada']?>"><?=utf8_encode($rs[$i]['temporada'])?></option>
		<?}else{?>
			<option value="<?=$rs[$i]['id_temporada']?>"><?=utf8_encode($rs[$i]['temporada'])?></option>
	<?}}?> 
</select></div>
<div class="clear"></div>
<div id="bd-plantilla"></div>
