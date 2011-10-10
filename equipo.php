<?include("top.php")?>
<script type='text/javascript'>
<?
if (isset($_GET['k']) && trim($_GET['k'])!="")
	echo "var equipo=".$_GET['k']."";
else{
	if (isset($_GET['t'])){
		$sql="select e.id_equipo, te.id_torneos_equipos, te.nom_equipo from t_equipos e, t_deportes d, t_torneos t, t_torneos_equipos te";
		$sql.=" where d.id_deporte=e.id_deporte";
		//$sql.=" and e.url_equipo='".$_GET['n']."'";
		$sql.=" and d.url_deporte='".$_GET['d']."'";
		$sql.=" and t.id_torneo='".$_GET['t']."'";
		$sql.=" and t.id_equipo=e.id_equipo";
		$sql.=" and ".amigableMySql("te.nom_equipo")."='".$_GET['n']."'";
		$sql.=" and te.id_torneo=t.id_torneo";
		$row=$db->get_row($sql,ARRAY_A);
		
		if($row['id_equipo']!=""){
			echo "var equipo=".$row['id_equipo'].";";
			echo "var equipoTorneo=".$row['id_torneos_equipos'].";";
			
			$nomEquipo=utf8_encode($row['nom_equipo']);
		}
		echo "var admin=false;";
	}else{
		$sql="select e.id_equipo, e.nom_equipo from t_equipos e, t_deportes d";
		$sql.=" where d.id_deporte=e.id_deporte";
		$sql.=" and e.url_equipo='".$_GET['n']."'";
		$sql.=" and d.url_deporte='".$_GET['d']."'";
		$row=$db->get_row($sql,ARRAY_A);
		
		if($row['id_equipo']!=""){
			echo "var equipo=".$row['id_equipo'].";";
			echo "var admin=true";
			$nomEquipo=utf8_encode($row['nom_equipo']);
			$administrado=true;
		}else{
			echo "var equipo=0;";
			echo "var admin=false;";
			$nomEquipo=$_GET['n'];
		}
	}
	$idEquipo=$row['id_equipo'];
}
echo ';var reurl="'.$reurl.'";';
?>
</script>
	<!-- Required markup. -->
	
	<form name="administralo" id="administralo" method="POST" action="<?=getServer()?>/">
		<div style="display:none"><input type="hidden" name="equipo" value="<?=$_GET['n']?>"></div>
		<div style="display:none"><input type="hidden" name="deporte" value="<?=$_GET['d']?>"></div>
                <div style="display:none"><input type="hidden" name="torneo" value="<?=$_GET['t']?>"></div>
	</form>
	
	
	
	<!--[if gt IE 6]><link rel="stylesheet" type="text/css" href="<?=$reurl?>css/ie.css"/><![endif]-->
    <iframe id="yui-history-iframe" src="<?=$reurl?>js/yui3/assets/blank.html"></iframe>
    <input id="yui-history-field" type="hidden">
    
	<?
	include("js/comments.js.php");?>
	<?include("js/equipo.js.php");?>
	<div id="bd"><noscript><?@include('assets/inicio.php')?></noscript></div>
	
	
<?include("bottom.php")?>