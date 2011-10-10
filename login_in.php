
<script type="text/javascript">
var oLinkButton4 = new YAHOO.widget.Button({  
	type: "link",  
	id: "btnLogin",  
	label: "Entrar",  
	href: "javascript:document.getElementById('frmlog').submit()",  
	container: "btn-login" }); 
</script>
<div id="login-box">
<?
if ($_POST['logout']==1){
	unset($_SESSION["uusSerRmed"]);
	unset($_SESSION["uusSerRmed_player"]);
}
	
if(isset($_SESSION["uusSerRmed"]))
	$user=$_SESSION["uusSerRmed"];
else
	$user=$_SESSION["uusSerRmed_player"];

if (!isset($_SESSION["uusSerRmed"]) && !isset($_SESSION["uusSerRmed_player"])){?>
	<form id="frmlog" action="<?=getServer()?>/admin/login.php" method="post">
	<div id="login-1">
		<div id="link-forgot"><a href='<?=getServer()?>/reset.php' rel="nofollow"><?=utf8_encode('¿')?>Has olvidado tu contrase&ntilde;a?</a></div>
		<div id="check-rememberme"><input type="checkbox" name="rememberme" id="rememberme"><label for="rememberme">Recuerdame</label> </div>
	</div>
	<div id="login-inputs">
		<div id="btn-login"></div>
		<div id="login-2">
			<input type="text" id="user" name="user" onkeypress="keypressLogin(event)">
			<input type="password" id="pass" name="pass" onkeypress="keypressLogin(event)">
		</div>
		
	</div>
	</form>
<?}else{?>
	<div id="loged">
		<div><span style='color:#8CF'><?=$user?></span></div>
		<?$server="http://".$_SERVER['REQUEST_URI'];
		if(strpos($server,"admin")>0){
			$sql="select e.url_equipo, e.nom_equipo, d.url_deporte from t_deportes d, t_equipos e ";
			$sql.=" where e.id_deporte=d.id_deporte and e.user='".$_SESSION["uusSerRmed"]."' order by id_equipo";
			$rs=$db->get_results($sql,ARRAY_A);
			$count=sizeof($rs);
			for($i=0;$i<$count;$i++){
				$row=$rs[$i];
				$destino=$row['nom_equipo'];
				$link=getServer()."/deporte/".$row['url_deporte']."/".$row['url_equipo'];
				echo '<div id="link-logaut"><a href="'.$link.'">'.utf8_encode($destino).'</a></div>';
			}
			
		}else{
			if(strpos($server,"jugador.php")>0){
				$sql="select e.url_equipo, e.nom_equipo, d.url_deporte ";
				$sql.=" from t_deportes d inner join t_equipos e on e.id_deporte=d.id_deporte";
				$sql.=" inner join t_plantilla p on p.id_equipo=e.id_equipo";
				$sql.=" inner join t_solicitudes s on s.id_plantilla=p.id_plantilla";
				$sql.=" where s.email='".$_SESSION["uusSerRmed_player"]."' order by e.id_equipo";
				$rs=$db->get_results($sql,ARRAY_A);
				$count=sizeof($rs);
				for($i=0;$i<$count;$i++){
					$row=$rs[$i];
					$destino=$row['nom_equipo'];
					$link=getServer()."/deporte/".$row['url_deporte']."/".$row['url_equipo'];
					echo '<div id="link-logaut"><a href="'.$link.'">'.utf8_encode($destino).'</a></div>';
				}
				
			}else{
				if(isset($_SESSION["uusSerRmed"])){
					$destino="Administrar";
					$link=getServer()."/admin/";
					echo '<div id="link-logaut"><a href="'.$link.'">'.$destino.'</a></div>';
				}else{
					$destino="Preferencias";
					$link=getServer()."/jugador.php";
					echo '<div id="link-logaut"><a href="'.$link.'">'.$destino.'</a></div>';
				}
			}
				
		}
		?>
		
		<div id="link-logaut"><a href="javascript:document.getElementById('frmLogout').submit();" onmouseover="window.status='';return true;">Salir</a></div>
		<form id="frmLogout" method="post">
		<input type="hidden" name="logout" value="1">
		</form>
	</div>
<?}
?>

</div>