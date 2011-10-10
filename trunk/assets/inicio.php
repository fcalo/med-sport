<?
$noscript=!file_exists("../back/libs/session.php");
if($noscript){
	include(dirname(__FILE__)."/../../admin/libs/session.php");
	include(dirname(__FILE__)."/../../admin/config/database.php");
	//include(dirname(__FILE__)."/util.php");
}else{
	include("../back/libs/session.php");
	include("../admin/config/database.php");
	include("util.php");
	$idEquipo=$_GET['k'];
}
$sql="select e.id_temporada, e.temporada from t_temporadas e, t_torneos o where e.id_temporada=o.temporada and o.id_equipo=".$idEquipo;
$sql.=" order by id_temporada desc;";
$rs=$db->get_results($sql,ARRAY_A);
$idTemporada=$rs[0]['id_temporada'];
$temporada=$rs[0]['temporada'];
?>
<input id="temporada" type="hidden" value="<?=$idTemporada?>">
<?
if($idTemporada!=""){
	$sql="select distinct nom_torneo, id_torneo from t_torneos ";
	$sql.=" where id_equipo=".$idEquipo;
	$sql.=" and temporada=".$idTemporada;
	$rst=$db->get_results($sql,ARRAY_A);
	$idTorneo=$rst[0]['id_torneo'];
	$torneo=utf8_encode($rst[0]['nom_torneo'])	;
}
?>
<input id="torneo" type="hidden" value="<?=$idTorneo?>">
<?
if($idTorneo!=""){
	$sql="select distinct jornada from t_clasificaciones ";
	$sql.=" where id_torneo=".$idTorneo;
	$sql.=" order by jornada desc";
	$rs=$db->get_results($sql,ARRAY_A);
	$jornada=$rs[0]['jornada'];
}
?>
<input id="jornada" type="hidden" value="<?=$jornada?>">
<div id="inicio">
<div id="inicio-left">
	<div class="inicio-label">Evoluci&oacute;n</div>
	<?if(!$noscript){?>
		<div id="chart">Unable to load Flash content. The YUI Charts Control requires Flash Player 9.0.45 or higher. You can download the latest version of Flash Player from the <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player Download Center</a>.</p></div>
	<?}?>
	<div class="inicio-label"><div>Clasificaci&oacute;n <?=$temporada?>&nbsp;</div><div ><?=$torneo?>&nbsp;</div><div style='white-space:nowrap;'><i>jornada <?=$jornada?></i></div></div>
	<div id="bd-clasificacion"><?if($noscript) include('load_clasificacion.php');?></div>
	<div class="inicio-label" style="margin:40px 0 20px 0">Pr&oacute;ximo partido</div>
	<div id="bd-proximos"><?if($noscript) include('load_proximos.php')?></div>
	<div class="inicio-label" style="clear:both;margin:40px 0 20px 0">Ultimos partidos</div>
	<div id="bd-resultados"><?
	if($noscript){ 
		include('load_resultados.php');
	}?></div>
	<?if($noscript){
		echo "<div><br><br><br><br>";
		include('enlaces.php');
		echo "</div>";
	}
	?>
	
</div>
<div id="inicio-right">
	<?include('tags_goleadores.php');?>
	<h3>Jugadores</h3>
	<div id="bd-plantilla"><?if($noscript) include('load_plantilla.php')?></div>
</div>
</div>