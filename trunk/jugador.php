<?
include("top.php");



$sql="select p.nombre, p.puesto, p.numero, p.imagen, p.id_plantilla,p.imagen,";
$sql.=" date_format(fec_nacimiento,'%e') dia, date_format(fec_nacimiento,'%c') mes, date_format(fec_nacimiento,'%Y') ano ";
$sql.=" ,s.ind_comentario, s.ind_firma, s.ind_asistencia ";
$sql.=" from t_plantilla p inner join t_solicitudes s";
$sql.=" on p.id_plantilla=s.id_plantilla";
$sql.=" where s.email='".$_SESSION["uusSerRmed_player"]."'";
$row=$db->get_row($sql,ARRAY_A);
$nombre=utf8_encode($row['nombre']);
$puesto=utf8_encode($row['puesto']);
$numero=utf8_encode($row['numero']);
$diafn=$row['dia'];
$mesfn=$row['mes'];
$anofn=$row['ano'];
$imagen=$row['imagen'];
$id=$row['id_plantilla'];
$comentario=$row['ind_comentario']=='S';
$firma=$row['ind_firma']=='S';
$asistencia=$row['ind_asistencia']=='S';

//include("admin/libs/util/images.php");


if (isset($_POST['borrar'])){
	include("admin/libs/util/paths.php");
	$path="admin/uploads/t_plantilla/".$id."/";
	if (file_exists($path)){
		if(rmdirr($path)){
			$sql="update t_plantilla set imagen=null where id_plantilla=".$id;
			$db->show_errors=false;
			$db->query($sql);
			if($db->last_error!="")
				$m=$db->last_error."-".$sql;
			else
				$m="Imagen eliminada correctamente.";
			$db->show_errors=false;
		}else
			$m="No se pudo eliminar la imagen";
	}
}

if ($_FILES['imagen']['name']!="") {
	include("admin/libs/util/paths.php");
	
	$path="admin/uploads/t_plantilla/".$id."/";
	$file=basename($_FILES['imagen']['name']);
	$targetPath=$path.$file;
	$savePath = str_replace("admin",".",$targetPath);
	$imagen=$savePath;
	if (!ensurePath($path)){
		$fallo="Error almacenando";
	}
	if(@!move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) 
		$fallo="No se pudo subir la imagen";
	else{
		$sql="update t_plantilla set imagen='".$savePath."' where id_plantilla=".$id;
		$db->show_errors=false;
		$db->query($sql);
		if($db->last_error!="")
			$fallo=$db->last_error."-".$sql;
		$db->show_errors=false;
		
		chmod($targetPath,0777);
		
		$widths[0]=48;
		$widths[1]=200;
		
		for($i=0;$i<=1;$i++){
			
			$width=$widths[$i];
			$height="";
			
			
			$pathRes=$path.$width."x".$height."/";
			
			if (!ensurePath($pathRes)){
				$fallo="Error creando las miniaturas";
			}
			
			if($width=="")
				$width=$height*10;
				
			if($height=="")
				$height=$width*10;
			
			
			if(!resizeImage($path, $pathRes, $file, $width,$height)){
				$msg="RESIZE";
				$fallo="Error creando las miniaturas";
			}
		}
	}
	if ($fallo=="")
		$m="Imagen subida correctamente";
	else
		$m=$fallo;
		
	
}



?>
<script type="text/javascript">
var oButtonDatos = new YAHOO.widget.Button({ 
	type: "link",
	id: "button-datos", 
	label: "Guardar Datos", 
	container: "btn-datos",
	href: "javascript:guardarDatos()" });
var oButtonUpload = new YAHOO.widget.Button({ 
	type: "link",
	id: "button-upload", 
	label: "Subir Imagen", 
	container: "btn-upload",
	href: "javascript:upload()" });
var oButtonDelete = new YAHOO.widget.Button({ 
	type: "link",
	id: "button-delete", 
	label: "Eliminar Imagen", 
	container: "btn-upload",
	href: "javascript:deleteFile()" });
var oButtonSettings = new YAHOO.widget.Button({ 
	type: "link",
	id: "button-settings", 
	label: "Guardar Notificaciones", 
	container: "btn-settings",
	href: "javascript:guardarNotificaciones()" });
	
function upload(){
	document.getElementById('fupload').submit();
}
function deleteFile(){
	document.getElementById('fdelete').submit();
}
	
function guardarDatos(){
	params="n="+document.getElementById('nombre').value;
	params+="&nu="+document.getElementById('numero').value;
	params+="&p="+document.getElementById('puesto').value;
	params+="&d="+document.getElementById('dia').value;
	params+="&m="+document.getElementById('mes').value;
	params+="&a="+document.getElementById('ano').value;
		
	conDatos=crearXMLHttpRequest();
	conDatos.onreadystatechange = cbGuardarDatos;
	conDatos.open('POST','jugador/datos.php', true);
	conDatos.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	conDatos.setRequestHeader("Content-length", params.length);
	conDatos.setRequestHeader("Connection", "close");
	conDatos.send(params);
}
function cbGuardarDatos(){
	if(conDatos.readyState == 4)
	{
		rs=document.getElementById('rs1');
		if(conDatos.responseText=="OK")
			rs.innerHTML="Datos actualizados correctamente.";
		else
			rs.innerHTML=conDatos.responseText;
		rs.style.display="block";
			
		
	}
}

function guardarNotificaciones(){
	if(document.getElementById('comentario').checked)
		params="c=S";
	else
		params="c=N";

	if(document.getElementById('firma').checked)
		params+="&f=S";
	else
		params+="&f=N";

	if(document.getElementById('asistencia').checked)
		params+="&a=S";
	else
		params+="&a=N";
		
		
		
	conNotificaciones=crearXMLHttpRequest();
	conNotificaciones.onreadystatechange = cbGuardarNotificaciones;
	conNotificaciones.open('POST','jugador/notificaciones.php', true);
	conNotificaciones.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	conNotificaciones.setRequestHeader("Content-length", params.length);
	conNotificaciones.setRequestHeader("Connection", "close");
	conNotificaciones.send(params);
}
function cbGuardarNotificaciones(){
	if(conNotificaciones.readyState == 4)
	{
		rs=document.getElementById('rs3');
		if(conNotificaciones.responseText=="OK")
			rs.innerHTML="Notificaciones actualizadas correctamente.";
		else
			rs.innerHTML=conNotificaciones.responseText;
		rs.style.display="block";
			
		
	}
}


</script>
<link rel="stylesheet" type="text/css" href="css/jugador.css" />
<div id="bd">
	<div id="preferences_section">
		<div id="preferences_section_personal">
			<div id="section_header_personal" class="section_header">
				<span>Informaci&oacute;n personal</span>
			</div>
			<div id="perfil_container">
				<table> 
				<tr><td class="label_item" >Nombre/Alias:</td><td><input type="text" value="<?=$nombre?>" id="nombre" size="30" maxlength="255"></td></tr>
				<tr><td class="label_item" >Puesto:</td><td><input type="text" value="<?=$puesto?>" id="puesto" size="10" maxlength="30"></td></tr>
				<tr><td class="label_item" >Dorsal:</td><td><input type="text" value="<?=$numero?>"  id="numero" size="1" maxlength="3"></td></tr>
				<tr><td class="label_item" >Fecha de Nacimiento:</td><td>
				<select id="dia" style="margin-right:5px;">
					<?
					
					
					for ($dia=1;$dia<=31;$dia++){
						$selected="";
						if ($dia==$diafn)
							$selected="selected";
						echo '<option '.$selected.' value='.$dia.' >'.$dia.'</option>';
					}?>
				</select>
				<select id="mes" style="margin-right:5px;">
					<option value='1' >Enero</option>
					<option value='2' >Febrero</option>
					<option value='3' >Marzo</option>
					<option value='4' >Abril</option>
					<option value='5' >Mayo</option>
					<option value='6' >Junio</option>
					<option value='7' >Julio</option>
					<option value='8' >Agosto</option>
					<option value='9' >Septiembre</option>
					<option value='10' >Octubre</option>
					<option value='11' >Noviembre</option>
					<option value='12' >Diciembre</option>
				</select>
				<script type="text/javascript">
					document.getElementById('mes').value='<?=$mesfn?>';
				</script>
				<select id="ano" style="margin-right:5px;">
					<?
					$current=date("Y");
					for ($ano=$current;$ano>=1900;$ano--){
						$selected="";
						if ($ano==$anofn)
							$selected="selected";
						echo '<option '.$selected.' value='.$ano.' >'.$ano.'</option>';
					}?>
				</select>
				</td></tr>
				</table>
			</div>
			<center><div id="btn-datos"></div></center>
			<center><div id="rs1" class="msg_resultado"></div></center>
		</div>
		<div id="preferences_section_imagen">
			<div id="section_header_personal" class="section_header">
				<span>Imagen de perfil</span>
			</div>
			<div id="perfil_container">
				<form action="" method="post" id="fdelete">
				<input name="borrar" type="hidden" value="1">
				</form>
				<table> 
				<tr>
				<td style="padding-left:50px"><?=paintImg($imagen,$nombre,200,"./img/player200.gif")?></td>
				<td>Imagen:</td><td>
					<form action="" method="post" enctype="multipart/form-data" id="fupload">
					<input name="imagen" type="file" id="imagen">
					</form>

				</td></tr>
				</table>
			</div>
			<center><div id="btn-upload" ></div></center>
			<center><div id="rs2" class="msg_resultado"></div></center>
		</div>
		<div id="preferences_section_settings">
			<div id="section_header_personal" class="section_header">
				<span>Notificaciones</span>
			</div>
			<div id="perfil_container">
				<div style='text-align:center;margin-bottom:15px;'>Enviar notificaciones por correo electr&oacute;nico a <i><?=$_SESSION["uusSerRmed_player"]?></i> cuando ocurra:</div>
				<table width="80%">  
				<tr><td class="label_item" >Publicaci&oacute;n de un comentario:</td><td><input id="comentario" type="checkbox"></td></tr>
				<tr><td class="label_item" >Firma en el libro de visitas:</td><td><input id="firma" type="checkbox"></td></tr>
				<tr><td class="label_item" >Solicitud de confirmaci&oacute;n de asistencia:</td><td><input id="asistencia" type="checkbox"></td></tr>
				</table>
			</div>
			<script type="text/javascript">
				<?
				if ($comentario)
					echo 'document.getElementById("comentario").checked=true;';
				if ($firma)
					echo 'document.getElementById("firma").checked=true;';
				if ($asistencia)
					echo 'document.getElementById("asistencia").checked=true;';
				?>	
			</script>
			<center><div id="btn-settings"></div></center>
			<center><div id="rs3" class="msg_resultado"></div></center>
		</div>
	</div>
</div>
<?
if($m!="")
	echo "<script>document.getElementById('rs2').innerHTML=\"".$m."\";document.getElementById('rs2').style.display='block';</script>";
include("bottom.php")?>

