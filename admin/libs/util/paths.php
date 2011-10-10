<?php
function ensurePath($path){
	//return true;
	return ensurePath_($path);
	/*$rt=true;
	if (!file_exists($path)){
		if (!mkdir($path,0777,true))
			die("No se pudo crear ".$path);
	}
	return $rt;
	*/
}

function ensurePath_($path){
	$rt=true;
	$a=split("/",$path);
	$c=sizeof($a);
	$r="";
	for ($i=0;$i<$c;$i++){
		$r.=$a[$i]."/";
		if (!file_exists($r)){
			//echo "no existe";
			if (!mkdir($r))
				die("No se pudo crear ".$path);
                        chmod($r,0766);
		}
                
		
		
	}
	return $rt;
		
}


/** * Delete a file, or a folder and its contents 
* * @author Aidan Lister <aidan@php.net> 
* @version 1.0.0 
* @param string $dirname The directory to delete 
* @return bool Returns true on success, false on failure */
function rmdirr($dirname){ 
	// Simple delete for a file 
	if (is_file($dirname)) 
	{ 
		return unlink($dirname); 
	} 
	// Loop through the folder 
	$dir = dir($dirname); 
	while (false !== $entry = $dir->read()) { 
		// Skip pointers 
		if ($entry == '.' || $entry == '..') 
		{ continue; } 
		// Deep delete directories 
		if (is_dir("$dirname/$entry")) { 
			rmdirr("$dirname/$entry"); 
		} else { 
			unlink("$dirname/$entry"); 
		} 
	} 
	// Clean up 
	$dir->close(); 
	return rmdir($dirname);
}
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

	return " replace(replace( replace( replace( replace( replace( replace( replace( replace( replace( replace( replace( replace(replace(replace(lower(".utf8_encode($campo)."),'  ',' '),'  ',' '),'+ ',''),' ','-'),'<br>',''),'(',''),')',''),',',''),'.',''),'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),'ñ','n')";
}
?>