<?
include("../back/libs/session.php");
include("../admin/config/database.php");
include("util.php");
?>


<div class="filtro-equipo"><select id="temporada" onchange="loadTorneos()">
	<option value=""></option>
	<?
	$sql="select distinct e.id_temporada, e.temporada from t_temporadas e, t_torneos o where e.id_temporada=o.temporada and o.id_equipo=".$_GET['k'];
        $sql.=" order by e.id_temporada desc";
	$rs=$db->get_results($sql,ARRAY_A);
	$count=sizeof($rs);
	for($i=0;$i<$count;$i++){
		if($i==0){?>
			<option selected value="<?=$rs[$i]['id_temporada']?>"><?=utf8_encode($rs[$i]['temporada'])?></option>
		<?}else{?>
			<option value="<?=$rs[$i]['id_temporada']?>"><?=utf8_encode($rs[$i]['temporada'])?></option>
	<?}}?> 
</select></div>
<div class="filtro-equipo"><select id="torneo" onchange="loadJornadas()"></select></div>
<div class="filtro-equipo"><select id="jornada" onchange="loadResultados()"></select></div>

<div class="clear"></div>
<div id="bd-resultados"></div>
