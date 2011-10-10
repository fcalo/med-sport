<?

$host    = "mysql5-20.perso"; // Host name
$db_name = "miequipomed";		// Database name
$db_user = "miequipomed";		// Database user name
$db_pass = "m3dm3d10";			// Database Password
$server="http://www.miequipodeportivo.com";

$server="http://".$_SERVER['HTTP_HOST'];
if(strpos($server,".dev")>0){
	$host    = "localhost"; // Host name
	$db_name = "127.0.0.1";		// Database name
	$db_user = "root";		// Database user name
	$db_pass = "r00t";			// Database Password
	$server="http://med.dev";
}else{
	$host    = "mysql5-20.perso"; // Host name
	$db_name = "miequipomed";		// Database name
	$db_user = "miequipomed";		// Database user name
	$db_pass = "m3dm3d10";			// Database Password
	$server="http://www.miequipodeportivo.com";

}

$conn = mysql_connect($host,$db_user,$db_pass)or die(mysql_error());
mysql_select_db($db_name,$conn)or die(mysql_error());


function error($msg){
    include("admin/libs/util/mmail.php");
    //manda un mail y para la ejecucion
    sendMail("fernando.calo.sanchez@gmail.com","error en el cron",$msg);
    die($msg);
}



//avisa de los partidos que no han sido avisados ya, quedan menos de 5 dias y tiene hora y lugar
$sql="select p.id_partido, date_format(p.fecha,'%e/%c/%Y') fecha, p.hora, p.lugar, e.nom_equipo rival from t_partidos p";
$sql.=" inner join t_torneos_equipos e on  e.id_torneo=p.id_torneo and e.id_torneos_equipos=p.rival ";
$sql.=" where p.avisado!='S'";
$sql.=" and p.lugar!=''";
$sql.=" and p.lugar is not null";
$sql.=" and p.hora is not null";
$sql.=" and p.hora!=''";
$sql.=" and curdate()+5>=p.fecha+0";
$sql.=" and p.fecha>curdate();";


$resultSet = mysql_query($sql);
while($row = mysql_fetch_array($resultSet)){

	echo "partido a avisar ".$row['id_partido']."<br>";
	
	$sql="select t_torneos_equipos.nom_equipo rival, url_deporte, url_equipo ";
	$sql.=" from t_partidos inner join t_equipos on t_partidos.id_equipo=t_equipos.id_equipo";
	$sql.=" inner join t_deportes on t_equipos.id_deporte=t_deportes.id_deporte";
	$sql.=" inner join t_torneos_equipos on t_partidos.rival=t_torneos_equipos.id_torneos_equipos";
	$sql.=" where id_partido=".$row['id_partido'];
	$rowLinks=mysql_query($sql);
	$rowLink=mysql_fetch_array($rowLinks);
	
	//Para cada jugador confirmado del equipo genera un aviso
	$sql="select s.email, l.id_plantilla ";
	$sql.=" from t_solicitudes s inner join t_plantilla l on l.id_plantilla=s.id_plantilla";
	$sql.=" inner join t_partidos p on p.id_equipo=l.id_equipo";
	$sql.=" inner join t_torneos t on t.id_torneo=p.id_torneo and t.temporada=l.temporada";
	$sql.=" where p.id_partido=".$row['id_partido'];
	$sql.=" and ind_asistencia='S'";
	$sql.=" and fec_confirmacion is not null";
	$jugadores = mysql_query($sql);
	while($jugador = mysql_fetch_array($jugadores)){
	
		$asunto=" Partido contra ".utf8_encode($row['rival']);
		
		echo $asunto."<br>";
				
		
		$link=$server."/partido/".$row['id_partido'];
		$linkOk=$server."/asistencia.php?a=".sha1($row['id_partido'])."&b=".sha1($jugador['id_plantilla'].$row['id_partido'])."&c=a1";
		$linkKo=$server."/asistencia.php?a=".sha1($row['id_partido'])."&b=".sha1($jugador['id_plantilla'].$row['id_partido'])."&d=a1";
		
		$cuerpo="<div>";
		$cuerpo.="<div>".$asunto."</div>";
		$cuerpo.="<div>Tienes partido contra ".$row['rival']." el dia ".$row['fecha']." a las ".$row['hora']." en ".$row['lugar']."</div>";
		$cuerpo.="<div>Si vas a asistir al encuentro sigue este enlace <a href='".$linkOk."'>".$linkOk."</a>.</div>";
		$cuerpo.="<div>Si no vas a poder asistir al encuentro, avisa a tus compa&ntilde;eros siguiendo este enlace <a href='".$linkKo."'>".$linkKo."</a>.</div>";
		$cuerpo.="<div>&nbsp;</div>";
		$cuerpo.="<div>puedes ver la previa del partido en <a href='".$link."'>".$link."</a>.</div>";
		$cuerpo.="<div>&nbsp;</div>";
		$cuerpo.="<div>Gracias</div>";
		$cuerpo.="<div><a href='".$server."'>Mi equipo deportivo</a></div>";
		$cuerpo.="</div>";
		
		$sql="insert into t_envios (para, asunto, cuerpo) values ('".$jugador['email']."','".$asunto."','".str_replace("'","\'",$cuerpo)."')";
		mysql_query($sql);
	}
	
	mysql_query("update t_partidos set avisado='S' where id_partido=".$row['id_partido']);

}
//Tareas

$loops=1;
$sql="select count(*) c from t_tareas";
$rC = mysql_query($sql);
if($r = mysql_fetch_array($rC)){
    $loops=$r['c']/25;
}
if($loops>20)
    $loops=20;
if($loops==0)
    echo "Nada que hacer";
if($loops<1)
    $loops=1;

for($loop=0;$loop<$loops;$loop++){
    $sql="select id_tarea, tipo_tarea, valor_tarea, estado_tarea from t_tareas order by id_tarea limit 1";
    $resultSet = mysql_query($sql);
    if($row = mysql_fetch_array($resultSet)){
        $idTarea=$row['id_tarea'];
        $tipo=$row['tipo_tarea'];
        $valor=$row['valor_tarea'];
        $estado=$row['estado_tarea'];
        $aVal=explode("|",$valor);
        switch($tipo){
            case 1:
                $torneo=$aVal[0];
                $mail=$aVal[1];
                $equipo=$aVal[2];
                $newIdTorneo=$aVal[3];

                //Equipo a copiar a otro torneo
                $oldIdEquipo=0;
                $findEquipo=$equipo;
                $binicio=true;
                $loops=0;
                while($oldIdEquipo==0 && $loops<10){
                    $sql="select id_torneos_equipos id_equipo, nom_equipo ";
                    $sql.=" from t_torneos_equipos te inner join t_torneos t on te.user=t.user";
                    $sql.=" where t.id_torneo=".$torneo;
                    $sql.=" and nom_equipo like '%$findEquipo%'";
                    $rs = mysql_query($sql);
                    if($rw = mysql_fetch_array($rs)){
                        $oldIdEquipo=0;
                        if(sizeof($rs)>0){
                            $oldIdEquipo=$rw['id_equipo'];
                            $nomEquipoOld=$rw['nom_equipo'];
                        }

                    }
                    if($oldIdEquipo==0)
                        if($binicio)
                            $findEquipo=substr($findEquipo,1);
                        else
                            $findEquipo=substr($findEquipo,0,strlen($findEquipo)-1);

                    $binicio=!$binicio;
                    $loops++;
                }

                $sql="update t_equipos set nom_equipo='".$nomEquipoOld."', id_torneo_origen=$torneo where user='".$mail."'";
                if (!mysql_query($sql))
                    error($sql."--".mysql_error());

                $sql="insert into t_tareas(tipo_tarea, valor_tarea, estado_tarea)";
                $sql.=" select 2 tipo_tarea, concat('$valor','|".$oldIdEquipo."','|',op.id_partido) valor_tarea, 1 estado_tarea";
                $sql.=" from t_otros_partidos op ";
                $sql.=" inner join v_equipos_torneo el1 on op.local=el1.id_torneos_equipos";
                $sql.=" inner join v_equipos_torneo ev1 on op.visitante=ev1.id_torneos_equipos";
                $sql.=" , v_equipos_torneo el2 ";
                $sql.=" , v_equipos_torneo ev2 ";
                $sql.=" where op.id_torneo=".$torneo;
                $sql.=" and  el1.id_torneo=".$torneo;
                $sql.=" and el2.id_torneo=".$newIdTorneo;
                $sql.=" and el2.nom_equipo like el1.nom_equipo";
                $sql.=" and ev1.id_torneo=".$torneo;
                $sql.=" and ev2.id_torneo=".$newIdTorneo;
                $sql.=" and ev2.nom_equipo like ev1.nom_equipo";
                $sql.=" and local!=".$oldIdEquipo;
                $sql.=" and visitante!=".$oldIdEquipo;
                if (!mysql_query($sql))
                    error($sql."--".mysql_error());

                $sql="insert into t_tareas(tipo_tarea, valor_tarea, estado_tarea)";
                $sql.=" select 2 tipo_tarea, concat('$valor','|".$oldIdEquipo."','|',p.id_partido) valor_tarea, 2 estado_tarea";
                $sql.=" from t_partidos p ";
                $sql.=" inner join v_equipos_torneo ev1 on p.rival=ev1.id_torneos_equipos";
                $sql.=" , v_equipos_torneo el2 ";
                $sql.=" , v_equipos_torneo ev2 ";
                $sql.=" , v_equipos_torneo el1 ";
                $sql.=" where p.id_torneo=".$torneo;
                $sql.=" and  el1.id_torneo=".$torneo;
                $sql.=" and el1.id_torneos_equipos=0" ;
                $sql.=" and el2.id_torneo=".$newIdTorneo;
                $sql.=" and el2.nom_equipo like el1.nom_equipo";
                $sql.=" and ev1.id_torneo=".$torneo;
                $sql.=" and ev2.id_torneo=".$newIdTorneo;
                $sql.=" and ev2.nom_equipo like ev1.nom_equipo";
                $sql.=" and rival!=".$oldIdEquipo;
                if (!mysql_query($sql))
                    error($sql."---".mysql_error());

                $sql="insert into t_tareas(tipo_tarea, valor_tarea, estado_tarea)";
                $sql.=" select 3 tipo_tarea, concat('$valor','|".$oldIdEquipo."','|',op.id_partido) valor_tarea, 1 estado_tarea";
                $sql.=" from t_otros_partidos op ";
                $sql.=" inner join v_equipos_torneo el1 on op.local=el1.id_torneos_equipos";
                $sql.=" inner join v_equipos_torneo ev1 on op.visitante=ev1.id_torneos_equipos";
                $sql.=" where op.id_torneo=".$torneo;
                $sql.=" and  el1.id_torneo=".$torneo;
                $sql.=" and ev1.id_torneo=".$torneo;
                $sql.=" and (local=".$oldIdEquipo;
                $sql.=" or visitante=".$oldIdEquipo.")";
                if (!mysql_query($sql))
                    error($sql."--".mysql_error());


                $sql="insert into t_tareas(tipo_tarea, valor_tarea, estado_tarea)";
                $sql.=" select 3 tipo_tarea, concat('$valor','|".$oldIdEquipo."','|',p.id_partido) valor_tarea, 2 estado_tarea";
                $sql.=" from t_partidos p ";
                $sql.=" inner join v_equipos_torneo ev1 on p.rival=ev1.id_torneos_equipos";
                $sql.=" , v_equipos_torneo el1 ";
                $sql.=" where p.id_torneo=".$torneo;
                $sql.=" and  el1.id_torneo=".$torneo;
                $sql.=" and el1.id_torneos_equipos=0";
                $sql.=" and ev1.id_torneo=".$torneo;
                $sql.=" and rival=".$oldIdEquipo;
                if (!mysql_query($sql))
                    error($sql."---".mysql_error());


                $sql="insert into t_tareas(tipo_tarea, valor_tarea, estado_tarea)";
                $sql.=" values (4, concat('$valor','|".$oldIdEquipo."'), 1)";
                if (!mysql_query($sql))
                    error($sql."---".mysql_error());
                break;
            case 2:
                //copiar t_otro_partido
                $torneo=$aVal[0];
                $mail=$aVal[1];
                $equipo=$aVal[2];
                $newIdTorneo=$aVal[3];
                $oldIdEquipo=$aVal[4];
                $idPartido=$aVal[5];
                if($estado==1){
                    $sql="insert into t_otros_partidos (id_torneo, fecha, jornada, local, visitante, goles_local, goles_visitante, user) ";
                    $sql.=" select $newIdTorneo, op.fecha, op.jornada, el2.id_torneos_equipos, ev2.id_torneos_equipos, op.goles_local, op.goles_visitante, '".$mail."'";
                    $sql.=" from t_otros_partidos op ";
                    $sql.=" inner join v_equipos_torneo el1 on op.local=el1.id_torneos_equipos";
                    $sql.=" inner join v_equipos_torneo ev1 on op.visitante=ev1.id_torneos_equipos";
                    $sql.=" , v_equipos_torneo el2 ";
                    $sql.=" , v_equipos_torneo ev2 ";
                    $sql.=" where op.id_torneo=".$torneo;
                    $sql.=" and op.id_partido=".$idPartido;
                    $sql.=" and  el1.id_torneo=".$torneo;
                    $sql.=" and el2.id_torneo=".$newIdTorneo;
                    $sql.=" and el2.nom_equipo like el1.nom_equipo";
                    $sql.=" and ev1.id_torneo=".$torneo;
                    $sql.=" and ev2.id_torneo=".$newIdTorneo;
                    $sql.=" and ev2.nom_equipo like ev1.nom_equipo";
                    $sql.=" and local!=".$oldIdEquipo;
                    $sql.=" and visitante!=".$oldIdEquipo;
                }else{
                    $sql="insert into t_otros_partidos (id_torneo, fecha, jornada, local, visitante, goles_local, goles_visitante, user) ";
                    $sql.=" select $newIdTorneo, p.fecha, p.jornada, ";
                    $sql.=" if(p.ind_visitante='N',el2.id_torneos_equipos, ev2.id_torneos_equipos) , ";
                    $sql.=" if(p.ind_visitante='N',ev2.id_torneos_equipos, el2.id_torneos_equipos) , ";
                    $sql.=" if(p.ind_visitante='N',p.goles_mios, p.goles_rival) goles_local, ";
                    $sql.=" if(p.ind_visitante='S',p.goles_mios, p.goles_rival) goles_visitante, ";
                    $sql.="'".$mail."'";
                    $sql.=" from t_partidos p ";
                    $sql.=" inner join v_equipos_torneo ev1 on p.rival=ev1.id_torneos_equipos";
                    $sql.=" , v_equipos_torneo el2 ";
                    $sql.=" , v_equipos_torneo ev2 ";
                    $sql.=" , v_equipos_torneo el1 ";
                    $sql.=" where p.id_torneo=".$torneo;
                    $sql.=" and p.id_partido=".$idPartido;
                    $sql.=" and  el1.id_torneo=".$torneo;
                    $sql.=" and  el1.id_torneos_equipos=0";
                    $sql.=" and el2.id_torneo=".$newIdTorneo;
                    $sql.=" and el2.nom_equipo like el1.nom_equipo";
                    $sql.=" and ev1.id_torneo=".$torneo;
                    $sql.=" and ev2.id_torneo=".$newIdTorneo;
                    $sql.=" and ev2.nom_equipo like ev1.nom_equipo";
                    $sql.=" and rival!=".$oldIdEquipo;
                }
                if($sql!="")
                    if (!mysql_query($sql))
                        error($sql."---".mysql_error());
                break;
            case 3:
                //copiar t_partido
                $torneo=$aVal[0];
                $mail=$aVal[1];
                $equipo=$aVal[2];
                $newIdTorneo=$aVal[3];
                $oldIdEquipo=$aVal[4];
                $idPartido=$aVal[5];
                if($estado==1){
                    $sql=" insert into t_partidos (id_equipo, id_torneo, fecha, jornada, ind_visitante, rival, goles_mios, goles_rival, user, hora, lugar) ";
                    $sql.=" select (select id_equipo from t_equipos where user='".$mail."') id_equipo, ";
                    $sql.=" $newIdTorneo, op.fecha, op.jornada, ";
                    $sql.=" if(local=".$oldIdEquipo.",'N', 'S') ind_visitante,";
                    $sql.=" if(local=".$oldIdEquipo.",ev2.id_torneos_equipos, el2.id_torneos_equipos) rival,";
                    $sql.=" if(local=".$oldIdEquipo.",op.goles_local, op.goles_visitante) goles_mios, ";
                    $sql.=" if(visitante=".$oldIdEquipo.",op.goles_local, op.goles_visitante) goles_mios, ";
                    $sql.="'".$mail."','-' hora, '-' lugar";
                    $sql.=" from t_otros_partidos op ";
                    $sql.=" inner join v_equipos_torneo el1 on op.local=el1.id_torneos_equipos";
                    $sql.=" inner join v_equipos_torneo ev1 on op.visitante=ev1.id_torneos_equipos";
                    $sql.=" , v_equipos_torneo el2 ";
                    $sql.=" , v_equipos_torneo ev2 ";
                    $sql.=" where op.id_torneo=".$torneo;
                    $sql.=" and  op.id_partido=".$idPartido;
                    $sql.=" and  el1.id_torneo=".$torneo;
                    $sql.=" and el2.id_torneo=".$newIdTorneo;
                    $sql.=" and el2.nom_equipo like el1.nom_equipo";
                    $sql.=" and ev1.id_torneo=".$torneo;
                    $sql.=" and ev2.id_torneo=".$newIdTorneo;
                    $sql.=" and ev2.nom_equipo like ev1.nom_equipo";
                    $sql.=" and (local=".$oldIdEquipo;
                    $sql.=" or visitante=".$oldIdEquipo.")";

                }else{
                    $sql="insert into t_partidos (id_equipo, id_torneo, fecha, jornada, ind_visitante, rival, goles_mios, goles_rival, user, lugar, hora) ";
                    $sql.=" select (select id_equipo from t_equipos where user='".$mail."') id_equipo, ";
                    $sql.="$newIdTorneo, p.fecha, p.jornada, ";
                    $sql.=" if(p.ind_visitante='S','N', 'S') ind_visitante,";
                    $sql.=" el2.id_torneos_equipos rival,";
                    $sql.=" p.goles_rival goles_mios, ";
                    $sql.=" p.goles_mios goles_rival, ";
                    $sql.="'".$mail."', p.lugar, p.hora";
                    $sql.=" from t_partidos p ";
                    $sql.=" inner join v_equipos_torneo ev1 on p.rival=ev1.id_torneos_equipos";
                    $sql.=" , v_equipos_torneo el2 ";
                    $sql.=" , v_equipos_torneo ev2 ";
                    $sql.=" , v_equipos_torneo el1 ";
                    $sql.=" where p.id_torneo=".$torneo;
                    $sql.=" and p.id_partido=".$idPartido;
                    $sql.=" and  el1.id_torneo=".$torneo;
                    $sql.=" and el1.id_torneos_equipos=0";
                    $sql.=" and el2.id_torneo=".$newIdTorneo;
                    $sql.=" and el2.nom_equipo like el1.nom_equipo";
                    $sql.=" and ev1.id_torneo=".$torneo;
                    $sql.=" and ev2.id_torneo=".$newIdTorneo;
                    $sql.=" and ev2.nom_equipo like ev1.nom_equipo";
                    $sql.=" and rival=".$oldIdEquipo;
                }
                if($sql!="")
                    if (!mysql_query($sql))
                        error($sql."---".mysql_error());
                break;
            case 4:
                //copiar clasificacion
                $torneo=$aVal[0];
                $mail=$aVal[1];
                $equipo=$aVal[2];
                $newIdTorneo=$aVal[3];
                $oldIdEquipo=$aVal[4];
                $idPartido=$aVal[5];
                //t_clasificaciones

                $sql="delete from t_clasificaciones where user='".$mail."'";
                if (!mysql_query($sql))
                    error($sql."---".mysql_error());

                $sql="delete from t_clasificaciones_tabla where user='".$mail."'";
                    if (!mysql_query($sql))
                        error($sql."---".mysql_error());

                $sql="select distinct jornada from t_clasificaciones ";
                $sql.=" where id_torneo='".$torneo."'";
                $resultSet = mysql_query($sql);
                while($row = mysql_fetch_array($resultSet)){
                    $jornada=$row['jornada'];

                    //TODO:Hacer un insert por fecha y meter el id_clasificacion en la tabla hija
                    $sql="insert into t_clasificaciones (id_torneo, fecha, jornada, user)";
                    $sql.=" select $newIdTorneo id_torneo, fecha, jornada, '".$mail."' from t_clasificaciones ";
                    $sql.=" where id_torneo='".$torneo."'";
                    $sql.=" and jornada='".$jornada."'";
                    if (!mysql_query($sql))
                        error($sql."---".mysql_error());

                    $idClasificacion=mysql_insert_id();
                    //t_clasificaciones_tabla
                    $sql="insert into t_clasificaciones_tabla (posicion, id_torneos_equipos, puntos, jugados, ";
                    $sql.=" ganados, empatados,perdidos, favor, contra, id_clasificacion, user)";
                    $sql.=" select ct.posicion, et2.id_torneos_equipos, ct.puntos, ct.jugados, ";
                    $sql.=" ct.ganados, ct.empatados, ct.perdidos, ct.favor, ct.contra, ".$idClasificacion." id_clasificacion, '".$mail."' user";
                    $sql.=" from t_clasificaciones_tabla ct inner join t_clasificaciones c on c.id_clasificacion=ct.id_clasificacion ";
                    $sql.=" inner join v_equipos_torneo et1 on ct.id_torneos_equipos=et1.id_torneos_equipos";
                    $sql.=" , v_equipos_torneo et2 ";
                    $sql.=" where c.id_torneo=".$torneo;
                    $sql.=" and c.jornada='".$jornada."'";
                    $sql.=" and  et1.id_torneo=".$torneo;
                    $sql.=" and et2.id_torneo=".$newIdTorneo;
                    $sql.=" and et2.nom_equipo like et1.nom_equipo";
                    $sql.=" order by id_clasificacion;";

                    if (!mysql_query($sql))
                        error($sql."---".mysql_error());
                }


                break;
        }
        $sql="delete from t_tareas where id_tarea=".$idTarea;
        if (!mysql_query($sql))
            error($sql."---".mysql_error());
    }
}

?>