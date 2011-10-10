<?
if(!isset($_GET['k'])){
	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
	header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header( "Cache-Control: no-cache, must-revalidate" );
	header( "Pragma: no-cache" );
	header('Content-Type: text/html; charset=UTF-8');
	include("../admin/libs/session.php");
	include("../admin/config/database.php");
	include("util.php");
	$idPartido=$_GET['p'];
	$contraer=true;
}else{
	$contraer=false;
	$idPartido=$_GET['k'];
}
$last=$_GET['l']==1;

$html="";

$sql="select 'm' tipo, id_partido,  date_format(p.fecha,'%e/%c/%Y') fecha,  e.nom_equipo local, te.nom_equipo visitante,";
$sql.=" p.goles_mios goles_local, p.goles_rival goles_visitante, ind_visitante, cronica, id_partido, 0 id_local, p.rival id_visitante";
$sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
$sql.=" where e.id_equipo=p.id_equipo";
$sql.=" and p.id_partido=".$idPartido;
$sql.=" and ind_visitante='N'";
$sql.=" and te.id_torneos_equipos=p.rival";
$sql.=" union";
$sql.=" select 'm' tipo, id_partido,  date_format(p.fecha,'%e/%c/%Y') fecha, te.nom_equipo local, e.nom_equipo visitante,";
$sql.=" p.goles_rival goles_local, p.goles_mios goles_visitante, ind_visitante, cronica, id_partido, p.rival id_local, 0 id_visitante";
$sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
$sql.=" where e.id_equipo=p.id_equipo";
$sql.=" and p.id_partido=".$idPartido;
$sql.=" and ind_visitante='S'";
$sql.=" and te.id_torneos_equipos=p.rival";

$rs=$db->get_results($sql,ARRAY_A);

$count=sizeof($rs);


$i=0;
$row=$rs[$i];
$id=$row['id_partido'];
$bvisitante=$row['ind_visitante']=='S';


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
	if($last)
		$html.="<div style='float:right;'><a href='javascript:loadLastResultados()'><img src='/img/contraer.gif' alt='Contraer detalles' title='Contraer detalles' ></a></div>";
	else
		$html.="<div style='float:right;'><a href='javascript:loadResultados()' ><img src='/img/contraer.gif'  alt='Contraer detalles' title='Contraer detalles'></a></div>";
	$html.="<div style='float:right'><a href='/partido/".$row['id_partido']."' target='_blank'><img src='/img/maximizar.gif' alt='Abrir partido en ventana nueva' title='Abrir partido en ventana nueva'></a></div>";
}
$html.="<div class='partido-fecha'>".$row['fecha'];

$html.="</div>";
$html.="<div class='partido-equipo'>".$local."</div>";
$html.="<div class='partido-goles'>".$row['goles_local']."</div>";
$html.="<div class='partido-goles'>".$row['goles_visitante']."</div>";
$html.="<div class='partido-equipo'>".$visitante."</div>";
$html.="</div>";


//Textos propios del deporte
$sql="select d.id_deporte, d.texto_goles, d.texto_amarillas, d.texto_rojas ";
$sql.=" from t_deportes d ";
$sql.=" join t_equipos e on d.id_deporte=e.id_deporte";
$sql.=" join t_torneos t on e.id_equipo=t.id_equipo";
$sql.=" join t_partidos p on p.id_torneo=t.id_torneo";
$sql.=" where p.id_partido=".$idPartido;
$rsTextos=$db->get_results($sql,ARRAY_A);
if($rsTextos){
	$idDeporte=$rsTextos[0]['id_deporte'];
	$textoGoles=utf8_encode($rsTextos[0]['texto_goles']);
	$textoAmarillas=utf8_encode($rsTextos[0]['texto_amarillas']);
	$textoRojas=utf8_encode($rsTextos[0]['texto_rojas']);
}
$isBaloncesto=$idDeporte==4;

//Jugadores
$sql="select p.numero, p.nombre, pp.goles, if(pp.ind_amonestacion='S',1,0) amarillas, if(pp.ind_exclusion='S',1,0) rojas, tres, dos, uno, faltas ";
$sql.=" from t_partidos_plantilla pp INNER JOIN t_plantilla p ON p.id_plantilla=pp.id_plantilla ";
$sql.=" INNER JOIN t_partidos e on e.id_equipo=p.id_equipo AND e.id_partido=pp.id_partido";
$sql.=" INNER JOIN t_torneos t ON t.id_torneo=e.id_torneo AND t.temporada=p.temporada ";
$sql.=" where pp.id_partido=".$idPartido;
$sql.=" order by if(numero='',999,numero)";	


/*$html.="<div id='detalle-partido'>";
	$html.="<div id='cronica-partido' style='float:".$floatCronica.";'>".$cronica."</div>";
		$html.="<div id='jugadores-partido' style='float:".$float.";'>";
			$html.="<div id='jugador'>";
				$html.="<div id='tabla-jugador'>";
					$html.="<table  cellspacing='0' cellpadding='0' border='0'><tbody>"; 
					$html.="<tr><th>N".utf8_encode("�")."</th><th>Jugador</th><th>Goles</th><th>Amarillas</th><th>Expulsiones</th>"; 
						
					$rs=$db->get_results($sql,ARRAY_A);
					$count=sizeof($rs);
					for($i=0;$i<$count;$i++){
						$row=$rs[$i];
						$nombre=utf8_encode($row['nombre']);
						$html.="<tr><td>".$row['numero']."</td><td>".$nombre."</td><td>".$row['goles']."</td><td>".$row['amarillas']."</td><td>".$row['rojas']."</td>"; 
					}
					$html.="</tbody></table>"; 
				$html.="</div>";
			$html.="</div>f";
		$html.="</div>";
	$html.="</div>";
$html.="</div>";>*/




$html.="<div id='detalle-partido'>";
	$html.="<div id='titulo-detalle'>Detalle del partido</div>";
	
	$html.="<div id='jugadores-partido'>";
		
		$html.="<div id='jugador'>Jugadores";
		
			$html.="<div id='tabla-jugador'>";
					$html.="<table><tbody>"; 
					if($isBaloncesto)
						$html.="<tr><th>Nº</th><th>Jugador</th><th>".$textoGoles."</th><th>T3</th><th>T2</th><th>T1</th><th>Faltas</th>";
					else
						$html.="<tr><th>Nº</th><th>Jugador</th><th>".$textoGoles."</th><th>".$textoAmarillas."</th><th>".$textoRojas."</th>";
					
						
					$rs=$db->get_results($sql,ARRAY_A);
					$count=sizeof($rs);
					for($i=0;$i<$count;$i++){
						$row=$rs[$i];
						$nombre=utf8_encode($row['nombre']);
						if($isBaloncesto)
							$html.="<tr><td>".$row['numero']."</td><td>".$nombre."</td><td>".$row['goles']."</td><td>".$row['tres']."</td><td>".$row['dos']."</td><td>".$row['uno']."</td><td>".$row['faltas']."</td>"; 
						else
							$html.="<tr><td>".$row['numero']."</td><td>".$nombre."</td><td>".$row['goles']."</td><td>".$row['amarillas']."</td><td>".$row['rojas']."</td>"; 
					}
					$html.="</tbody></table>"; 
			$html.="</div>";
		$html.="</div>";
	$html.="</div>";
	$html.="<div id='cronica-partido'>".$cronica."</div>";
$html.="</div>";

$html.="<div id='comentarios-partido'>";
	$html.="<div id='titulo-comentarios'>Comentarios</div>";
	$html.="</div>";
$html.="</div>";
echo $html;
$id=$idPartido;
$bpartido=true;
include('comentarios.php');

?>