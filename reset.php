<?include("top.php")

?>
<script>
var oLinkButton4 = new YAHOO.widget.Button({  
	type: "link",  
	id: "btnReset",  
	label: "Restablecer contrase&ntilde;a",  
	href: "javascript:document.getElementById('frmreset').submit()",  
	container: "btn-reset" }); 
</script>
<div id="bd">
	<div  id="box-blue">
		<div id="reset-box">
			<h2><?=utf8_encode("¿Has olvidado tu contraseña?")?></h2>
			<div>Introduce tu direcci&oacute;n de correo para reestablecer la contrase&ntilde;a de tu cuenta</div><br>
			<form id="frmreset" method="post" action="admin/login.php">
			<input type="hidden" name="olvidada" value="1"/>
			<div class="login-ln"><div class="label">Email</div><input class="login-textarea" type="text" value="" name="user" id="mail" /></div>
			</form>
			<div id="breset"><div id="btn-reset"></div></div>
		</div>
	</div>
</div>
<?include("bottom.php")?>

