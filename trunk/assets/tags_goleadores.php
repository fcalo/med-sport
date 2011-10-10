<?
//Textos propios del deporte

if(isset($_GET['t']) || isset($_GET['tt'])){
}else{	


	$sql="select d.texto_goleadores ";
	$sql.=" from t_deportes d ";
	$sql.=" join t_equipos e on d.id_deporte=e.id_deporte";
	$sql.=" where e.id_equipo=".$idEquipo;
	$rsTextos=$db->get_results($sql,ARRAY_A);
	if($rsTextos){
		$goleadores=utf8_encode($rsTextos[0]['texto_goleadores']);
	}
	?>

	<h3> <?=$goleadores?> </h3>
	<div id="tags-goleadores">
	<ul>
	<?


        if($idTemporada!=""){
            $sql="select t_plantilla.id_plantilla, nombre, goles";
            $sql.=" from t_plantilla left join";
            $sql.=" (select id_plantilla, sum(goles) goles";
            $sql.=" from t_partidos_plantilla pp, t_partidos p, t_torneos t";
            $sql.=" where t.id_equipo=".$idEquipo;
            $sql.=" and t.temporada=".$idTemporada;
            $sql.=" and p.id_torneo=t.id_torneo";
            $sql.=" and pp.id_partido=p.id_partido";
            $sql.=" group by id_plantilla ) t on t_plantilla.id_plantilla=t.id_plantilla";
            $sql.=" where temporada=".$idTemporada." and id_equipo=".$idEquipo;
            $sql.=" and goles>0 order by nombre";
            $rs=$db->get_results($sql,ARRAY_A);

            $count=sizeof($rs);
            for($i=0;$i<$count;$i++){
                    $totalGoles+=$rs[$i]['goles'];
            }
            for($i=0;$i<$count;$i++){
                    $row=$rs[$i];
                    /*size 0.7->2.5
                    weight 100->900*/
                    $goles=$row['goles'];
                    if($goles!=$totalGoles){
                            $size=(($goles*1.8)/(0.75*$totalGoles))+0.7;
                            $weight=(($goles*800)/(0.75*$totalGoles))+100;
                    }
                    echo '<li><a href="javascript:verJugador(\''.$row['id_plantilla'].'\')" style="font-size:'.$size.'em;font-weight:'.$weight.'">'.utf8_encode($row['nombre']).'<span style="color:#555;">('.$goles.')</span></a> </li>';
            }
        }
            ?>
	</ul>
	</div>
	<div style="clear:both"></div>
<?}?>