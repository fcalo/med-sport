<div id="bottom">
	<div id="bottom-in">
		<div><a href="<?=getServer()?>/aviso.php" rel="nofollow">Aviso legal</a></div><div>|</div>
		<div><a href="mailto:contacta@miequipodeportivo.com" rel="nofollow">Contacta</a></div><div>|</div>
		<?if($reurl!=""){?>
			<div><a href="<?=getServer()?>">Administra tu equipo</a></div><div>|</div>
		<?}else{?>
			<div><a href="<?getServer()?>/deporte/futbol/gestor-de-equipos">Equipo demostraci&oacute;n</a></div><div>|</div>
		<?}?>
	</div>
</div>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-3743387-2");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
<?//@$compressor->finish();?>