<?include(dirname(__FILE__)."/../admin/libs/util/images.php");function getServer(){		$server="http://".$_SERVER['HTTP_HOST'];	/*if(strpos($server,"localhost")>0)		$server.="/med";*/	return $server;}function getRoot(){	$root=$_SERVER['DOCUMENT_ROOT'];	if(strpos($root,"servidor")>0)		$root.="/med";	return $root;}function paintImg($imagen,$titulo, $width,$default,$onclick=''){		$filePath=getRoot()."/admin/".getThumb($imagen,$width,'');	$file=getServer()."/admin/".getThumb($imagen,$width,'');		if (file_exists($filePath) ){		if ($onclick==""){			return "<img src='".$file."' alt='".$titulo."' title='".$titulo."'/>";		}else			return "<a href=\"javascript:".$onclick."\" border='0'><img src='".$file."' alt='".$titulo."' title='".$titulo."' /></a>";	}	else		return "<img src='".getServer().$default."' border='0' />";	}function urls_amigables($url) {	//a minusculas	$url = utf8_encode(strtolower($url));	//caracteres especiales latinos	$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');	$repl = array('a', 'e', 'i', 'o', 'u', 'n');	$url = str_replace ($find, $repl, $url);	//guiones	$find = array(' ', '&', '\r\n', '\n', '+');	$url = str_replace ($find, '-', $url);	//dem�s caracteres especiales	$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');	$repl = array('', '-', '');	$url = preg_replace ($find, $repl, $url);	return $url;}function amigableMySql($campo){	return " replace(replace( replace( replace( replace( replace( replace( replace( replace( replace( replace( replace( replace(replace(replace(lower(".utf8_encode($campo)."),'  ',' '),'  ',' '),'+ ',''),' ','-'),'<br>',''),'(',''),')',''),',',''),'.',''),'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),'ñ','n')";}function getUrlEquipoSinAdmin($idTorneo,$idEquipo){	//include("back/config/database.php");	 include(dirname(__FILE__)."/../admin/config/database.php");        //Comprueba si tiene admin        $sql="select d.url_deporte, e.url_equipo nom_equipo";        $sql.=" from t_equipos e inner join t_torneos_equipos te on te.id_torneo=e.id_torneo_origen ";        $sql.=" inner join  t_deportes d on d.id_deporte=e.id_deporte ";        $sql.=" where te.id_torneo=$idTorneo";        $sql.=" and te.id_torneos_equipos=$idEquipo";        $sql.=" and e.url_equipo=".amigableMySql("te.nom_equipo");        $sql.=" union ";        $sql.=" select d.url_deporte, e.url_equipo nom_equipo";        $sql.=" from t_equipos e inner join t_deportes d on d.id_deporte=e.id_deporte ";        $sql.=" inner join t_torneos t on t.id_equipo=e.id_equipo ";        $sql.=" inner join t_equipos current on t.id_torneo=current.id_torneo_origen ";        $sql.=" ,t_torneos_equipos te ";        $sql.=" where te.id_torneo=$idTorneo";        $sql.=" and te.id_torneos_equipos=$idEquipo";        $sql.=" and e.url_equipo=".amigableMySql("te.nom_equipo");        $sql.=" union ";        $sql.=" select d.url_deporte, e.url_equipo nom_equipo";        $sql.=" from t_equipos e inner join t_deportes d on d.id_deporte=e.id_deporte ";        $sql.=" inner join t_torneos t on t.id_equipo=e.id_equipo ";        $sql.=" inner join t_equipos current on e.id_torneo_origen=current.id_torneo_origen ";        $sql.=" ,t_torneos_equipos te ";        $sql.=" where te.id_torneo=$idTorneo";        $sql.=" and te.id_torneos_equipos=$idEquipo";        $sql.=" and e.url_equipo=".amigableMySql("te.nom_equipo");        $row=$db->get_row($sql,ARRAY_A);        if($row!=null){            $deporte=$row['url_deporte'];            $equipo=urls_amigables($row['nom_equipo']);            return getServer()."/deporte/".$deporte."/".$equipo;        }else{            $sql="select d.url_deporte,d.id_deporte, ".amigableMySql("te.nom_equipo")." nom_equipo, e.id_torneo_origen ";            $sql.=" from t_deportes d join t_equipos e on d.id_deporte=e.id_deporte join t_torneos t on e.id_equipo=t.id_equipo,";            $sql.=" t_torneos_equipos te";            $sql.=" where t.id_torneo=".$idTorneo;            $sql.=" and te.id_torneo=t.id_torneo";            $sql.=" and te.id_torneos_equipos=".$idEquipo;            $row=$db->get_row($sql,ARRAY_A);            $deporte=$row['url_deporte'];            $idDeporte=$row['id_deporte'];            $equipo=urls_amigables($row['nom_equipo']);            if($row['id_torneo_origen']!=null)                $idTorneo=$row['id_torneo_origen'];            return getServer()."/deporte/".$deporte."/".$equipo."/".$idTorneo;        }	}function copiarDatosEquipo($mail,$torneo,$equipo,$idEq){    include("back/config/database.php");    //t_torneos    $sql="insert into t_torneos (id_equipo, temporada, nom_torneo, user, puntos_victoria, puntos_empate, puntos_derrota)";    $sql.=" select $idEq id_equipo, temporada, nom_torneo, '".$mail."' user, puntos_victoria, puntos_empate, puntos_derrota ";    $sql.=" from t_torneos where id_torneo=".$torneo;    $db->query($sql);        if($db->last_error!="")        die($sql."--".$db->last_error);    $newIdTorneo=$db->insert_id;        //t_torneos_equipos    $oldIdEquipo=0;    $findEquipo=$equipo;    $binicio=true;    $loops=0;    while($oldIdEquipo==0 && $loops<10){        $sql="select id_torneos_equipos id_equipo ";        $sql.=" from t_torneos_equipos te inner join t_torneos t on te.user=t.user";        $sql.=" where t.id_torneo=".$torneo;        $sql.=" and nom_equipo like '%$findEquipo%'";        $rs = mysql_query($sql);        if($rw = mysql_fetch_array($rs)){            $oldIdEquipo=0;            if(sizeof($rs)>0)                $oldIdEquipo=$rw['id_equipo'];        }        if($oldIdEquipo==0)            if($binicio)                $findEquipo=substr($findEquipo,1);            else                $findEquipo=substr($findEquipo,0,strlen($findEquipo)-1);        $binicio=!$binicio;        $loops++;    }    $sql="insert into t_torneos_equipos (nom_equipo, id_torneo, user)";    $sql.=" SELECT nom_equipo, $newIdTorneo id_torneo,'".$mail."'  FROM v_equipos_torneo where id_torneo=".$torneo." and id_torneos_equipos!=$oldIdEquipo";    $db->query($sql);    if($db->last_error!="")        die($sql."--".$db->last_error);    //El resto se va metiendo poco a poco en el cron    $sql="insert into t_tareas (tipo_tarea, valor_tarea, estado_tarea)";    $val=$torneo."|".$mail."|".$equipo."|".$newIdTorneo;    $sql.=" values (1,'".$val."',0)";    $db->query($sql);}?>