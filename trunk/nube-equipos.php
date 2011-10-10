<?include("top.php")

?>
<div id="bd">
	<div class="ult-equipos" style="width:100%">
		<div class="ult-equipos-titulo"><h3>Nube de equipos</h3></div>
		<div class="ult-equipos-bd tags"><ul>
			<?
			$sql="select e.nom_equipo, d.url_deporte, m.miembros, p.partidos, f.fotos, v.videos, ";
			$sql.="(coalesce(m.miembros,0)+(coalesce(p.partidos,0)*2)+(coalesce(f.fotos,0)*4)+(coalesce(v.videos,0)*8)) comb ";
 			$sql.="from t_login l, t_deportes d, t_equipos e left join ";
 			$sql.="(select count(*) miembros, id_equipo from t_plantilla group by id_equipo) m on m.id_equipo=e.id_equipo ";
 			$sql.="left join (select count(*) partidos, id_equipo from t_partidos group by id_equipo) p on p.id_equipo=e.id_equipo ";
 			$sql.="left join (select count(*) fotos, user from t_imagenes group by user) f on f.user=e.user ";
 			$sql.="left join (select count(*) videos, user from t_videos group by user) v on v.user=e.user ";
 			$sql.="where e.user=l.user ";
 			$sql.="and d.id_deporte=e.id_deporte ";
 			$sql.="and fec_activacion is not null ";
 			$sql.="and e.user!='miequipodeportivo@miequipodeportivo.com' ";
 			$sql.="order by nom_equipo;";
			
			$rs=$db->get_results($sql,ARRAY_A);
			$count=sizeof($rs);
			
			$count=sizeof($rs);
			
			for($i=0;$i<$count;$i++){
				$total+=$rs[$i]['comb'];
			}
			for($i=0;$i<$count;$i++){
				$row=$rs[$i];
				$link=getServer()."/deporte/".$row['url_deporte']."/".urls_amigables($row['nom_equipo']);
				
				/*size 0.7->2.5
				weight 100->900*/
				$val=$row['comb'];
				if($val!=$total){
					$size=(($val*1.8)/(0.75*$total))+0.7;
					$weight=(($val*800)/(0.75*$total))+100;
				}
				echo '<li><a href="'.$link.'" style="font-size:'.$size.'em;font-weight:'.$weight.'">'.utf8_encode($row['nom_equipo']).'</a></li>';
			}?>
		</ul></div>
	</div>
</div>
<?include("bottom.php")?>

