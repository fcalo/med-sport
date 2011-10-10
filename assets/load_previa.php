<?
if(!isset($_GET['k'])){
	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
	header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header( "Cache-Control: no-cache, must-revalidate" );
	header( "Pragma: no-cache" );
	header('Content-Type: text/html; charset=UTF-8');
	include("../admin/config/database.php");
	include("../back/libs/session.php");
	include("util.php");
	$idPartido=$_GET['p'];
	$contraer=true;
}else{
	$contraer=false;
	$idPartido=$_GET['k'];
}
//echo "Cargando los resultados del equipo ".$_GET['e']." para el torneo  ".$_GET['r']." en la jornada".$_GET['j'];
$html="";

$sql="select count(*) c from t_partidos ";
$sql.=" where id_torneo=(select id_torneo from t_partidos where id_partido=".$idPartido.")";
$sql.=" and (ind_jugado='S' or (goles_mios>0 or goles_rival>0))";
$row=$db->get_row($sql,ARRAY_A);
$totalPartidos=$row['c'];

$sql="select 'm' tipo, id_partido,  p.fecha, date_format(p.fecha,'%e/%c/%Y') fecha,  e.nom_equipo local, te.nom_equipo visitante,";
$sql.=" p.goles_mios goles_local, p.goles_rival goles_visitante, hora, lugar, cronica, 0 id_local, p.rival id_visitante";
$sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
$sql.=" where e.id_equipo=p.id_equipo";
$sql.=" and p.id_partido=".$idPartido;
$sql.=" and ind_visitante='N'";
$sql.=" and te.id_torneos_equipos=p.rival";
$sql.=" and (ind_jugado='N' and coalesce(p.goles_mios,0)=0 or coalesce(p.goles_rival,0)=0)";
$sql.=" union";
$sql.=" select 'm' tipo, id_partido, p.fecha,  date_format(p.fecha,'%e/%c/%Y') fecha, te.nom_equipo local, e.nom_equipo visitante,";
$sql.=" p.goles_rival goles_local, p.goles_mios goles_visitante, hora, lugar, cronica, p.rival id_local, 0 id_visitante";
$sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
$sql.=" where e.id_equipo=p.id_equipo";
$sql.=" and p.id_partido=".$idPartido;
$sql.=" and ind_visitante='S'";
$sql.=" and te.id_torneos_equipos=p.rival";
$sql.=" and (ind_jugado='N' and coalesce(p.goles_mios,0)=0 or coalesce(p.goles_rival,0)=0)";
$sql.=" order by 3 , 2 ";
if ($idPartido==1)
	$sql.=" limit 1";


$rs=$db->get_results($sql,ARRAY_A);
$count=sizeof($rs);
if($count==0){
	echo utf8_encode("A&uacute;n no hay datos");
	//exit;
}

for($i=0;$i<$count;$i++){
	$row=$rs[$i];
	$sql="select max(id_torneo) id_torneo from v_equipos_torneo where (id_torneos_equipos='".$row['id_local']."' or id_torneos_equipos='".$row['id_visitante']."') and id_torneos_equipos!=0";
	$r=$db->get_row($sql,ARRAY_A);
	$idTorneo=$r['id_torneo'];
	if($row['id_local']==0){
		$sql="select e.url_equipo, d.url_deporte ";
		$sql.=" from t_equipos e inner join t_torneos t on t.id_equipo=e.id_equipo";
		$sql.=" inner join t_deportes d on d.id_deporte=e.id_deporte ";
		$sql.=" where id_torneo='".$idTorneo."'";
		$r=$db->get_row($sql,ARRAY_A);
		$url=getServer()."/deporte/".$r['url_deporte']."/".$r['url_equipo'];
	}else{
		$url=getUrlEquipoSinAdmin($idTorneo,$row['id_local']);
	}
	if($contraer)
		if ($url==$_SERVER['HTTP_REFERER'])
			$local="<b>".utf8_encode($row['local'])."</a>";
		else
			$local="<a href='".$url."'>".utf8_encode($row['local'])."</a>";
	else
		$local="<a href='".$url."'>".utf8_encode($row['local'])."</a>";


	if($row['id_visitante']==0){
		$sql="select e.url_equipo, d.url_deporte ";
		$sql.=" from t_equipos e inner join t_torneos t on t.id_equipo=e.id_equipo";
		$sql.=" inner join t_deportes d on d.id_deporte=e.id_deporte ";
		$sql.=" where id_torneo='".$idTorneo."'";
		$r=$db->get_row($sql,ARRAY_A);
		$url=getServer()."/deporte/".$r['url_deporte']."/".$r['url_equipo'];
	}else{
		$url=getUrlEquipoSinAdmin($idTorneo,$row['id_visitante']);
	}

	if($contraer)
		if ($url==$_SERVER['HTTP_REFERER'])
			$visitante="<b>".utf8_encode($row['visitante'])."</a>";
		else
			$visitante="<a href='".$url."'>".utf8_encode($row['visitante'])."</a>";
	else
		$visitante="<a href='".$url."'>".utf8_encode($row['visitante'])."</a>";

	$cronica=utf8_encode($row['cronica']);
	$html.="<div class='partido'>";
	if($contraer){
		if($row['hora']!="" && $row['lugar']!=""){
			$html.="<div style='float:right'><a href='javascript:loadProximos()'><img src='/img/contraer.gif' alt='Contraer detalles' title='Contraer detalles'></a></div>";
			$html.="<div style='float:right'><a href='/partido/".$row['id_partido']."' target='_blank'><img src='/img/maximizar.gif' alt='Abrir partido en ventana nueva' title='Abrir partido en ventana nueva'></a></div>";
		}
		
	}
	$html.="<div class='partido-fecha'>".$row['fecha']." ".$row['hora']." ".utf8_encode($row['lugar']);
	$html.="</div>";
	$html.="<div class='partido-equipo'>".$local."</div>";
	$html.="<div class='partido-goles'>-</div>";
	$html.="<div class='partido-goles'>-</div>";
	$html.="<div class='partido-equipo'>".$visitante."</div>";
	$html.="</div>";
}

$sql="select p.numero, p.nombre, a.ind_asistencia, s.email, p.id_plantilla, coalesce(t_p.partidos,0) partidos ";
$sql.=" from t_plantilla p inner join t_partidos e on e.id_equipo=p.id_equipo";
$sql.=" INNER JOIN t_torneos t ON t.id_torneo=e.id_torneo AND t.temporada=p.temporada ";
$sql.=" left join t_partidos_asistencia a on p.id_plantilla=a.id_plantilla and a.id_partido=e.id_partido";
$sql.=" left join (select id_plantilla, email from t_solicitudes where fec_confirmacion is not null) s on p.id_plantilla=s.id_plantilla";
$sql.=" left join (select id_plantilla, count(*) partidos from t_partidos_plantilla pp inner join t_partidos p on p.id_partido=pp.id_partido";
$sql.=" 	where p.id_torneo=(select id_torneo from t_partidos where id_partido=".$idPartido.")";
$sql.=" 	and (p.ind_jugado='S' or (p.goles_mios>0 or p.goles_rival>0))";
$sql.=" 	group by id_plantilla) t_p on t_p.id_plantilla=p.id_plantilla";
$sql.=" where e.id_partido=".$idPartido;
$sql.=" order by if(numero='',999,numero)";	

$html.="<div id='detalle-partido'>";
if($cronica!=""){
	$html.="<div id='titulo-detalle'>Previa del partido</div>";
	$html.="<div id='cronica-partido'>".$cronica."</div>";
}
$html.="<div id='titulo-detalle'>Asistencia prevista</div>";
$html.="<div id='panel-asistencia'>";
	$rs=$db->get_results($sql,ARRAY_A);
	$count=sizeof($rs);
	for($i=0;$i<$count;$i++){
		$row=$rs[$i];
		$nombre=utf8_encode($row['nombre']);
		$user=false;
		if($row['email']!="" && $row['email']==$_SESSION['uusSerRmed_player']){
			$user=true;
			$padding="style='padding:12px 5px;'";
		}
		$html.="<div class='item-asistencia' $padding><div style='float:left;'>".$nombre."</div><div style='float:right;padding-right:5px;'>";
		if($user){
			$html.='<input type="hidden" id="idpartido" value="'.$idPartido.'">';
			$html.='<input type="hidden" id="idplantilla" value="'.$row['id_plantilla'].'">';
			$html.='<div id="buttonsasistencia" class="yui-buttongroup">';
			if($row['ind_asistencia']=='S')
				$checked="checked";
			$html.='<input id="radio_asistencia_ok" type="radio" name="radiofield1" value="Asistir&eacute;"  '.$checked.'">';
			$checked="";
			if($row['ind_asistencia']=='N')
				$checked="checked";
			$html.='<input id="radio_asistencia_ko" type="radio" name="radiofield1" value="No Asistir&eacute;" '.$checked.'">';
			$html.='</div>';
		}else{
			if($row['ind_asistencia']=='S')
				$html.='<b>Asistir&aacute;</b>';
			if($row['ind_asistencia']=='N')
				$html.='<b>No asistir&aacute;</b>';
			if($row['ind_asistencia']==null)
				$html.='<b>Tal vez asista</b>';
			if($totalPartidos>0)
				$html.='. Asiste al '.round((($row['partidos']/$totalPartidos)*100),1).'% de los encuentros.';
		}
		$html.="</div></div>"; 
	}
	
$html.="</div>";
$html.="<div id='comentarios-partido'>";
	$html.="<div id='titulo-comentarios'>Comentarios</div>";
	$html.="</div>";
$html.="</div>";
echo $html;
$bpartido=true;
$id=$idPartido;
include('comentarios.php');

?>