<?
if (isset($_GET['e'])){
	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
	header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header( "Cache-Control: no-cache, must-revalidate" );
	header( "Pragma: no-cache" );
	header('Content-Type: text/html; charset=UTF-8');
	include("../admin/config/database.php");
	include("util.php");

	if($_GET['a']=="0"){
		echo utf8_encode("A&uacute;n no hay datos");
		//exit;
                $salir=true;
	}
	$idEquipo=$_GET['e'];
	$one=$_GET['p']==1;
}
else
	$one=true;

if(!$salir){
    //echo "Cargando los resultados del equipo ".$_GET['e']." para el torneo  ".$_GET['r']." en la jornada".$_GET['j'];
    $html="";

    $sql="select 'm' tipo, id_partido,  p.fecha, date_format(p.fecha,'%e/%c/%Y') fecha,  e.nom_equipo local, te.nom_equipo visitante,";
    $sql.=" p.goles_mios goles_local, p.goles_rival goles_visitante, hora, lugar, 0 id_local, p.rival id_visitante";
    $sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
    $sql.=" where e.id_equipo=p.id_equipo";
    $sql.=" and e.id_equipo=".$idEquipo;
    $sql.=" and ind_visitante='N'";
    $sql.=" and te.id_torneos_equipos=p.rival";
    $sql.=" and ind_jugado='N' and (coalesce(p.goles_mios,0)=0 and coalesce(p.goles_rival,0)=0)";
    $sql.=" and coalesce(concat(p.fecha,' ',p.hora),p.fecha)>=now()";
    $sql.=" union";
    $sql.=" select 'm' tipo, id_partido, p.fecha,  date_format(p.fecha,'%e/%c/%Y') fecha, te.nom_equipo local, e.nom_equipo visitante,";
    $sql.=" p.goles_rival goles_local, p.goles_mios goles_visitante, hora, lugar, p.rival id_local, 0 id_visitante";
    $sql.=" from t_partidos p, t_equipos e, t_torneos_equipos te";
    $sql.=" where e.id_equipo=p.id_equipo";
    $sql.=" and e.id_equipo=".$idEquipo;
    $sql.=" and ind_visitante='S'";
    $sql.=" and te.id_torneos_equipos=p.rival";
    $sql.=" and ind_jugado='N' and (coalesce(p.goles_mios,0)=0 and coalesce(p.goles_rival,0)=0)";
    $sql.=" and coalesce(concat(p.fecha,' ',p.hora),p.fecha)>=now()";
    $sql.=" order by 3 , 2 ";
    if ($one)
            $sql.=" limit 1";

    //echo $sql;

    $rs=$db->get_results($sql,ARRAY_A);
    $count=sizeof($rs);
    if($count==0){
            echo utf8_encode("A&uacute;n no hay datos");
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
            if($row['hora']!="" && $row['lugar']!=""){
                    $html.="<div style='float:right'><a href='javascript:verPrevia(".$row['id_partido'].")'rel='nofollow' ><img  alt='Desplegar previa' title='Desplegar previa' src='/img/desplegar.gif'></a></div>";
                    $html.="<div style='float:right'><a href='/partido/".$row['id_partido']."' target='_blank' rel='nofollow' ><img src='/img/maximizar.gif' alt='Abrir partido en ventana nueva' title='Abrir partido en ventana nueva'></a></div>";
            }
            $html.="<div class='partido-fecha'>".$row['fecha']." ".$row['hora']." ".utf8_encode($row['lugar']);
            $html.="</div>";
            $html.="<div class='partido-equipo'>".$local."</div>";
            $html.="<div class='partido-goles'>-</div>";
            $html.="<div class='partido-goles'>-</div>";
            $html.="<div class='partido-equipo'>".$visitante."</div>";
            $html.="</div>";
    }
    echo $html;
}

?>