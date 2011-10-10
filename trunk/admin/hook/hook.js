function cbHook(IdEntidad){
	switch(IdEntidad){
		case 20:
			i=0;
			while (document.getElementById("btn-confirmar"+i)!=null){
				if (document.getElementById("btn-denegar"+i)!=null){
					document.getElementById("btn-confirmar"+i).innerHTML="";
					document.getElementById("btn-denegar"+i).innerHTML="";
					var oButtonConfirmar = new YAHOO.widget.Button({ 
						type: "link", 
						id: "button-confirmar", 
						label: "Confirmar", 
						container: "btn-confirmar"+i,
						href: "javascript:confirmarSolicitud("+document.getElementById("id_solicitud"+i).value+")"
					});
					var oButtonDenegar = new YAHOO.widget.Button({ 
						type: "link", 
						id: "button-denegar", 
						label: "Denegar", 
						container: "btn-denegar"+i,
						href: "javascript:denegarSolicitud("+document.getElementById("id_solicitud"+i).value+")"
					});
				}
				i++;
			}	
			
			break;
		case 131,"131":
			document.getElementById("btn_guardar_jugo").innerHTML="";
			var oButtonGuardar = new YAHOO.widget.Button({ 
					type: "link", 
					id: "boton_guardar_jugo", 
					label: "Guardar", 
					container: "btn_guardar_jugo",
					href: "javascript:guardarQuienJugo()"
				});
			break;
		case 133,"133":
			document.getElementById("btn_guardar_asistencia").innerHTML="";
			var oButtonGuardar = new YAHOO.widget.Button({ 
					type: "link", 
					id: "boton_guardar_asistencia", 
					label: "Guardar", 
					container: "btn_guardar_asistencia",
					href: "javascript:guardarAsistencia()"
				});
			break;
	}
}

function confirmarSolicitud(idSolicitud){
	conSolicitud=crearXMLHttpRequest();
	conSolicitud.onreadystatechange=cbConfirmarSolicitud;
	params="i="+idSolicitud;
	params+="&c=1";
	conSolicitud.open('GET','hook/solicitudesX.php?'+params,true);
	conSolicitud.send(null);
}
function cbConfirmarSolicitud(){
	if(conSolicitud.readyState == 4)
		loadHook('solicitudes.php','','Solicitudes Identidad','CDCDCD',19);
}
function denegarSolicitud(idSolicitud){
	conDenegar=crearXMLHttpRequest();
	conDenegar.onreadystatechange=cbDenegarSolicitud;
	params="i="+idSolicitud;
	params+="&c=0";
	conDenegar.open('GET','hook/solicitudesX.php?'+params,true);
	conDenegar.send(null);
}
function cbDenegarSolicitud(){
	if(conDenegar.readyState == 4)
		loadHook('solicitudes.php','','Solicitudes Identidad','CDCDCD',19);
}

var colaGuardarQuien=new Array();;
var conGuardarQuien=null;
function guardarQuienJugo(row){
        if(conGuardarQuien==null){
            if (row==null)
                    row=0;
            if(document.getElementById('participo_'+row)==null){
                    document.getElementById('loader_guardar_jugo').style.visibility="hidden";
                    alert("Se guardaron los datos sobre quien ha participado en el partido");
            }else{
                    //if (document.getElementById('participo_'+row).checked){
                            document.getElementById('loader_guardar_jugo').style.visibility="visible";
                            var idPlantilla=document.getElementById('id_plantillaj_'+row).value;
                            conGuardarQuien=crearXMLHttpRequest();
                            conGuardarQuien.onreadystatechange=cbGuardarQuien;
                            params="row="+row+"&";
                            params+="id_plantilla="+idPlantilla+"&";
                            params+="id_partido="+document.getElementById('id_partido').value+"&";
                            if(document.getElementById('puntos_'+row)!=null)
                                    params+="puntos="+document.getElementById('puntos_'+row).value+"&";
                            if(document.getElementById('t3_'+row)!=null)
                                    params+="t3="+document.getElementById('t3_'+row).value+"&";
                            if(document.getElementById('t2_'+row)!=null)
                                    params+="t2="+document.getElementById('t2_'+row).value+"&";
                            if(document.getElementById('t1_'+row)!=null)
                                    params+="t1="+document.getElementById('t1_'+row).value+"&";
                            if(document.getElementById('faltas_'+row)!=null)
                                    params+="faltas="+document.getElementById('faltas_'+row).value+"&";
                            if(document.getElementById('goles_'+row)!=null)
                                    params+="goles="+document.getElementById('goles_'+row).value+"&";
                            if(document.getElementById('amonestacion_'+row)!=null)
                                    params+="amonestacion="+(document.getElementById('amonestacion_'+row).checked?"S":"N")+"&";
                            if(document.getElementById('expulsion_'+row)!=null)
                                    params+="expulsion="+(document.getElementById('expulsion_'+row).checked?"S":"N")+"&";
                            params+="guardar="+(document.getElementById('participo_'+row).checked?"S":"N")+"&";

                            conGuardarQuien.open('GET','hook/save_quien.php?'+params,true);
                            conGuardarQuien.send(null);
                    //}else
                    //	guardarQuienJugo(row+1);
            }
        }else{
                colaGuardarQuien[colaGuardarQuien.length]=row;

	}
	
}
function cbGuardarQuien(){
	if(conGuardarQuien.readyState == 4){
		var rs=conGuardarQuien.responseText.split("#");
		var row=rs[1];
		var ok=rs[0]=="OK";
		if(ok){
			var existe=document.getElementById('id_plantilla_'+((row*1)+1))!=null;
			if(existe){
                                colaGuardarQuien[colaGuardarQuien.length]=((row*1)+1);
				//guardarQuienJugo((row*1)+1);
                        }else{
				alert("Se guardaron los datos sobre quien ha participado en el partido");
				document.getElementById('loader_guardar_jugo').style.visibility="hidden";
			}
		}else{
			alert("Ocurrio un error guardando quien ha participado.");
			document.getElementById('loader_guardar_jugo').style.visibility="hidden";
		}
                conGuardarQuien=null;
		if (colaGuardarQuien.length>0){
			k=colaGuardarQuien.length-1;
			guardarQuienJugo(colaGuardarQuien[k]);
			colaGuardarQuien.splice(k,1);
		}
        }
		
}


function guardarAsistencia(row){
	if (row==null)
		row=0;
	if(document.getElementById('asistira_'+row)==null){
		document.getElementById('loader_guardar_asistencia').style.visibility="hidden";
		alert("Se guardaron los datos sobre la asistencia al partido");
	}else{
		//if (!document.getElementById('talvez_'+row).checked){
			document.getElementById('loader_guardar_asistencia').style.visibility="visible";
			var idPlantilla=document.getElementById('id_plantilla_'+row).value;
			conAsistencia=crearXMLHttpRequest();
			conAsistencia.onreadystatechange=cbGuardarAsistencia;
			params="row="+row+"&";
			params+="id_plantilla="+idPlantilla+"&";
			params+="id_partido="+document.getElementById('id_partido').value+"&";
			params+="asistencia="+(document.getElementById('asistira_'+row).checked?"S":"N")+"&";
			params+="guardar="+(document.getElementById('talvez_'+row).checked?"N":"S")+"&";
				
			conAsistencia.open('GET','hook/save_asistencia.php?'+params,true);
			conAsistencia.send(null);
		//}else
		//	guardarAsistencia(row+1);
	}
	
}
function cbGuardarAsistencia(){
	if(conAsistencia.readyState == 4)
		var rs=conAsistencia.responseText.split("#");
		var row=rs[1];
		var ok=rs[0]=="OK";
		if(ok){
			var existe=document.getElementById('id_plantilla_'+((row*1)+1))!=null;
			if(existe)
				guardarAsistencia((row*1)+1);
			else{
				alert("Se guardaron los datos sobre la asistencia al partido");
				document.getElementById('loader_guardar_asistencia').style.visibility="hidden";
			}
		}else{
			alert("Ocurrio un error guardando los datos sobre la asistencia al partido.");
			document.getElementById('loader_guardar_asistencia').style.visibility="hidden";
		}
		
}

function clickAsistencia(radio, row){
	radios=new Array("asistira_"+row,"talvez_"+row,"noasistira_"+row);

	for (i=0;i<3;i++){
		if(radio.id!=radios[i])
			document.getElementById(radios[i]).checked=false;
	}
}