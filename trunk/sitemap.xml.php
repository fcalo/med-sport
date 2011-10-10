<?
function urls_amigables($url) {

	//a minusculas
	$url = utf8_encode(strtolower($url));

	//caracteres especiales latinos
	$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
	$repl = array('a', 'e', 'i', 'o', 'u', 'n');
	$url = str_replace ($find, $repl, $url);

	//guiones
	$find = array(' ', '&', '\r\n', '\n', '+');
	$url = str_replace ($find, '-', $url);

	//dem�s caracteres especiales
	$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
	$repl = array('', '-', '');
	$url = preg_replace ($find, $repl, $url);

	return $url;

}
function amigableMySql($campo)
{
	return " replace(replace( replace( replace( replace( replace( replace( replace( replace( replace( replace( replace( replace(replace(replace(lower(".$campo."),'  ',' '),'  ',' '),'+ ',''),' ','-'),'<br>',''),'(',''),')',''),',',''),'.',''),'�','a'),'�','e'),'�','i'),'�','o'),'�','u'),'�','n')";
}

function getUrlEquipoSinAdmin($idTorneo,$idEquipo){

	$sql="select d.url_deporte,d.id_deporte, ".amigableMySql("te.nom_equipo")." nom_equipo ";
	$sql.=" from t_deportes d join t_equipos e on d.id_deporte=e.id_deporte join t_torneos t on e.id_equipo=t.id_equipo,";
	$sql.=" t_torneos_equipos te";
	$sql.=" where t.id_torneo=".$idTorneo;
	$sql.=" and te.id_torneo=t.id_torneo";
	$sql.=" and te.id_torneos_equipos=".$idEquipo;
	$host    = "mysql5-20.perso"; // Host name
	$db_name = "miequipomed";		// Database name
	$db_user = "miequipomed";		// Database user name
	$db_pass = "m3dm3d10";
	$conn = mysql_connect($host,$db_user,$db_pass)or die(mysql_error());
	mysql_select_db($db_name,$conn)or die(mysql_error());
	$resultSet = mysql_query($sql);
	if($row = mysql_fetch_array($resultSet)){
		$deporte=$row['url_deporte'];
		$idDeporte=$row['id_deporte'];
		$equipo=urls_amigables($row['nom_equipo']);
	}
	return "http://www.miequipodeportivo.com/deporte/".$deporte."/".$equipo."/".$idTorneo;

}



//include("../back/config/database.php");

$nl=chr(13).chr(10);



$xml='<?xml version="1.0" encoding="UTF-8"?>'.$nl;

$xml.='<urlset'.$nl;

$xml.='      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'.$nl;

$xml.='      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'.$nl;

$xml.='      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'.$nl;

$xml.='            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'.$nl;

$xml.='<url>'.$nl;

$xml.='  <loc>http://www.miequipodeportivo.com/</loc>'.$nl;

$xml.='  <priority>1.00</priority>'.$nl;

$xml.='</url>'.$nl;

$xml.='<url>'.$nl;

$xml.='  <loc>http://www.miequipodeportivo.com/reset.php</loc>'.$nl;

$xml.='  <priority>0.10</priority>'.$nl;

$xml.='</url>'.$nl;
$xml.='<url>'.$nl;

$xml.='  <loc>http://www.miequipodeportivo.com/aviso.php</loc>'.$nl;

$xml.='  <priority>0.10</priority>'.$nl;

$xml.='</url>'.$nl;


$xml.='<url>'.$nl;
$xml.='  <loc>http://www.miequipodeportivo.com/gestor-equipos.php</loc>'.$nl;
$xml.='  <priority>0.50</priority>'.$nl;
$xml.='</url>'.$nl;


$xml.='<url>'.$nl;
$xml.='  <loc>http://www.miequipodeportivo.com/gestor-torneos.php</loc>'.$nl;
$xml.='  <priority>0.50</priority>'.$nl;
$xml.='</url>'.$nl;

$xml.='<url>'.$nl;
$xml.='  <loc>http://www.miequipodeportivo.com/gestor-plantilla.php</loc>'.$nl;
$xml.='  <priority>0.50</priority>'.$nl;
$xml.='</url>'.$nl;

$xml.='<url>'.$nl;
$xml.='  <loc>http://www.miequipodeportivo.com/gestor-partidos.php</loc>'.$nl;
$xml.='  <priority>0.50</priority>'.$nl;
$xml.='</url>'.$nl;

$xml.='<url>'.$nl;
$xml.='  <loc>http://www.miequipodeportivo.com/gestor-resultados.php</loc>'.$nl;
$xml.='  <priority>0.50</priority>'.$nl;
$xml.='</url>'.$nl;

$xml.='<url>'.$nl;
$xml.='  <loc>http://www.miequipodeportivo.com/gestor-clasificacion.php</loc>'.$nl;
$xml.='  <priority>0.50</priority>'.$nl;
$xml.='</url>'.$nl;

$xml.='<url>'.$nl;
$xml.='  <loc>http://www.miequipodeportivo.com/recordatorio-asistencia.php</loc>'.$nl;
$xml.='  <priority>0.50</priority>'.$nl;
$xml.='</url>'.$nl;

$xml.='<url>'.$nl;
$xml.='  <loc>http://www.miequipodeportivo.com/estadisticas.php</loc>'.$nl;
$xml.='  <priority>0.50</priority>'.$nl;
$xml.='</url>'.$nl;

$xml.='<url>'.$nl;
$xml.='  <loc>http://www.miequipodeportivo.com/fotos-videos.php</loc>'.$nl;
$xml.='  <priority>0.50</priority>'.$nl;
$xml.='</url>'.$nl;





$sql="select e.url_equipo, e.nom_equipo, d.url_deporte from t_deportes d, t_equipos e ";

$sql.=" where e.id_deporte=d.id_deporte order by id_equipo";

	$host    = "mysql5-20.perso"; // Host name
	$db_name = "miequipomed";		// Database name
	$db_user = "miequipomed";		// Database user name
	$db_pass = "m3dm3d10";



$conn = mysql_connect($host,$db_user,$db_pass)or die(mysql_error());

         mysql_select_db($db_name,$conn)or die(mysql_error());



$resultSet = mysql_query($sql);

while($row = mysql_fetch_array($resultSet))

{
	$link="http://www.miequipodeportivo.com/deporte/".$row['url_deporte']."/".$row['url_equipo'];
	$xml.='<url>'.$nl;
	$xml.='  <loc>'.$link.'</loc>'.$nl;
	$xml.='  <priority>0.80</priority>'.$nl;
	$xml.='</url>'.$nl;
}
//equipos sin administrador
$sql="select id_torneo, id_torneos_equipos id_equipo from t_torneos_equipos";
$resultSet = mysql_query($sql);

while($row = mysql_fetch_array($resultSet))

{

	$link=getUrlEquipoSinAdmin($row['id_torneo'],$row['id_equipo']);
	$xml.='<url>'.$nl;
	$xml.='  <loc>'.$link.'</loc>'.$nl;
	$xml.='  <priority>0.60</priority>'.$nl;
	$xml.='</url>'.$nl;

}
//partidos
$sql="select id_partido from t_partidos";
$resultSet = mysql_query($sql);

while($row = mysql_fetch_array($resultSet))
{
	$link="http://www.miequipodeportivo.com/partido/".$row['id_partido'];
	$xml.='<url>'.$nl;
	$xml.='  <loc>'.$link.'</loc>'.$nl;
	$xml.='  <priority>0.55</priority>'.$nl;
	$xml.='</url>'.$nl;
}

$xml.='</urlset>'.$nl;
header('Content-type: text/xml');
echo $xml;
/*$file="/home/miequipodeportivo/www/sitemap.xml";

$f=fopen($file,"w");

chmod($file, 0775);

$rt=fwrite($f,$xml);

if ($rt<1)

	$msg="Ocurrio un error generando el fichero(fWrite)";

fclose($f);

if ($msg=="")

	echo "Fichero generado correctamente<br>";

else

	echo $msg;


*/
?>