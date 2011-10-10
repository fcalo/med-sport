<?include("top.php")?>
<div id="bd">
	<div  id="box-blue" class="box-blue-index">
		<div id="register-box">
			<h2>Administra tu equipo</h2>
			<div class="login-ln"><div class="label">Email</div><input class="login-textarea" type="text" value="" id="mail" onblur="checkMail()" onkeypress="keypress(event)" ><div id="status_mail" class="status "></div></div>
			<div class="login-ln"><div class="label">Contrase&ntilde;a</div><input class="login-textarea" type="password" value="" id="passb" onblur="checkPass()" onkeypress="keypress(event)"><div id="status_passb" class="status "></div></div>
			<div class="login-ln"><div class="label">Repetir Contrase&ntilde;a</div><input class="login-textarea" type="password" value="" id="repass" onblur="checkRepass()" onkeypress="keypress(event)"><div id="status_repass" class="status "></div></div>
			<div class="login-ln"><div class="label">Deporte</div><select class="login-textarea" id="deporte" onblur="checkDeporte()" onkeypress="keypress(event)">
				<option value=""></option>
				<?
				if(isset($_POST['deporte'])){
					$sql="select distinct deporte from t_deportes where url_deporte='".$_POST['deporte']."'";
					$rs=$db->get_row($sql,ARRAY_A);
					$dep=$rs['deporte'];
				}



				$sql="select distinct deporte from t_deportes";
				$rs=$db->get_results($sql,ARRAY_A);
				$count=sizeof($rs);
				for($i=0;$i<$count;$i++){?>
				  <option value="<?=$rs[$i]['deporte']?>"><?=utf8_encode($rs[$i]['deporte'])?></option>
				<?}?>

			</select><div id="status_deporte" class="status "></div></div>
			<?
			if ($dep!="")
				echo "<script type='text/javascript'>document.getElementById('deporte').value='".$dep."'</script>";
			?>
			<div class="login-ln"><div class="label">Equipo</div><input class="login-textarea" type="text" value="<?=ucwords(str_replace("-"," ",$_POST['equipo']))?>" id="equipo" onblur="checkEquipo()" onkeypress="keypress(event)"><div id="status_equipo" class="status "></div></div>
                        <input type="hidden" name="torneo" id="torneo" value="<?=$_POST['torneo']?>">
			<div class="login-ok"><div id="buttoncontainer"></div></div>
			<div id="bar-loader"></div>

		</div>
		<div id="thanks-box">
			<h2>Registro realizado correctamente</h2>
			<ul>
				<li><div>Para empezar a gestionar tu equipo debes activar tu cuenta.</div></li>
				<li><div>Sigue las instrucciones que encontrar&aacute;s en el buzon de la cuenta de correo que nos has indicado. No olvides mirar en el correo no deseado, por si acaso ;)</div></li>
				<li><div>Gracias por registrar tu equipo.</div></li>
			</ul>
		</div>
	</div>
	<div class="ult-equipos">
		<div class="ult-equipos-titulo"><h3>Equipos m&aacute;s activos</h3></div>
		<div class="ult-equipos-bd">
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
 			$sql.="order by comb desc, fec_activacion desc limit 4;";

			$rs=$db->get_results($sql,ARRAY_A);
			$count=sizeof($rs);

			for($i=0;$i<$count;$i++){
				$row=$rs[$i];
				$link=getServer()."/deporte/".$row['url_deporte']."/".urls_amigables($row['nom_equipo']);
			?>
			  <div><a href="<?=$link?>"><?=utf8_encode($row['nom_equipo'])?></a></div>
			<?}?>
		</div>
	</div>

	<div class="ult-equipos">
		<div class="ult-equipos-titulo"><h3>&Uacute;ltimos equipos inscritos</h3></div>
		<div class="ult-equipos-bd">
			<?
			$sql="select e.nom_equipo, d.url_deporte";
			$sql.=" from t_equipos e, t_login l, t_deportes d";
			$sql.=" where e.user=l.user";
			$sql.=" and d.id_deporte=e.id_deporte";
			$sql.=" and fec_activacion is not null";
			$sql.=" and e.user!='miequipodeportivo@miequipodeportivo.com'";
			$sql.=" order by fec_activacion desc limit 4;";

			$rs=$db->get_results($sql,ARRAY_A);
			$count=sizeof($rs);

			for($i=0;$i<$count;$i++){
				$row=$rs[$i];
				$link=getServer()."/deporte/".$row['url_deporte']."/".urls_amigables($row['nom_equipo']);
			?>
			  <div><a href="<?=$link?>"><?=utf8_encode($row['nom_equipo'])?></a></div>

			<?}?>
			<div style="text-align:right;font-size:8px;padding-top:10px;"><a href="nube-equipos.php"><b>Todos</b></a></div>
		</div>
	</div>
	<div id="servicios">
		<div id="servicios-titulo"><h3><i>Mi equipo deportivo</i> te ofrece:</h3></div>
		<div id="servicios-bd">
			<ul>
				<li><a href='gestor-equipos.php' title="gestor equipos"> Gestor de equipos</a></li>
				<li><a href='gestor-torneos.php' title="gestor torneos">Gestor de torneos</a></li>
				<li><a href='gestor-plantilla.php' title="gestor plantilla">Gestor de plantilla</a></li>
				<li><a href='gestor-partidos.php' title="gestor partidos, resultados, cronicas, asistencia, comentarios">Gestor de partidos(resultados, cronicas, asistencia, comentarios)</a></li>
				<li><a href='gestor-resultados.php' title="todos los resultados">Gestor de resultados de la liga</a></li>
				<li><a href='gestor-clasificacion.php' title="gestor clasificaci&oacute;n">Gestor de clasificaciones</a></li>
				<li><a href='recordatorio-asistencia.php' title="recordatorio y confirmaci&oacute;n de asistencia para el proximo partido">Recordatorio y confirmaci&oacute;n de asistencia para los pr&oacute;ximos partidos</a></li>
				<li><a href='estadisticas.php' title="estadisticas">Estad&iacute;sticas de tu equipo</a></li>
				<li><a href='fotos-videos.php' title="sube las fotos de tu equipo">Fotos y videos de tu equipo</a></li>
			</ul>
		</div>
	</div>
</div>
<?include("bottom.php")?>