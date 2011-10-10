<?	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
	header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header( "Cache-Control: no-cache, must-revalidate" );
	header( "Pragma: no-cache" );
	header('Content-Type: text/html; charset=UTF-8');
	include("../admin/config/database.php");
	
	$name=$_POST['name'];
	$email=$_POST['email'];
	$id=$_POST['i'];
	$comment=$_POST['comment'];
	if($_POST['p']=="true"){
		//Comentario
		$check = $db->query("insert into t_partidos_comentarios(nombre,email,comentario,date,id_partido) values('$name','$email','$comment',now(),$id)");
		if($check){
			//envio correos
			//al administrador
			$sql="select t_torneos_equipos.nom_equipo rival, url_deporte, url_equipo ";
			$sql.=" from t_partidos inner join t_equipos on t_partidos.id_equipo=t_equipos.id_equipo";
			$sql.=" inner join t_deportes on t_equipos.id_deporte=t_deportes.id_deporte";
			$sql.=" inner join t_torneos_equipos on t_partidos.rival=t_torneos_equipos.id_torneos_equipos";
			$sql.=" where id_partido=".$id;
			$row=$db->get_row($sql,ARRAY_A);
			
			$asunto=$name." ha comentado el partido contra ".utf8_encode($row['rival']);
			
			$server="http://".$_SERVER['HTTP_HOST'];
			//$link=$server."/deporte/".$row['url_deporte']."/".$row['url_equipo']."#navbar=resultados";
			$link=$server."/partido/".$id;
			$cuerpo="<div>";
			$cuerpo.="<div>".$asunto."</div>";
			$cuerpo.="<div>puedes verlo en <a href='".$link."'>".$link."</a></div>";
			$cuerpo.="<div>&nbsp;</div>";
			$cuerpo.="<div>Gracias</div>";
			$cuerpo.="<div><a href='".$server."'>Mi equipo deportivo</a></div>";
			$cuerpo.="</div>";
			
			$sql="select user from t_partidos where id_partido=".$id;
			$row=$db->get_row($sql,ARRAY_A);
			if($row['user']!=$email){
				$sql="insert into t_envios (para, asunto, cuerpo) values ('".$row['user']."','".$asunto."','".str_replace("'","\'",$cuerpo)."')";
				$db->query($sql);
			}
			
			//A los jugadores que lo permiten
			$sql="select distinct t_solicitudes.email ";
			$sql.=" from t_solicitudes inner join t_plantilla on t_plantilla.id_plantilla=t_solicitudes.id_plantilla" ;
			$sql.=" inner join t_partidos on t_plantilla.id_equipo=t_partidos.id_equipo ";
			$sql.=" where id_partido=".$id;
			$sql.=" and fec_confirmacion is not null and t_solicitudes.email!='".$email."'";
			$sql.=" and ind_comentario='S'";
			$rs=$db->get_results($sql,ARRAY_A);
			$count=sizeof($rs);
			for($i=0;$i<$count;$i++){
				$row=$rs[$i];
				$sql="insert into t_envios (para, asunto, cuerpo) values ('".$row['email']."','".$asunto."','".str_replace("'","\'",$cuerpo)."')";
				$db->query($sql);
			}
			
		}
			
	}else{
		//Firma
		$check = $db->query("insert into t_equipos_visitas(nombre,email,comentario,date,id_equipo) values('$name','$email','$comment',now(),$id)");
		if($check){
			//envio correos
			//al administrador
			$sql="select rival, url_deporte, url_equipo ";
			$sql.=" from t_partidos inner join t_equipos on t_partidos.id_equipo=t_equipos.id_equipo";
			$sql.=" inner join t_deportes on t_equipos.id_deporte=t_deportes.id_deporte";
			$sql.=" where t_equipos.id_equipo=".$id;
			
			
			$row=$db->get_row($sql,ARRAY_A);
			
			$asunto=$name." ha firmado en el libro de visitas";
			
			$server="http://".$_SERVER['HTTP_HOST'];
			$link=$server."/deporte/".$row['url_deporte']."/".$row['url_equipo']."#navbar=visitas";
			$cuerpo="<div>";
			$cuerpo.="<div>".$asunto."</div>";
			$cuerpo.="<div>puedes verlo en <a href='".$link."'>".$link."</a></div>";
			$cuerpo.="<div>&nbsp;</div>";
			$cuerpo.="<div>Gracias</div>";
			$cuerpo.="<div><a href='".$server."'>Mi equipo deportivo</a></div>";
			$cuerpo.="</div>";
			
			$sql="select user from t_equipos where id_equipo=".$id;
			$row=$db->get_row($sql,ARRAY_A);
			if($row['user']!=$email){
				$sql="insert into t_envios (para, asunto, cuerpo) values ('".$row['user']."','".$asunto."','".str_replace("'","\'",$cuerpo)."')";
				$db->query($sql);
			}
			
			//A los jugadores que lo permiten
			$sql="select distinct t_solicitudes.email ";
			$sql.=" from t_solicitudes inner join t_plantilla on t_plantilla.id_plantilla=t_solicitudes.id_plantilla" ;
			$sql.=" inner join t_partidos on t_plantilla.id_equipo=t_partidos.id_equipo ";
			$sql.=" where t_partidos.id_equipo=".$id;
			$sql.=" and fec_confirmacion is not null and t_solicitudes.email!='".$email."'";
			$sql.=" and ind_firma='S'";
			$rs=$db->get_results($sql,ARRAY_A);
			$count=sizeof($rs);
			for($i=0;$i<$count;$i++){
				$row=$rs[$i];
				$sql="insert into t_envios (para, asunto, cuerpo) values ('".$row['email']."','".$asunto."','".str_replace("'","\'",$cuerpo)."')";
				$db->query($sql);
			}
			
		}
	}
	

	$date_added = date("j n Y, g:i a",time());

	if($check)
		echo $date_added;
	else
		echo "0";
?>	   
