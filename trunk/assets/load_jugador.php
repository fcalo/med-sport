<?
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');
include("../admin/config/database.php");
include("util.php");

//echo "Cargando la plantilla del equipo ".$_GET['e']." para la temporada ".$_GET['t'];
$html="";

//Textos propios del deporte
$sql="select d.id_deporte, d.texto_goles,d.texto_goles_singular, d.texto_amarillas, d.texto_rojas ";
$sql.=" from t_deportes d ";
$sql.=" join t_equipos e on d.id_deporte=e.id_deporte";
$sql.=" join t_plantilla p on e.id_equipo=p.id_equipo";
$sql.=" where p.id_plantilla=".$_GET['j'];
$rsTextos=$db->get_results($sql,ARRAY_A);
if($rsTextos){
	$idDeporte=$rsTextos[0]['id_deporte'];
	$textoGoles=utf8_encode($rsTextos[0]['texto_goles']);
	$textoGolesSingular=utf8_encode($rsTextos[0]['texto_goles_singular']);
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
	$sql.=" coalesce(t.faltas,'0') faltas";
	$sql.=" from t_plantilla left join";
	$sql.=" (select id_plantilla, count(*) partidos, sum(goles) goles,";
	$sql.=" sum(faltas) faltas,sum(uno) uno,sum(dos) dos,sum(tres) tres";
	$sql.=" from t_partidos_plantilla pp, t_partidos p, t_torneos t";
	$sql.=" where pp.id_plantilla=".$_GET['j'];
	$sql.=" and p.id_torneo=t.id_torneo";
	$sql.=" and pp.id_partido=p.id_partido";
	$sql.=" group by id_plantilla ) t on t_plantilla.id_plantilla=t.id_plantilla";
	$sql.=" where t_plantilla.id_plantilla=".$_GET['j']." order by if(numero='',999,numero);";
}else{
	$sql="select t_plantilla.id_plantilla, imagen, nombre, puesto, numero, floor((datediff(now(),fec_nacimiento)/365.25)) edad, coalesce(t.partidos,'0') partidos, ";
	$sql.=" coalesce(t.goles,'0') goles, coalesce(t.amarillas,'0') amarillas, coalesce(t.rojas,'0') rojas";
	$sql.=" from t_plantilla left join";
	$sql.=" (select id_plantilla, count(*) partidos, sum(goles) goles,";
	$sql.=" sum(if(ind_amonestacion='S',1,0)) amarillas,sum(if(ind_exclusion='S',1,0)) rojas";
	$sql.=" from t_partidos_plantilla pp, t_partidos p, t_torneos t";
	$sql.=" where pp.id_plantilla=".$_GET['j'];
	$sql.=" and p.id_torneo=t.id_torneo";
	$sql.=" and pp.id_partido=p.id_partido";
	$sql.=" group by id_plantilla ) t on t_plantilla.id_plantilla=t.id_plantilla";
	$sql.=" where t_plantilla.id_plantilla=".$_GET['j']." order by if(numero='',999,numero);";
}


$rs=$db->get_results($sql,ARRAY_A);

$row=$rs[0];
$imagen=utf8_encode($row['imagen']);
$nombre=utf8_encode($row['nombre']);
$puesto=utf8_encode($row['puesto']);

$html.="<div id='jugador-detalle'>";
	$html.="<div id='img-jugador'>".paintImg($imagen,$nombre,200,"./img/player200.gif")."</div>";
	$html.="<div id='nombre-jugador'><div style='padding-top:10px;font-size:50%;float:right'><a href='javascript:loadPlantilla()'>Volver</a></div>".$nombre."</div>";
	$html.="<div class='datos-jugador-detalle' style='border:0'>";
		$html.="<div>".$puesto."</div>";
		$html.="<div>".$row['edad']." a&ntilde;os</div>";
	$html.="</div>";
	$html.="<div class='datos-jugador-detalle'><ul>";
		
		$sql="select pp.goles, date_format(p.fecha,'%e/%c/%Y') fecha, te.nom_equipo rival, pp.ind_amonestacion, pp.ind_exclusion, pp.tres, pp.dos, pp.uno, pp.faltas ";
		$sql.=" from t_partidos p, t_partidos_plantilla pp, t_torneos_equipos te";
		$sql.=" where p.id_partido=pp.id_partido ";
		$sql.=" and pp.id_plantilla=".$_GET['j'];
		$sql.=" and te.id_torneos_equipos=p.rival";
		$rs=$db->get_results($sql,ARRAY_A);
		$count=sizeof($rs);
		for($i=0;$i<$count;$i++){
			$row=$rs[$i];
			$html.="<li>El ".$row['fecha']." jug&oacute; contra ".utf8_encode($row['rival']);
			$acciones="";
			if($row['goles']>1)
				$acciones.=" , metio ".$row['goles']." ".$textoGoles;
			else
				if($row['goles']>0)
					$acciones.=" , metio ".$row['goles']." ".$textoGolesSingular;
			if($row['ind_amonestacion']=='S')
				$acciones.=" , fue amonestado ";
			if($row['ind_exclusion']=='S')
				$acciones.=" , fue expulsado ";
			if($isBaloncesto){
				if($row['tres']>1)
					$acciones.=" , ".$row['tres']." triples";
				else
					if($row['tres']>0)
						$acciones.=" , ".$row['tres']." triple";
				
				if($row['dos']>0)
					$acciones.=" , ".$row['dos']." de dos";
				
				if($row['uno']>1)
					$acciones.=" , ".$row['uno']." tiros libres";
				else
					if($row['uno']>0)
						$acciones.=" , ".$row['uno']." tiro libre";
				
				if($row['faltas']>1)
					$acciones.=" , cometi&oacute; ".$row['faltas']." faltas";
				else
					if($row['faltas']>0)
						$acciones.=" , cometi&oacute; ".$row['faltas']." falta";
			}
			
			
			$aacciones=explode(",",$acciones);
			$countAcciones=sizeof($aacciones);
			for($iAcciones=1;$iAcciones<$countAcciones;$iAcciones++){
				if($iAcciones+1!=$countAcciones)
					$html.=",".$aacciones[$iAcciones];
				else
					$html.=" y".$aacciones[$iAcciones];
			}
			$html.=".";
			$html.="</li>";
		}
	$html.="</ul></div>";
$html.="</div>";
echo $html;


?>