<?
$html="";
if (!isset($_GET['e']) && !isset($_GET['t'])){

}else{
	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
	header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header( "Cache-Control: no-cache, must-revalidate" );
	header( "Pragma: no-cache" );
	header('Content-Type: text/html; charset=UTF-8');
//	if file_exists(
	require("../admin/config/database.php");
	include("./util.php");
	
	
	//echo "Cargando la plantilla del equipo ".$_GET['e']." para la temporada ".$_GET['t'];
	$html="";
	
	if($_GET['e']=="" || $_GET['t']==""){
		echo utf8_encode("A&uacute;n no hay datos");
                $salir=true;
		//exit;
	}
	$idEquipo=$_GET['e'];
	$idTemporada=$_GET['t'];
}

if(!$salir){
	//Textos propios del deporte
	$sql="select d.id_deporte, d.texto_goles, d.texto_amarillas, d.texto_rojas ";
	$sql.=" from t_deportes d ";
	$sql.=" join t_equipos e on d.id_deporte=e.id_deporte";
	$sql.=" where e.id_equipo=".$idEquipo;
	$rsTextos=$db->get_results($sql,ARRAY_A);
	

	if($rsTextos){
		$idDeporte=$rsTextos[0]['id_deporte'];
		$textoGoles=utf8_encode($rsTextos[0]['texto_goles']);
		$textoAmarillas=utf8_encode($rsTextos[0]['texto_amarillas']);
		$textoRojas=utf8_encode($rsTextos[0]['texto_rojas']);
	}
	$isBaloncesto=$idDeporte==4;
	


	if($isBaloncesto){
		$sql="select t_plantilla.id_plantilla, imagen, nombre, puesto, numero, floor((datediff(now(),fec_nacimiento)/365.25)) edad,";
		$sql.=" coalesce(t.partidos,'0') partidos, ";
		$sql.=" coalesce(t.goles,'0') goles, coalesce(t.faltas,'0') amarillas, ";
		$sql.=" coalesce(t.tres,'0') tres,";
		$sql.=" coalesce(t.dos,'0') dos,";
		$sql.=" coalesce(t.uno,'0') uno,";
		$sql.=" coalesce(t.faltas,'0') faltas, fec_confirmacion, ind_admin";
		$sql.=" from t_plantilla left join";
		$sql.=" (select id_plantilla, count(*) partidos, sum(goles) goles,";
		$sql.=" sum(faltas) faltas,sum(uno) uno,sum(dos) dos,sum(tres) tres";
		$sql.=" from t_partidos_plantilla pp, t_partidos p, t_torneos t";
		$sql.=" where t.id_equipo=".$idEquipo;
		$sql.=" and t.temporada=".$idTemporada;
		$sql.=" and p.id_torneo=t.id_torneo";
		$sql.=" and pp.id_partido=p.id_partido";
		$sql.=" group by id_plantilla ) t on t_plantilla.id_plantilla=t.id_plantilla";
		$sql.=" left join (select id_plantilla, max(fec_confirmacion) fec_confirmacion from t_solicitudes group by id_plantilla) t_solicitudes on t_plantilla.id_plantilla=t_solicitudes.id_plantilla";
		
		$sql.=" where id_equipo=".$idEquipo;
		$sql.=" and temporada=".$idTemporada." order by if(numero='',999,numero);";
	}else{
		$sql="select t_plantilla.id_plantilla, imagen, nombre, puesto, numero, floor((datediff(now(),fec_nacimiento)/365.25)) edad, coalesce(t.partidos,'0') partidos, ";
		$sql.=" coalesce(t.goles,'0') goles, coalesce(t.amarillas,'0') amarillas, coalesce(t.rojas,'0') rojas, fec_confirmacion, ind_admin";
		$sql.=" from t_plantilla left join";
		$sql.=" (select id_plantilla, count(*) partidos, sum(goles) goles,";
		$sql.=" sum(if(ind_amonestacion='S',1,0)) amarillas,sum(if(ind_exclusion='S',1,0)) rojas";
		$sql.=" from t_partidos_plantilla pp, t_partidos p, t_torneos t";
		$sql.=" where t.id_equipo=".$idEquipo;
		$sql.=" and t.temporada=".$idTemporada;
		$sql.=" and p.id_torneo=t.id_torneo";
		$sql.=" and pp.id_partido=p.id_partido";
		$sql.=" group by id_plantilla ) t on t_plantilla.id_plantilla=t.id_plantilla";
		$sql.=" left join (select id_plantilla, max(fec_confirmacion) fec_confirmacion from t_solicitudes group by id_plantilla) t_solicitudes on t_plantilla.id_plantilla=t_solicitudes.id_plantilla";
		$sql.=" where id_equipo=".$idEquipo;
		$sql.=" and temporada=".$idTemporada." order by if(numero='',999,numero);";
	}



	$rs=$db->get_results($sql,ARRAY_A);
	$count=sizeof($rs);

	if($count==0){
		echo "Aún no hay datos";
		//exit;
	}


	for($i=0;$i<$count;$i++){
		$row=$rs[$i];
		if ($i%3==0)
			$clear="style='clear:both'";
		else
			$clear="";
		$imagen=utf8_encode($row['imagen']);
		$nombre=utf8_encode($row['nombre']);
		$puesto=utf8_encode($row['puesto']);
		$html.="<div $clear class='jugador'>";
		$html.="<div class='img-jugador'>".paintImg($imagen,$nombre,48,"./img/player.gif")."</div>";
		$html.="<div class='datos-jugador'>";
		if($row['fec_confirmacion']==null && $row['ind_admin']!="S")
			$html.="<div class='link_identidad'><a style='color:#F80' href='javascript:solicitar(".$row['id_plantilla'].",\"".$nombre."\")'>&iexcl;Soy yo!</a></div>";
		$html.="<div><a href='javascript:verJugador(".$row['id_plantilla'].")'>".$nombre."</a></div>";
		$html.="<div>".$puesto."</div>";
		$html.="</div>";
		$html.="<div class='tabla-jugador'>";
		$html.="<table><tbody>"; 
		if ($isBaloncesto){
			$html.="<tr><th>Nº</th><th>Edad</th><th>Partidos</th><th>".$textoGoles."</th><th>T3</th><th>T2</th><th>T1</th><th>faltas</th>";
			$html.="<tr><td>".$row['numero']."</td><td>".$row['edad']."</td><td>".$row['partidos']."</td><td>".$row['goles']."</td><td>".$row['tres']."</td><td>".$row['dos']."</td><td>".$row['uno']."</td><td>".$row['faltas']."</td>"; 
		}else{
			$html.="<tr><th>Nº</th><th>Edad</th><th>Partidos</th><th>".$textoGoles."</th><th>".$textoAmarillas."</th><th>".$textoRojas."</th>";
			$html.="<tr><td>".$row['numero']."</td><td>".$row['edad']."</td><td>".$row['partidos']."</td><td>".$row['goles']."</td><td>".$row['amarillas']."</td><td>".$row['rojas']."</td>"; 
		}
		$html.="</tbody></table>"; 
		$html.="</div>";
		$html.="</div>";
		
	}
	echo $html;
}
?>