<?
if (isset($_GET['r']) || isset($_GET['j']) || isset($_GET['e'])){
	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
	header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header( "Cache-Control: no-cache, must-revalidate" );
	header( "Pragma: no-cache" );
	header('Content-Type: text/html; charset=UTF-8');
	include("./../admin/config/database.php");
	include("util.php");
	$admin=$_GET['a']==1;
        $idEquipo=$_GET['e'];
}else{
	include(dirname(__FILE__)."/../../admin/config/database.php");
	$admin=true;
}


//echo "Cargando los resultados del equipo ".$_GET['e']." para el torneo  ".$_GET['r']." en la jornada".$_GET['j'];
$html="";


if(isset($_GET['j']) && trim($_GET['j'])!=""){
	$last=0;
	$sql="select 'm' tipo, id_partido, date_format(p.fecha,'%e/%c/%Y') fecha,  e.nom_equipo local, te.nom_equipo visitante,";
	$sql.=" p.goles_mios goles_local, p.goles_rival goles_visitante";
	$sql.=" ,0 id_local, p.rival id_visitante ";
	$sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
	$sql.=" where e.id_equipo=p.id_equipo";
	$sql.=" and p.id_torneo=".$_GET['r'];
	$sql.=" and p.jornada=".$_GET['j'];
	$sql.=" and ind_visitante='N'";
	$sql.=" and te.id_torneos_equipos=p.rival";
	$sql.=" and (ind_jugado='S' or p.goles_mios>0 or p.goles_rival>0)";
	$sql.=" union";
	$sql.=" select 'm' tipo, id_partido,  date_format(p.fecha,'%e/%c/%Y') fecha, te.nom_equipo local, e.nom_equipo visitante,";
	$sql.=" p.goles_rival goles_local, p.goles_mios goles_visitante";
	$sql.=" ,p.rival id_local, 0 id_visitante ";
	$sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
	$sql.=" where e.id_equipo=p.id_equipo";
	$sql.=" and p.id_torneo=".$_GET['r'];
	$sql.=" and p.jornada=".$_GET['j'];
	$sql.=" and ind_visitante='S'";
	$sql.=" and te.id_torneos_equipos=p.rival";
	$sql.=" and (ind_jugado='S' or p.goles_mios>0 or p.goles_rival>0)";
	$sql.=" union";
	$sql.=" select 'o' tipo, p.id_partido,  date_format(p.fecha,'%e/%c/%Y') fecha, tel.nom_equipo local, tev.nom_equipo visitante, p.goles_local, p.goles_visitante,";
	$sql.=" p.local id_local, p.visitante id_visitante ";
	$sql.=" from t_otros_partidos p, t_torneos_equipos tel, t_torneos_equipos tev";
	$sql.=" where p.id_torneo=".$_GET['r'];
	$sql.=" and p.jornada=".$_GET['j'];
	$sql.=" and tel.id_torneos_equipos=p.local";
	$sql.=" and tev.id_torneos_equipos=p.visitante";
	$sql.=" order by 1";
}else{
	$last=1;
	if ($admin){
		$sql="select 'm' tipo, p.fecha, id_partido,  date_format(p.fecha,'%e/%c/%Y') fecha,  e.nom_equipo local, te.nom_equipo visitante,";
		$sql.=" p.goles_mios goles_local, p.goles_rival goles_visitante";
		$sql.=" ,0 id_local, p.rival id_visitante ";
		$sql.=" from t_equipos e, t_torneos_equipos te, t_partidos p";
                $sql.=" INNER JOIN t_torneos t ON t.id_torneo=p.id_torneo";
		$sql.=" where e.id_equipo=p.id_equipo";
		$sql.=" and e.id_equipo=".$idEquipo;
                if(isset($_GET['t']))
                    $sql.=" and t.temporada='".$_GET['t']."'";
		$sql.=" and ind_visitante='N'";
		$sql.=" and te.id_torneos_equipos=p.rival";
		$sql.=" and (ind_jugado='S' or p.goles_mios>0 or p.goles_rival>0)";
		$sql.=" union";
		$sql.=" select 'm' tipo, p.fecha, id_partido,  date_format(p.fecha,'%e/%c/%Y') fecha, te.nom_equipo local, e.nom_equipo visitante,";
		$sql.=" p.goles_rival goles_local, p.goles_mios goles_visitante";
		$sql.=" ,p.rival id_local, 0 id_visitante ";
		$sql.=" from t_equipos e, t_torneos_equipos te, t_partidos p";
                $sql.=" INNER JOIN t_torneos t ON t.id_torneo=p.id_torneo";
		$sql.=" where e.id_equipo=p.id_equipo";
		$sql.=" and e.id_equipo=".$idEquipo;
                if(isset($_GET['t']))
                    $sql.=" and t.temporada='".$_GET['t']."'";
		$sql.=" and ind_visitante='S'";
		$sql.=" and te.id_torneos_equipos=p.rival";
		$sql.=" and (ind_jugado='S' or p.goles_mios>0 or p.goles_rival>0)";
		$sql.=" order by 2 desc";
	}else{
		$sql="select 'o' tipo, p.fecha, id_partido,  date_format(p.fecha,'%e/%c/%Y') fecha, ";
		$sql.=" el.nom_equipo local, ev.nom_equipo visitante,";
		$sql.=" p.goles_local, p.goles_visitante";
		$sql.=" ,p.local id_local, p.visitante id_visitante ";
		$sql.=" from t_otros_partidos p, t_torneos_equipos el, t_torneos_equipos ev";
		$sql.=" where p.local=el.id_torneos_equipos";
		$sql.=" and p.visitante=ev.id_torneos_equipos";
		$sql.=" and (p.local=".$idEquipo." or p.visitante=".$idEquipo.")";
		$sql.=" and p.goles_local is not null ";
		$sql.=" and p.goles_visitante is not null ";
		$sql.=" union select 'o' tipo, p.fecha, id_partido,  date_format(p.fecha,'%e/%c/%Y') fecha, te.nom_equipo local, e.nom_equipo visitante,";
		$sql.=" p.goles_rival goles_local, p.goles_mios goles_visitante";
		$sql.=" ,p.rival id_local, 0 id_visitante ";
		$sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
		$sql.=" where e.id_equipo=p.id_equipo";
		$sql.=" and p.rival=".$idEquipo;
		$sql.=" and ind_visitante='S'";
		$sql.=" and te.id_torneos_equipos=p.rival";
		$sql.=" and (ind_jugado='S' or p.goles_mios>0 or p.goles_rival>0)";
		$sql.=" union select 'o' tipo, p.fecha, id_partido,  date_format(p.fecha,'%e/%c/%Y') fecha,  e.nom_equipo local, te.nom_equipo visitante,";
		$sql.=" p.goles_mios goles_local, p.goles_rival goles_visitante";
		$sql.=" ,0 id_local, p.rival id_visitante ";
		$sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
		$sql.=" where e.id_equipo=p.id_equipo";
		$sql.=" and p.rival=".$idEquipo;
		$sql.=" and ind_visitante='N'";
		$sql.=" and te.id_torneos_equipos=p.rival";
		$sql.=" and (ind_jugado='S' or p.goles_mios>0 or p.goles_rival>0)";
		$sql.=" order by 2 desc";
	}
}

$rs=$db->get_results($sql,ARRAY_A);
$count=sizeof($rs);

if($count==0){
	echo utf8_encode("A&uacute;n no hay datos");
//	exit;
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
	if ($url==$_SERVER['HTTP_REFERER'])
		$local="<b>".utf8_encode($row['local'])."</a>";
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
	if ($url==$_SERVER['HTTP_REFERER'])
		$visitante="<b>".utf8_encode($row['visitante'])."</a>";
	else
		$visitante="<a href='".$url."'>".utf8_encode($row['visitante'])."</a>";

	$html.="<div class='partido'>";
	if ($row['tipo']=="m" && $admin){
		//$html.="<div style='float:right;margin-right:10px'><a style='font-size:140%;font-weight:bold;color:#fff;' href='javascript:verPartido(".$row['id_partido'].",".$last.")' >M&aacute;s Detalles</a></div>";
		$html.="<div style='float:right'><a href='javascript:verPartido(".$row['id_partido'].",".$last.")' rel='nofollow'><img  alt='Desplegar detalles del partido de mi equipo' title='Desplegar detalles del partido de mi equipo' src='/img/desplegar.gif'></a></div>";
		$html.="<div style='float:right'><a href='/partido/".$row['id_partido']."' target='_blank' rel='nofollow'><img src='/img/maximizar.gif' alt='Abrir partido de mi equipo en ventana nueva' title='Abrir partido de mi equipo en ventana nueva'></a></div>";
	}
	$html.="<div class='partido-fecha'>".$row['fecha'];
	
	$html.="</div>";
	$html.="<div class='partido-equipo'>".$local."</div>";
	$html.="<div class='partido-goles'>".$row['goles_local']."</div>";
	$html.="<div class='partido-goles'>".$row['goles_visitante']."</div>";
	$html.="<div class='partido-equipo'>".$visitante."</div>";
	$html.="</div>";
}
echo $html;


?>