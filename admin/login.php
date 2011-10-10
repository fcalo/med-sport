<?

    
	include("libs/session.php");
	include("config/database.php");
	include("libs/util/mmail.php");
	include("../assets/util.php");

        
	
	if($_POST['olvidada']==1){
		
		function generar_pass ($longitud) {
	
	        mt_srand((double)microtime()*1000000);
	
	        $letras=array (array ("a","e","i","o","u"),array ("b","c","d","f","g","h","j","k","l","m","n","p","q","r","s","t","v","w","x","y","z"),array ("br","ch","cr","fr","gr","pr","pl","tr"),array ("lt","lm","ln","ls","mn","st","sm"));
	
	        // Primer caracter
	        $primero=mt_rand (0,2);
	        $elemento=mt_rand (0,count ($letras[$primero])-1);
	        $password=$letras[$primero][$elemento];
	        if ($primero!=0) $cons=true;
	
	        while (strlen ($password)<$longitud) {
	                if ($cons) {
	                        $index_prob=mt_rand (0,4);
	                        $password.=$letras[0][$index_prob];
	                        $cons=false;
	                }
	                else {
	                        $cons=array ("1","1","1","2","3");
	                        // tiene mas probabilidades, 3/5 de salir solo una consonante
	                        $index_prob=mt_rand (0,count($cons)-1);
	                        $elemento=mt_rand (0,count ($letras[$cons[$index_prob]])-1);
	                        $password.=$letras[$cons[$index_prob]][$elemento];
	                }
	        }
	        return substr ($password,0,$longitud); // ya que si lo ultimo a�adido es una consonante doble puede que estemos a�adiendo un caracter de mas

		}
		$user=$_POST['user'];
		include('./config/database.php');	

		//$rs=$db->get_row("select email from t_login where user='".$user."'",ARRAY_A);
		//$email=$rs['email'];
		$email=$user;
		if($email!=""){
			$pass=ucfirst(generar_pass(7)).rand(1,99);
			$sql="update t_login set pass='".sha1($pass)."' where user='".$user."'";
			$db->query($sql);
			//$header = "From: ". $Name . " <" . $email . ">\r\n"; 

			$server="http://".$_SERVER['HTTP_HOST'];
			$link=$server."/activacion.php?i=".$idEquipo."&k=".sha1("SalT".$_GET['mail'].sha1($_GET['passb']));
			$cuerpo="<div>";
			$cuerpo.="<div>Su nueva contrase&ntilde;a es: </div>";
			$cuerpo.="<div>contrase&ntilde;a: ".$pass."</div>";
			$cuerpo.="<div>&nbsp;</div>";
			$cuerpo.="<div>Si no ha solicitado est� email, ignorelo.</div>";
			$cuerpo.="<div>&nbsp;</div>";
			$cuerpo.="<div><a href='".$server."'>".$server."</a></div>";
			$cuerpo.="</div>";
			if ($rt=sendMail($email,utf8_encode("restablecer contrase�a[miequipodeportivo.com]"),utf8_encode($cuerpo)))
				$rt="OK";
			else
				$error=utf8_encode("Se le ha enviado un email con su nueva contras�a.");
		}
		else
			$error="No se pudo recuperar el email, pongase en contacto con el administrador.";
		
	}else{
	
		
		
		
		$user=$_POST['user'];
		$pass=$_POST['pass'];
		$failed=false;
		if(($user!="" && $pass!="") || isset($_COOKIE['key_med']))
		{
			
			if (ALLOW_ACCESS || strtolower($user)=="admin"){
				include('./config/database.php');
				
				if (isset($_COOKIE['key_med']) && $pass==""  ){
					$rs=$db->get_row("select count(*) c from t_login where user='".$_COOKIE['id_med']."' and pass='".$_COOKIE['key_med']."' and fec_activacion is not null ",ARRAY_A);
					if ($rs['c']>0){
						$_SESSION[constant(USER.PROJECT)]=$_COOKIE['id_med'];
					}else{
						//player?
						$rs=$db->get_row("select count(*) c from t_solicitudes where email='".$_COOKIE['id_med']."' and pass='".$_COOKIE['key_med']."' and fec_confirmacion is not null ",ARRAY_A);
						if ($rs['c']>0){
							$_SESSION[constant(USER.PROJECT)."_player"]=$_COOKIE['id_med'];
						}
					}
					Header('Location: /');
					die ("header");
					end;
				}else{
					$rs=$db->get_row("select count(*) c from t_login where user='".$user."' and pass='".sha1($pass)."' and fec_activacion is not null ",ARRAY_A);
					
					if ($rs['c']>0){
						$_SESSION[constant(USER.PROJECT)]=$user;
						if($_POST['rememberme']!=null){
							setcookie("id_med", $_POST['user'], time()+60*60*24*100, "/");
							if (isset($_COOKIE['key_med']))
								setcookie("key_med", $_COOKIE['key_med'], time()+60*60*24*100, "/");
							else{
								setcookie("key_med", sha1($_POST['pass']), time()+60*60*24*100, "/");
							}
						}

					}
					else{
						// no es admin, tal vez sea player
						$rs=$db->get_row("select count(*) c from t_solicitudes where email='".$user."' and pass='".sha1($pass)."' and fec_confirmacion is not null ",ARRAY_A);
						if ($rs['c']>0){
							$_SESSION[constant(USER.PROJECT)."_player"]=$user;
							if($_POST['rememberme']!=null){
								setcookie("id_med", $_POST['user'], time()+60*60*24*100, "/");
								if (isset($_COOKIE['key_med']))
									setcookie("key_med", $_COOKIE['key_med'], time()+60*60*24*100, "/");
								else{
									setcookie("key_med", sha1($_POST['pass']), time()+60*60*24*100, "/");
								}
							}

						}else
							$failed=true;
					}
						
				}
			}
			
		}
			
		
		if(isset($_SESSION[constant(USER.PROJECT)]) && $_SESSION[constant(USER.PROJECT)]!="")
		{
			header('Location: index.php');
			end;
		}
		if(isset($_SESSION[constant(USER.PROJECT)."_player"]) && $_SESSION[constant(USER.PROJECT)."_player"]!="")
		{
		
			$sql="select e.url_equipo, d.url_deporte from ";
			$sql.=" t_equipos e inner join t_plantilla p on p.id_equipo=e.id_equipo ";
			$sql.=" inner join t_solicitudes s on s.id_plantilla=p.id_plantilla ";
			$sql.=" inner join t_deportes d on d.id_deporte=e.id_deporte ";
			$sql.=" where s.email='".$_SESSION[constant(USER.PROJECT)."_player"]."'";
			$rs=$db->get_row($sql,ARRAY_A);
			header('Location: '.getServer()."/deporte/".$rs['url_deporte']."/".$rs['url_equipo']);
			end;
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Acceso al Sistema</title>

<!--dependencias yui-->
<link rel="stylesheet" type="text/css" href="js/yui/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="js/yui/build/button/assets/skins/sam/button.css">


<script type="text/javascript" src="js/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="js/yui/build/element/element-min.js"></script>
<script type="text/javascript" src="js/yui/build/button/button-min.js"></script>


<style>
body {
	margin:0;
	padding:10;
	height:100%;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
input{
	background-color: #F7F7F7;
	border: #C0C0C0 1px solid;
	width:45%;
	
}
#loginbox{
	position:absolute;
	border: 3px solid #CCC;
	top:33%;
/*	height:100px;
	min-height:100px;*/
	left:40%;
	width:20%;
	min-width:20%;
	padding-bottom:10px;
}
#failedbox{
	border: 3px solid #F00;
	color:#F00;
	text-align:center;
	width:98%;
    display:none;
	margin-top:50px;
}
#boxuser{
	padding:5px;
	padding-top:10px;
	
}
#boxpass{
	padding:5px;
	padding-bottom:15px;
	
}
label {display:block;float:left;width:45%;clear:left; }

#link{
	clear:both;
	position:relative;
	float:left;
	font-size: 9px;
	text-align:center;
	padding:5px;
	padding-top:10px;
	
}
#link2{
	position:relative;
	float:right;
	font-size: 9px;
	text-align:center;
	padding:5px;
	padding-top:10px;
	
}
a {
	color: #ccc;
}
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #ccc;
}
a:hover {
	text-decoration: underline;
	color: #333333;
}
a:active {
	text-decoration: none;
	color: #ccc;
}

#button_ko{position:relative;float:right;}
#button_ok{position:relative;float:right;}

</style>
<script>
var oButtonOk = new YAHOO.widget.Button({  
    type: "link",  
    id: "buttonOk",  
    label: "Aceptar",  
    href: "javascript:aceptar()",  
    container: "button_ok"}); 
var oButtonCancel = new YAHOO.widget.Button({  
    type: "link",  
    id: "buttonKo",  
    label: "Cancelar",  
    href: "javascript:history.back()",  
    container: "button_ko"}); 


function press(){
		if (event.keyCode == 13)
			aceptar();
}
function press_ff(e) {
	if (e.which == 13)
		aceptar();
}
var clientPC = navigator.userAgent.toLowerCase();
var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1)); 
function init(){
	if (is_ie) {
		document.getElementById("user").onkeypress = press;
		document.getElementById("pass").onkeypress = press;
	}else {
		document.getElementById("user").onkeypress = press_ff;
		document.getElementById("pass").onkeypress = press_ff;
	}
	document.getElementById("user").focus();
	<?if ($failed){
		echo 'document.getElementById("failedbox").style.display="block";';
	}?>

}
function aceptar(){
	if (document.getElementById("user").value=='' || document.getElementById("pass").value==""){
		alert('Introduzca los datos para conectar.');
		document.getElementById("user").focus();
	}else
		document.getElementById("frmLogin").submit();
}
function olvidada(){
	if(document.getElementById("user").value=="")
		alert("Introduzca el usuario para recordar la contrase<?=utf8_encode('�')?>a");
	else{
		document.getElementById("olvidada").value=1;
		document.getElementById("frmLogin").submit();
	}
}
<?
if ($error!="")
	echo "alert('".$error."');";
?>
</script>
<body class="yui-skin-sam" onLoad="init()">

	<div id="loginbox">
		<form name="frmLogin" id="frmLogin" method="POST">
		<input type="hidden" name="olvidada" id="olvidada">
		<div id="boxuser"><label>Usuario</label><input type="text" name="user" id="user"/></div>
		<div id="boxpass"><label>Contrase&ntilde;a</label><input type="password" name="pass" id="pass"/></div>

		<div id="boxbuttons">
			<span id="button_ok"></span>
			<span id="button_ko"></span>
		</div>
		<?if(CHANGE_PASS){?>
		<div id="link"><a href="cpass.php">Cambiar contrase&ntilde;a</a></div>
		<div id="link2"><a href="javascript:olvidada()">Contrase&ntilde;a olvidada</a></div>
		<?}?>
		</form>
		<div id="failedbox">Datos Incorrectos</div>
	</div>
</body>
</html>