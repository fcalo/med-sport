<?
include("../session.php");
include("../entity.php");
include("../field.php");
include("triggers.php");
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');


$idEntidad=$_POST["i"];
$entities=unserialize($_SESSION[constant(CT_ENTITIES.PROJECT)]);

//definicion
include("../../config/database.php");

if ($_SESSION[CT_LANGUAGES]){
	$idiomas=$db->get_results("select id_idioma, des_idioma, des_corta from t_idiomas",ARRAY_A);
	$numIdiomas=sizeof($idiomas);
}

foreach($entities as $entity){
	
	//busca en las hijas si tiene
	if ($entity->getIdEntity()!=$idEntidad && $entity->getHasChilds()){
		$find=false;
		foreach($entity->getEntitiesChilds() as $entityChild){
			if ($entityChild->getIdEntity()==$idEntidad){
				$tempEntity=$entityChild;
				$find=true;
			}
			if ($find){
				foreach($entity->getFields() as $field)
				if ($field->getIsKey())
					$parentKey=$field->getName();	
				$entity=$tempEntity;
			}
			$find=false;
		}
		
	}
	if ($entity->getIdEntity()==$idEntidad){
		$primero=true;
		$bIdiomas=$entity->getTableLanguages()!="";
		
		if($entity->getMaintanceType()>=2)
			$listar="S";
		else{
			$listar="N";
			if($entity->getByUser())
				$byUser="S";
			else
				$byUser="N";
		}
		
		if($entity->getLayout()==1)
			$listar="N";
			
		$sql="update ".$entity->getTable()." ";
		if ($bIdiomas)
			for($idi=0;$idi<$numIdiomas;$idi++){
				$sqlIdiomas[$idi]="update ".$entity->getTableLanguages()." ";
				$primeroIdiomas[$idi]=true;
			}
		
		foreach($entity->getFields() as $field){
			if (!$field->getIsKey()){
				if ($field->getIsMultilanguage()){
					for($idi=0;$idi<$numIdiomas;$idi++){
						if ($primeroIdiomas[$idi]){
							$sqlIdiomas[$idi].="set ".$field->getName()."='".$_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']]."'";
							$primeroIdiomas[$idi]=false;
						}else{
							$sqlIdiomas[$idi].=",".$field->getName()."='".$_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']]."'";
						}	
					}
				}else{
					if (!$field->getIsNull() && trim($_POST[$field->getName()])=="")
						die("KO#".$field->getName());
					if (!$field->getIsFile() || $_POST[$field->getName()]!=""){
						if ($primero){
							if ($field->getIsPass())
								$sql.="set ".$field->getName()."='".sha1($_POST[$field->getName()])."'";
							else
								if (strtolower($field->getType())=="date"){
									if($_POST[$field->getName()]!=""){
										$f=explode("/",$_POST[$field->getName()]);
										$sql.="set ".$field->getName()."='".$f[2]."-".$f[1]."-".$f[0]."'";
									}else
										$sql.="set ".$field->getName()."=null";
								}else
									if($_POST[$field->getName()]!="")
										$sql.="set ".$field->getName()."='".str_replace("|||||",chr(13).chr(10),$_POST[$field->getName()])."'";
									else
										$sql.="set ".$field->getName()."=null";
							$primero=false;
						}else{
							if ($field->getIsPass())
								$sql.=",".$field->getName()."='".sha1($_POST[$field->getName()])."'";
							else
								if (strtolower($field->getType())=="date"){
									if($_POST[$field->getName()]!=""){
										$f=explode("/",$_POST[$field->getName()]);
										$sql.=",".$field->getName()."='".$f[2]."-".$f[1]."-".$f[0]."'";
									}else
										$sql.=",".$field->getName()."=null";
								}else
									if($_POST[$field->getName()]!="")
										$sql.=",".$field->getName()."='".str_replace("|||||",chr(13).chr(10),$_POST[$field->getName()])."'";
									else
										$sql.=", ".$field->getName()."=null";
						}	
					}
				}
			}
			else{
				if ($entity->getIsChild()){
                                        $key=$_POST["_key_".$idEntidad];
					$where=" where ".$field->getName()."='".$_POST["_key_".$idEntidad]."'";
                                }else{
                                    $key=$_POST["_key"];
					if($where=="")
						$where=" where ".$field->getName()."='".$_POST["_key"]."'";
					else
						$where=" and ".$field->getName()."='".$_POST["_key"]."'";
				}
			}
			
		}
		if($bIdiomas){
			for($idi=0;$idi<$numIdiomas;$idi++){
				$sqlIdiomas[$idi].=$where." and id_idioma=".$idiomas[$idi]['id_idioma'];
				$db->query($sqlIdiomas[$idi]);
			}
		}
		
		
		$sql.=$where;
		//echo $sql;
		if (strpos($sql,"set"))
			$db->query($sql);
		echo $db->last_error;
                trigger($idEntidad, $key, $db);
		echo "OK#".$idEntidad."#".$_POST[$parentKey]."#".$listar."#".$byUser."#".$_POST["_key_".$idEntidad]."#".$_POST["_key"];
	}
}