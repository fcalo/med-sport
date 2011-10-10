<?php header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE cachingheader( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );header( "Cache-Control: no-cache, must-revalidate" );header( "Pragma: no-cache" );header('Content-Type: text/html; charset=UTF-8');include("admin/libs/util/mmail.php");function validar_clave($clave,&$error_clave){   if(strlen($clave) < 6){      $error_clave = "Debe tener al menos 6 caracteres";      return false;   }   if(strlen($clave) > 16){      $error_clave = "No puede tener m�s de 16 caracteres";      return false;   }   /*if (!preg_match('`[a-z]`',$clave)){      $error_clave = "Debe tener al menos una letra min�scula";      return false;   }   if (!preg_match('`[A-Z]`',$clave)){      $error_clave = "Debe tener al menos una letra may�scula";      return false;   }   if (!preg_match('`[0-9]`',$clave)){      $error_clave = "Debe tener al menos un caracter num�rico";      return false;   }*/   $error_clave = "";   return true;} function comprobar_email($email){    $mail_correcto = 0;    //compruebo unas cosas primeras    if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){       if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {          //miro si tiene caracter .          if (substr_count($email,".")>= 1){             //obtengo la terminacion del dominio             $term_dom = substr(strrchr ($email, '.'),1);             //compruebo que la terminaci�n del dominio sea correcta             if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){                //compruebo que lo de antes del dominio sea correcto                $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);                $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);                if ($caracter_ult != "@" && $caracter_ult != "."){                   $mail_correcto = 1;                }             }          }       }    }    if ($mail_correcto)       return true;    else       return false;} include("back/config/database.php");if(!isset($_GET['c'])){		//valida todos los campos	if (!comprobar_email($_GET['mailc']))		die("mailc#No es un email valido");	if(!validar_clave($_GET['passc'],$error))		die("passc#".utf8_encode($error));	$v=$_GET['repassc'];	$medio=strlen($v)/2;	if($medio==round($medio,0) && $medio>5 && substr($v,0,$medio)==substr($v,-$medio))		$rt="";	else		die("repassc#Las ".utf8_encode("contrase�as")." no son iguales");	$sql="insert into t_solicitudes(email, id_plantilla, pass, fec_solicitud, mensaje) values ";	$sql.="('".$_GET['mailc']."','".$_GET['id']."',sha1('".$_GET['passc']."'), now(),'".$_GET['msg']."')";		$db->show_errors=false;	if ($db->query($sql)){			$sql="select nombre, t_equipos.user mail from t_plantilla inner join t_equipos on t_plantilla.id_equipo=t_equipos.id_equipo where id_plantilla=".$_GET['id'];		$row=$db->get_row($sql,ARRAY_A);		$nombre=$row['nombre'];		$mail=$row['mail'];			$server="http://".$_SERVER['HTTP_HOST'];		$link=$server."/admin";		$cuerpo="<div>";		$cuerpo.="<div>Tienes una nueva solicitud de identidad de un jugador de tu equipo, puedes verla desde la secci�n de solicitudes de identidad en el admin de tu equipo</div>";		$cuerpo.="<div><a href='".$link."'>".$link."</a></div>";		$cuerpo.="<div>&nbsp;</div>";		$cuerpo.="<div>&nbsp;</div>";		$cuerpo.="<div>Gracias,<br/></div>";		$cuerpo.="<div><a href='".$server."'>Mi Equipo Deportivo</a></div>";		$cuerpo.="</div>";		if ($rt=sendMail($mail,utf8_encode("solicitud de identidad[".$nombre."]"),utf8_encode($cuerpo)))			$rt="OK";	}else{		if (strpos("  ".strtolower($db->last_error),"duplicate entry")>0)			die("mailc#Este Email ya ".utf8_encode("est�")." dado de alta en el sistema");		else			$rt="<br>Ocurrio un error. Intentelo m�s tarde.".$db->last_error;	}	$db->show_errors=true;	echo utf8_encode($rt);}else{	switch($_GET['c']){		case 'mailc':				if (comprobar_email($_GET['v']))					$rt="OK";				else					$rt="No es un email valido";			break;		case 'passc':			$error="";			if(validar_clave($_GET['v'],$error))				$rt="OK";			else				$rt=$error;			break;		case 'repassc':			$v=$_GET['v'];			$medio=strlen($v)/2;			if($medio==round($medio,0) && $medio>5 && substr($v,0,$medio)==substr($v,-$medio))				$rt="OK";			else				$rt="Las contrase�as no son iguales";			break;	}		$rt=$_GET['c']."#".$rt;	echo utf8_encode($rt);		}?>