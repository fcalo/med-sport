
function crearXMLHttpRequest() 
{
  var xmlHttp=null;
  if (window.ActiveXObject) 
	xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  else 
	if (window.XMLHttpRequest) 
	  xmlHttp = new XMLHttpRequest();
  return xmlHttp;

}
function loadPlantilla(){
	if(document.getElementById('temporada')!=null){
		conLoad=crearXMLHttpRequest();
		conLoad.onreadystatechange=cbLoadPlantilla;
		if(admin)
			params="e="+equipo;
		else
			params="e=-1";
		params+="&t="+document.getElementById('temporada').value;
		conLoad.open('GET',reurl +'assets/load_plantilla.php?'+params,true);
		conLoad.send(null);
	}

}


function verJugador(idJugador){
	conJugador=crearXMLHttpRequest();
	conJugador.onreadystatechange=cbVerJugador;
	params="j="+idJugador;
	conJugador.open('GET',reurl +'assets/load_jugador.php?'+params,true);
	conJugador.send(null);
}
function cbVerJugador(){
	if(conJugador.readyState == 4)
	{
		document.getElementById("bd-plantilla").innerHTML=conJugador.responseText;
	}
}
function solicitar(id, nombre){
	var html='<div >';
	html='<center><div id="thanks-player-box">Cuando el administrador del equipo confirme tu identidad podr&aacute;s acceder a las ventajas reservadas para los miembros del equipo.<br/><br/>Gracias por registrarte.<br/>';
	html+='<div style="margin-top:10px" id="btn-cancelar-solicitart"></div></div></center>'
	html+='<center><div id="register-player-box">';
	html+='<h2 style="color:#F80">'+nombre+'</h2>';
	html+='<input type="hidden" id="id" value="'+id+'">';
	html+='<div class="login-ln"><div><div class="label">Email</div><input class="login-textarea" type="text" value="" id="mailc" onblur="checkMailSolicitud()" onkeypress="keypressSolicitud(event)" /></div></div><div class="status" id="status_mailc"></div>';
	html+='<div class="login-ln"><div class="label">Contrase&ntilde;a</div><input class="login-textarea" type="password" value="" id="passc" onblur="checkPassSolicitud()" onkeypress="keypressSolicitud(event)"/></div><div class="status" id="status_passc"></div>';
	html+='<div class="login-ln"><div class="label">Repetir Contrase&ntilde;a</div><input class="login-textarea" type="password" value="" id="repassc" onblur="checkRepassSolicitud()" onkeypress="keypressSolicitud(event)"/></div><div class="status" id="status_repassc"></div>';
	html+='<div class="login-ln" style="height:auto;margin-bottom:5px;"><div class="label">Mensaje(opcional)</div><textarea id="mensaje" rows="5" cols="20" onkeypress="keypressSolicitud(event)"></textarea></div>';
	html+='<div style="clear:both;margin-left:50px" ><div style="float:left" id="btn-cancelar-solicitar"></div><div style="float:left" id="btn-solicitar"></div><div id="cortina-solicitud"></div></div>';
	html+='</div></center></div>'
	document.getElementById("bd-plantilla").innerHTML=html;
	var oButtonSol = new YAHOO.widget.Button({ 
		type: "link", 
		id: "button-solicitar", 
		label: "Solicitar identidad", 
		container: "btn-solicitar",
		href: "javascript:enviarSolicitud()"
	});
	var oButtonSolCancelar = new YAHOO.widget.Button({ 
		type: "link", 
		id: "button-cancelar", 
		label: "Cancelar", 
		container: "btn-cancelar-solicitar",
		href: "javascript:loadPlantilla()"
	});
	var oButtonSolCancelart = new YAHOO.widget.Button({ 
		type: "link", 
		id: "button-cancelart", 
		label: "Volver", 
		container: "btn-cancelar-solicitart",
		href: "javascript:loadPlantilla()"
	});
	document.getElementById('mailc').focus();
	
}
function enviarSolicitud(){
	con=crearXMLHttpRequest();
	con.onreadystatechange = cbEnviarSolicitud;
	params="&mailc="+document.getElementById('mailc').value;
	params+="&passc="+document.getElementById('passc').value;
	params+="&repassc="+document.getElementById('passc').value + document.getElementById('repassc').value;
	params+="&msg="+document.getElementById('mensaje').value;
	params+="&id="+document.getElementById('id').value;

	document.getElementById("cortina-solicitud").style.display="block";
	con.open('GET',reurl +'registrar_jugadores.php?'+params,true);
	con.send(null);

}
function cbEnviarSolicitud(){

	if(con.readyState == 4)
	{
		a=con.responseText.split("#");
		campo=a[0];
		if (campo=="OK"){
			document.getElementById("cortina-solicitud").style.display="none";
			document.getElementById("register-player-box").style.display="none";
			document.getElementById("thanks-player-box").style.display="block";
		}else{
			document.getElementById("cortina-solicitud").style.display="none";
			if(a[1]!=null){
				if(a[1]=="OK"){
					document.getElementById(campo).style.backgroundColor="#426c39";
					document.getElementById(campo).style.color="#fff";
					document.getElementById("status_"+campo).innerHTML="";
				}else{
					document.getElementById(campo).style.backgroundColor="#D11919";
					document.getElementById("status_"+campo).innerHTML=a[1];
					document.getElementById(campo).focus();
				}
				
				switch(campo){
					case "passc":
						document.getElementById("status_mailc").innerHTML="";
						break;
					case "repassc":
						document.getElementById("status_mailc").innerHTML="";
						document.getElementById("status_passc").innerHTML="";
						break;

				}
			}else
				alert(a[0]);
			
		}
	}
}

function checkMailSolicitud(){
	checkCampo("mailc", document.getElementById('mailc').value)
}
function checkPassSolicitud(){
	checkCampo("passc", document.getElementById('passc').value)
}
function checkRepassSolicitud(){
	checkCampo("repassc", document.getElementById('passc').value + document.getElementById('repassc').value)
}
var colaCheck=new Array();
var conCheck=null;

function checkCampo(campo, valor){
	if(conCheck==null){
		conCheck=crearXMLHttpRequest();
		conCheck.onreadystatechange=cbCheckCampo;
		params="c="+campo;
		params+="&v="+valor;
		conCheck.open('GET',reurl +'registrar_jugadores.php?'+params,true);
		conCheck.send(null);
	}else{
		var item=new Array(3)
		item[0]=campo;
		item[1]=valor;
		colaCheck[colaCheck.length]=item;
	}
}

function cbCheckCampo(){

	if(conCheck.readyState == 4)
	{
		a=conCheck.responseText.split("#");
		campo=a[0];
		if(a[1]=="OK"){
			document.getElementById(campo).style.backgroundColor="#426c39";
			document.getElementById(campo).style.color="#fff";
			document.getElementById("status_"+campo).innerHTML="";
		}else{
			document.getElementById(campo).style.backgroundColor="#D11919";
			document.getElementById("status_"+campo).innerHTML=a[1];
		}
		conCheck=null;
		if (colaCheck.length>0){
			k=colaCheck.length-1;
			checkCampo(colaCheck[k][0],colaCheck[k][1]);
			colaCheck.splice(k,1);
		}

	}
}

function keypressSolicitud(e){
	
	tecla=(document.all) ? e.keyCode : e.which;
	if(tecla==13) enviarSolicitud();
	
}






function cbLoadPlantilla(){
	if(document.getElementById('bd-plantilla')!=null){
		if(conLoad.readyState == 4)
		{
			document.getElementById("bd-plantilla").innerHTML=conLoad.responseText;
		}
	}
}


function loadResultados(){
	conResultado=crearXMLHttpRequest();
	conResultado.onreadystatechange=cbLoadResultados;
	params="e="+equipo;
	if (admin){
		params+="&a=1";
		
	}else{
		params+="&a=0";
	}
	params+="&r="+document.getElementById('torneo').value;
	params+="&j="+document.getElementById('jornada').value;
	
	conResultado.open('GET',reurl +'assets/load_resultados.php?'+params,true);
	conResultado.send(null);
}
function loadProximos(){
	if (document.getElementById("inicio-right")==null){
		conProximo=crearXMLHttpRequest();
		conProximo.onreadystatechange=cbLoadProximos;
		if (admin){
			params="e="+equipo;
			params+="&a=1";
			
		}else{
			params="e="+equipoTorneo;
			params+="&a=0";
		}
		params+="&r="+document.getElementById('torneo').value;
		conProximo.open('GET',reurl +'assets/load_proximos.php?'+params,true);
		conProximo.send(null);
	}else
		loadProximo();
}
function loadProximo(){
	conProximo=crearXMLHttpRequest();
	conProximo.onreadystatechange=cbLoadProximos;
	if (admin){
		params="e="+equipo;
		params+="&a=1";
		
	}else{
		params="e="+equipoTorneo;
		params+="&a=0";
	}
	params+="&r="+document.getElementById('torneo').value;
	params+="&p=1";
	conProximo.open('GET',reurl +'assets/load_proximos.php?'+params,true);
	conProximo.send(null);
}
function loadLastResultados(){
	conResultado=crearXMLHttpRequest();
	conResultado.onreadystatechange=cbLoadResultados;
	if (admin){
		params="e="+equipo;
		params+="&a=1";
		
	}else{
		params="e="+equipoTorneo;
		params+="&a=0";
	}
        params+="&t="+document.getElementById('temporada').value;
	conResultado.open('GET',reurl +'assets/load_resultados.php?'+params,true);
	conResultado.send(null);
}

var mIdPartido=0;
function verPartido(idPartido,last){
	mIdPartido=idPartido;
	conResultado=crearXMLHttpRequest();
	conResultado.onreadystatechange=cbVerPartido;
	params="p="+idPartido;
	params+="&l="+last;
	conResultado.open('GET',reurl +'assets/load_partido.php?'+params,true);
	conResultado.send(null);
}

var mIdPartidoPrevia=0;
function verPrevia(idPartido){
	mIdPartidoPrevia=idPartido;
	conAsistencia=crearXMLHttpRequest();
	conAsistencia.onreadystatechange=cbVerPrevia;
	params="p="+idPartido;
	conAsistencia.open('GET',reurl +'assets/load_previa.php?'+params,true);
	conAsistencia.send(null);
}


function cbLoadResultados(){
	if(conResultado.readyState == 4)
	{
		document.getElementById("bd-resultados").innerHTML=conResultado.responseText;
	}
}
function cbLoadProximos(){
	if(conProximo.readyState == 4)
	{
		document.getElementById("bd-proximos").innerHTML=conProximo.responseText;
	}
}
function cbVerPartido(){
	if(conResultado.readyState == 4)
	{
		document.getElementById("bd-resultados").innerHTML=conResultado.responseText;
		setBPartido(true)
		setIdComentarios(mIdPartido);
		loadCoreComments();
	}
}
var oButtonGroup =null;
function cbVerPrevia(){
	if(conAsistencia.readyState == 4)
	{
		document.getElementById("bd-proximos").innerHTML=conAsistencia.responseText;
		if (document.getElementById('buttonsasistencia')!=null){
			oButtonGroup = new YAHOO.widget.ButtonGroup("buttonsasistencia");
			oButtonGroup.addListener("checkedButtonChange", saveAsistencia);
		}
		setBPartido(true)
		setIdComentarios(mIdPartidoPrevia);
		loadCoreComments();
	}
}

function saveAsistencia(){
	if(oButtonGroup.getButton(0).get("checked") || oButtonGroup.getButton(1).get("checked")){
		conSaveAsistencia=crearXMLHttpRequest();
		conSaveAsistencia.onreadystatechange=cbSaveAsistencia;
		params="j="+document.getElementById('idplantilla').value;
		params+="&p="+document.getElementById('idpartido').value;
		if(oButtonGroup.getButton(1).get("checked"))
			params+="&a=N";
		else
			params+="&a=S";
		conSaveAsistencia.open('GET',reurl +'assets/save_asistencia.php?'+params,true);
		conSaveAsistencia.send(null);
	}
}
function cbSaveAsistencia(){
	if(conSaveAsistencia.readyState == 4)
	{
		if(conSaveAsistencia.responseText!="OK")
			alert(conSaveAsistencia.responseText);
	}
}
function loadClasificacion(){
	conClasificacion=crearXMLHttpRequest();
	conClasificacion.onreadystatechange=cbLoadClasificacion;
	params="e="+equipo;
	params+="&r="+document.getElementById('torneo').value;
	params+="&j="+document.getElementById('jornada').value;
	conClasificacion.open('GET',reurl +'assets/load_clasificacion.php?'+params,true);
	conClasificacion.send(null);
}

function cbLoadClasificacion(){
	if(conClasificacion.readyState == 4)
	{
		document.getElementById("bd-clasificacion").innerHTML=conClasificacion.responseText;
	}
}



function loadTorneos(){
	conTorneos=crearXMLHttpRequest();
	conTorneos.onreadystatechange=cbLoadTorneos;
	params="e="+equipo;
	params+="&t="+document.getElementById('temporada').value;
	conTorneos.open('GET',reurl +'assets/load_torneos.php?'+params,true);
	conTorneos.send(null);
}

function cbLoadTorneos(){
	if(conTorneos.readyState == 4)
	{
		if(conTorneos.responseText!=""){
			var datos=JSON.parse(conTorneos.responseText);
		    total=datos.length-1;
		    var select=document.getElementById('torneo');
		    select.innerHTML="";
		    for(i=0;i<=total;i++){
		    	select.options[select.options.length] = new Option(datos[i]['v'], datos[i]['k']);
		    }
			if (document.getElementById('bd-proximos')!=null)
				loadProximos();
			else
				if (document.getElementById('bd-clasificacion')!=null)
					loadJornadasClas();
				else
					loadJornadas();
		}
	}
}

function loadJornadas(){
	conJornadas=crearXMLHttpRequest();
	conJornadas.onreadystatechange=cbLoadJornadas;
	params="&r="+document.getElementById('torneo').value;
	conJornadas.open('GET',reurl +'assets/load_jornadas.php?'+params,true);
	conJornadas.send(null);
}
function loadJornadasClas(){
	conJornadas=crearXMLHttpRequest();
	conJornadas.onreadystatechange=cbLoadJornadas;
	params="&r="+document.getElementById('torneo').value;
	conJornadas.open('GET',reurl +'assets/load_jornadas_clasificacion.php?'+params,true);
	conJornadas.send(null);
}


function cbLoadJornadas(){
	if(conJornadas.readyState == 4)
	{
		if(conJornadas.responseText!=""){
			var datos=JSON.parse(conJornadas.responseText);
		    total=datos.length-1;
		    var select=document.getElementById('jornada');
		    select.innerHTML="";
		    for(i=0;i<=total;i++){
		    	select.options[select.options.length] = new Option(datos[i]['v'], datos[i]['k']);
		    }
			if (document.getElementById('bd-resultados')!=null)
				loadResultados();
			else
				loadClasificacion();
		}
	}
}

function vertodas(){
	document.getElementById('imagen-detalle').style.display="none";
	document.getElementById('imagenes').style.display="block";
}
function loadimagen(imagen){
	document.getElementById('imagen-detalle').style.display="block";
	document.getElementById('imagenes').style.display="none";
	document.getElementById('imagen-detalle-imagen').innerHTML="<img src='"+reurl+"admin/"+getThumb(imagen,500)+"'>";
}

function getThumb(path,width){
	var tokens=path.split("/");
	var num=tokens.length;
	var p="";
	for (i=0;i<num-1;i++)
		p+=tokens[i]+"/";
	p+=width+"x/thumb_"+tokens[num-1];
	return p;
}
