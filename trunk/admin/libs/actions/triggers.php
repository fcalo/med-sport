<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function generaClasificacion($jornada, $torneo, $db){
    //Obtiene los puntos

    $sql="select t.user, coalesce(t.puntos_victoria,d.puntos_victoria) puntos_victoria, ";
    $sql.="coalesce(t.puntos_empate,d.puntos_empate) puntos_empate,";
    $sql.="coalesce(t.puntos_derrota,d.puntos_derrota) puntos_derrota";
    $sql.=" FROM t_torneos t";
    $sql.=" INNER JOIN t_equipos e ON e.id_equipo=t.id_equipo ";
    $sql.=" INNER JOIN t_deportes d ON d.id_deporte=e.id_deporte ";
    $row=$db->get_row($sql,ARRAY_A);

    $puntosVictoria=$row['puntos_victoria'];
    $puntosEmpate=$row['puntos_empate'];
    $puntosDerrota=$row['puntos_derrota'];
    $user=$row['user'];

    //Obtener idClasificacion
    $sql="SELECT id_clasificacion ";
    $sql.=" FROM t_clasificaciones";
    $sql.=" WHERE id_torneo=".$torneo;
    $sql.=" and jornada=".$jornada;
    $row=$db->get_row($sql,ARRAY_A);
    if($row){
        $idClasificacion=$row['id_clasificacion'];
    }else{
        $sql=" select max(fecha) fecha from(";
        $sql.="        select fecha from t_otros_partidos where jornada=".$jornada." and id_torneo=".$torneo;
        $sql.="        union all";
        $sql.="        select fecha from t_partidos where jornada=".$jornada." and id_torneo=".$torneo;
        $sql.=") t";
        $row=$db->get_row($sql,ARRAY_A);


	$sql=" insert into t_clasificaciones (id_torneo, fecha, jornada, user) ";
	$sql.=" values (".$torneo.", '".$row['fecha']."', ".$jornada.", '".$user."');";
        $db->query($sql);
        $idClasificacion=$db->insert_id;

    }

    $sql="DELETE FROM t_clasificaciones_tabla where id_clasificacion=".$idClasificacion;
    $db->query($sql);


    $sql=" INSERT INTO t_clasificaciones_tabla ";
    $sql.=" (id_torneos_equipos, jugados, ganados, empatados, perdidos, favor, contra, puntos, user, id_clasificacion) ";
    $sql.=" select equipo, sum(jugado) jugados, sum(ganados) ganados,";
    $sql.=" sum(empatados) empatados,sum(perdidos) perdidos, sum(gf) gf, sum(gc) gc, sum(puntos), '".$user."' user , ".$idClasificacion." id_clasificacion from ";
    $sql.="(";
    $sql.="        select p.local equipo, 1 jugado, goles_local gf, goles_visitante gc,";
    $sql.="        if(goles_local>goles_visitante,1,0) ganados,";
    $sql.="        if(goles_local=goles_visitante,1,0) empatados,";
    $sql.="        if(goles_local<goles_visitante,1,0) perdidos,";
    $sql.="        if(goles_local>goles_visitante,".$puntosVictoria.",if(goles_local<goles_visitante,".$puntosDerrota.",".$puntosEmpate.")) puntos";
    $sql.="        from t_otros_partidos p";
    $sql.="        where p.id_torneo=".$torneo." and p.jornada<=".$jornada;
    $sql.="        union all";
    $sql.="        select p.visitante equipo,1 jugado, goles_visitante gf, goles_local gc,";
    $sql.="        if(goles_local<goles_visitante,1,0) ganados,";
    $sql.="        if(goles_local=goles_visitante,1,0) empatados,";
    $sql.="        if(goles_local>goles_visitante,1,0) perdidos, if(goles_local>goles_visitante,".$puntosDerrota.",if(goles_local<goles_visitante,".$puntosVictoria.",".$puntosEmpate.")) puntos";
    $sql.="        from t_otros_partidos p";
    $sql.="        where p.id_torneo=".$torneo;
    $sql.="        and p.jornada<=".$jornada;
    $sql.="        union all";
    $sql.="        select 0 equipo ,1 jugado, goles_mios gf, goles_rival gc,";
    $sql.="        if(goles_mios>goles_rival,1,0) ganados,";
    $sql.="        if(goles_mios=goles_rival,1,0) empatados,";
    $sql.="        if(goles_mios<goles_rival,1,0) perdidos, if(goles_mios>goles_rival,".$puntosVictoria.",if(goles_mios<goles_rival,".$puntosDerrota.",".$puntosEmpate.")) puntos";
    $sql.="        from t_partidos join t_equipos on t_equipos.id_equipo=t_partidos.id_equipo";
    $sql.="        where (ind_jugado='S' or goles_mios>0 or goles_rival>0)";
    $sql.="        and id_torneo=".$torneo." and jornada<=".$jornada;
    $sql.="        union all";
    $sql.="        select p.rival equipo ,1 jugado, goles_rival gf, goles_mios gc,";
    $sql.="        if(goles_mios<goles_rival,1,0) ganados,";
    $sql.="        if(goles_mios=goles_rival,1,0) empatados,";
    $sql.="        if(goles_mios>goles_rival,1,0) perdidos,if(goles_mios>goles_rival,".$puntosDerrota.",if(goles_mios<goles_rival,".$puntosVictoria.",".$puntosEmpate.")) puntos";
    $sql.="        from t_partidos p join t_equipos on t_equipos.id_equipo=p.id_equipo";
    $sql.="        where (ind_jugado='S' or goles_mios>0 or goles_rival>0)";
    $sql.="        and p.id_torneo=".$torneo." and p.jornada<=".$jornada;
    $sql.="    ) t";
    $sql.="    group by equipo";
    $sql.="    order by 9 desc,8 desc, 6 desc;";

    $db->query($sql);

    //Posiciones
    $sql="select @rownum:=coalesce(@rownum,0)+1 rk, t.* from ";
    $sql.=" (select id_clasificaciones_table idct, t.puntos ,  t.contra , t.favor";
    $sql.=" from (SELECT @rownum:=0) r, ";
    $sql.=" t_clasificaciones_tabla t join t_clasificaciones c on c.id_clasificacion=t.id_clasificacion ";
    $sql.=" where c.id_torneo=".$torneo." and c.jornada=".$jornada.") t";
    $sql.=" order by puntos desc, puntos, favor-contra desc, favor desc";

    $rs=$db->get_results($sql,ARRAY_A);
    foreach($rs as $row){
        $sql=" update t_clasificaciones_tabla ";
        $sql.=" set posicion=".$row['rk'];
        $sql.=" where id_clasificaciones_table=".$row['idct'];
        $db->query($sql);
    }

}

function amigablesMySql($campo)
{

	return " replace(replace( replace( replace( replace( replace( replace( replace( replace( replace( replace( replace( replace(replace(replace(lower(".utf8_encode($campo)."),'  ',' '),'  ',' '),'+ ',''),' ','-'),'<br>',''),'(',''),')',''),',',''),'.',''),'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),'ñ','n')";
}

function trigger($idEntidad, $key, $db){
    switch ($idEntidad){
            case 10:
                //Equipos
                $db->query("call generateUrlEquipo(".$key.")");

                //Comprueba que no haya mas equipos con la misma url
                $first=true;
                do{
                    if(!$first){
                        $db->query("call generateUrlEquipo(".$key.")");
                        $sql="UPDATE t_equipos set url_equipo=concat(url_equipo,'_','".rand(1, 99)."')";
                        $sql.=" WHERE id_equipo=".$key;
                        $db->query($sql);

                    }
                    $first=false;

                    $sql="SELECT count(*) c FROM t_equipos";
                    $sql.=" WHERE url_equipo=( SELECT url_equipo FROM t_equipos WHERE id_equipo=".$key.")";
                    $row=$db->get_row($sql,ARRAY_A);
                }while($row['c']>1);
                break;
            case 11:
                //Torneos
                //Puntos victoria
                $sql="update t_torneos t ";
                $sql.=" INNER JOIN t_equipos e ON e.id_equipo=t.id_equipo";
                $sql.=" INNER JOIN t_deportes d ON d.id_deporte=e.id_deporte";
                $sql.=" SET t.puntos_victoria=d.puntos_victoria ";
                $sql.=" WHERE t.puntos_victoria is null ";
                $sql.=" OR t.puntos_victoria=0";
                $db->query($sql);

                //Puntos empate
                $sql="update t_torneos t ";
                $sql.=" INNER JOIN t_equipos e ON e.id_equipo=t.id_equipo";
                $sql.=" INNER JOIN t_deportes d ON d.id_deporte=e.id_deporte";
                $sql.=" SET t.puntos_empate=d.puntos_empate ";
                $sql.=" WHERE t.puntos_empate is null ";
                $sql.=" OR t.puntos_empate=0";
                $db->query($sql);

                //Puntos derrota
                $sql="update t_torneos t ";
                $sql.=" INNER JOIN t_equipos e ON e.id_equipo=t.id_equipo";
                $sql.=" INNER JOIN t_deportes d ON d.id_deporte=e.id_deporte";
                $sql.=" SET t.puntos_derrota=d.puntos_derrota ";
                $sql.=" WHERE t.puntos_derrota is null ";
                $sql.=" OR t.puntos_derrota=0";
                $db->query($sql);
                
                break;
            case 13:
                //Partidos
                $sql="SELECT ind_jugado, goles_mios, goles_rival, jornada, id_torneo, user ";
                $sql.=" FROM t_partidos ";
                $sql.=" WHERE id_partido=".$key;
                $row=$db->get_row($sql,ARRAY_A);
                
                if ($row['ind_jugado']=='S' || intval($row['goles_mios'])>0 || intval($row['goles_rival'])>0)
                    generaClasificacion($row['jornada'], $row['id_torneo'], $db);


            case 14:
                //Otros partidos
                //TODO: generar clasificacion
                //Partidos
                $sql="SELECT jornada, id_torneo, user ";
                $sql.=" FROM t_otros_partidos ";
                $sql.=" WHERE id_partido=".$key;
                $row=$db->get_row($sql,ARRAY_A);
                generaClasificacion($row['jornada'], $row['id_torneo'], $db);
                //$db->query("call generaClasificacion(".$row['jornada'].",".$row['id_torneo'].",'".$row['user']."'");
            case 102:
                break;
    }
    
}

?>
