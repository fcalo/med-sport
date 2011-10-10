<script type="text/javascript">
YUI({combine: true, timeout: 10000}).use('io', 'history', function(Y) {

    var html, elem, bookmarkedSection, querySection, initSection, navbar;

    if (location.protocol.substr(0, 4) === 'file') {
        document.write('This example cannot be run locally. You must copy it to a web server and access it using HTTP or HTTPS.');
        return;
    }
	
	<?
	if (isset($_GET['t'])){
		$sql="select e.id_equipo, te.nom_equipo, te.id_torneos_equipos from t_equipos e, t_deportes d, t_torneos t, t_torneos_equipos te";
		$sql.=" where d.id_deporte=e.id_deporte";
		//$sql.=" and e.url_equipo='".$_GET['n']."'";
		$sql.=" and d.url_deporte='".$_GET['d']."'";
		$sql.=" and t.id_torneo='".$_GET['t']."'";
		$sql.=" and t.id_equipo=e.id_equipo";
		$sql.=" and ".amigableMySql("te.nom_equipo")."='".$_GET['n']."'";
		$sql.=" and te.id_torneo=t.id_torneo";
		$row=$db->get_row($sql,ARRAY_A);
		$nomEquipo=utf8_encode($row['nom_equipo']);
		$idEquipo=$row['id_equipo'];
		$idEquipoTorneo=$row['id_torneos_equipos'];
		if($row['id_equipo']!=""){
			echo "var equipo=".$row['id_equipo'].";";
		}
		echo "var admin=false;";
	}else{
		if (isset($_GET['k']) && trim($_GET['k'])!=""){
			$idEquipo=$_GET['k'];
			echo "var equipo=".$_GET['k']."";
		}else{
			$sql="select e.id_equipo, e.nom_equipo from t_equipos e, t_deportes d";
			$sql.=" where d.id_deporte=e.id_deporte";
			$sql.=" and e.url_equipo='".$_GET['n']."'";
			$sql.=" and d.url_deporte='".$_GET['d']."'";
			$row=$db->get_row($sql,ARRAY_A);
			$idEquipo=$row['id_equipo'];
			$nomEquipo=utf8_encode($row['nom_equipo']);
			if($row['id_equipo']!=""){
				echo "var equipo=".$row['id_equipo'].";";
				echo "var admin=true;";
			}else{
				echo "var equipo=0;";
				echo "var admin=false;";
			}
				
		}
	}
	?>

    html = [];
    html.push('<div id="hd-nav">');
    html.push('  <div id="nav">');
    html.push('    <ul>');
    html.push('      <li class="first"><a href="?section=inicio"><?=$nomEquipo?></a></li>');
    html.push('      <li><a href="?section=historia">Historia</a></li>');
	html.push('      <li><a href="?section=proximos">Proximos partidos</a></li>');
    html.push('      <li><a href="?section=resultados">Resultados</a></li>');
    html.push('      <li><a href="?section=clasificacion">Clasificaci&oacute;n</a></li>');
    html.push('      <li><a href="?section=plantilla">Plantilla</a></li>');
	<?
	$sql="select count(*) c from t_imagenes i, t_equipos e, t_deportes d";
	$sql.=" where d.id_deporte=e.id_deporte";
	$sql.=" and e.url_equipo='".$_GET['n']."'";
	$sql.=" and d.url_deporte='".$_GET['d']."'";
	$sql.=" and i.user=e.user";
	$row=$db->get_row($sql,ARRAY_A);
	if($row['c']>0){?>
		html.push('      <li><a href="?section=imagenes">Imagenes</a></li>');
	<?}
	$sql="select count(*) c from t_videos i, t_equipos e, t_deportes d";
	$sql.=" where d.id_deporte=e.id_deporte";
	$sql.=" and e.url_equipo='".$_GET['n']."'";
	$sql.=" and d.url_deporte='".$_GET['d']."'";
	$sql.=" and i.user=e.user";
	$row=$db->get_row($sql,ARRAY_A);
	if($row['c']>0){?>
	html.push('      <li ><a href="?section=videos">Videos</a></li>');
	<?}
	$sql="select count(*) c from t_enlace i, t_equipos e, t_deportes d";
	$sql.=" where d.id_deporte=e.id_deporte";
	$sql.=" and e.url_equipo='".$_GET['n']."'";
	$sql.=" and d.url_deporte='".$_GET['d']."'";
	$sql.=" and i.user=e.user";
	$row=$db->get_row($sql,ARRAY_A);
	if($row['c']>0){?>
    html.push('      <li ><a href="?section=enlaces">Enlaces</a></li>');
	<?}?>
	if(admin)
		html.push('      <li class="last"><a href="?section=visitas">Libro de visitas</a></li>');

    html.push('    </ul>');
    html.push('  </div>');
    html.push('</div>');
	html.push('<div id="without-admin"></div>');
    html.push('<div id="bd-nav"></div>');
    //html.push('<div id="ft">YUI Browser History Manager - Simple Navigation Bar Example</div>');

    elem = document.createElement('div');
    elem.id = 'doc';
    elem.className = 'yui-d1';
    elem.innerHTML = html.join('');
    //document.body.appendChild(elem);
	document.getElementById('bd').appendChild(elem);

    // This function does an XHR call to load and display the specified section.
    function loadSection(section) {
		//if(equipo==null)
			
		if(!admin){
			document.getElementById('hd-nav').style.marginBottom="1em";
			document.getElementById('without-admin').innerHTML="Este equipo no tiene administrador &iquest;Eres un integrante? <a href='javascript:document.getElementById(\"administralo\").submit()'>Administralo</a>";
		}
		
		<?if (isset($_GET['t'])){?>
			var url = '<?=$reurl?>assets/' + section + '.php?k=' + equipo + '&tt=<?=$_GET['t']?>',
		<?}else{?>
			var url = '<?=$reurl?>assets/' + section + '.php?k=' + equipo,
		<?}?>
            cfg = {
                on: {
                    success: function (id, o, args) {
                        Y.get('#bd-nav').set('innerHTML', o.responseText);
						
						switch(section){
							case "plantilla":
								loadPlantilla();
								break;
							case "proximos":
								loadTorneos();
								break;
							case "resultados":
								loadTorneos();
								break;
							case "clasificacion":
								loadTorneos();
								break;
							case "inicio":
								loadClasificacion();
								loadPlantilla();
								loadProximo();
								loadLastResultados();
								loadChart();
								break;
							case "visitas":
								setBPartido(false);
								setIdComentarios('<?=$idEquipo?>');
								loadCoreComments();
								break;
								
						}
							
						
                    },

                    failure: function (id, o, args) {
                        // Fallback...
                        var dst = location.href,
                            s = 'section=' + section,
                            r = /(\?|&)section=[^&]*/;

                        if (dst.match(r)) {
                            dst = dst.replace(r, '$1' + s);
                        } else {
                            if (dst.match(/\?.+/)) {
                                dst += '&';
                            } else if (!dst.match(/\?$/)) {
                                dst += '?';
                            }
                            dst += s;
                        }

                        location.href = dst;
                    }
                }
            };

        Y.io(url, cfg);
    }

    function initializeNavigationBar() {
        Y.on('click', function (evt) {
            var el = evt.target;
            while (el.get('id') !== 'nav') {
                if (el.get('nodeName').toUpperCase() === 'A') {
                    evt.preventDefault();
                    section = Y.History.getQueryStringParameter('section', el.get('href')) || 'inicio';
                    if (!Y.History.navigate('navbar', section)) {
                        // Fallback...
                        loadSection(section);
                    }
                    break;
                } else {
                    el = el.get('parentNode');
                }
            }
        }, '#nav');

        currentSection = Y.History.getCurrentState('navbar');
        loadSection(currentSection);
    }

    // The initial section will be chosen in the following order:
    //
    // URL fragment identifier (it will be there if the user previously
    // bookmarked the application in a specific state)
    //
    //         or
    //
    // 'section' URL parameter (it will be there if the user accessed
    // the site from a search engine result, or did not have scripting
    // enabled when the application was bookmarked in a specific state)
    //
    //         or
    //
    // 'home' (default)

    bookmarkedSection = Y.History.getBookmarkedState('navbar');
    querySection = Y.History.getQueryStringParameter('section');
    initSection = bookmarkedSection || querySection || 'inicio';

    // Register the 'navbar' module and subscribe to the 'history:moduleStateChange' event.
    Y.History.register('navbar', initSection).subscribe('history:moduleStateChange', loadSection);

    // Hook to the browser history utility 'history:ready' event to initialize the application.
    Y.History.subscribe('history:ready', initializeNavigationBar);

    // Initialize the browser history utility.
    if (!Y.History.initialize('#yui-history-field', '#yui-history-iframe')) {
        // Fallback...
        loadSection(initSection);
    }
});


function loadChart(){

	YAHOO.widget.Chart.SWFURL = "<?=$reurl?>back/js/yui/build/charts/assets/charts.swf?t=<?=time()?>";
	//revenue and expenses per day 
	
	var posiciones = 
	[ 
		<?
		if (isset($_GET['t'])){
			$idTorneo=$_GET['t'];
			$sql="select count(*) c from t_torneos_equipos where id_torneo=".$idTorneo;
			$rst=$db->get_results($sql,ARRAY_A);
			$totalEquipos=$rst[0]['c']+1;
			
			$sql="select c.jornada, ct.posicion  ";
			$sql.=" from t_clasificaciones c join t_clasificaciones_tabla ct on ct.id_clasificacion=c.id_clasificacion";
			$sql.=" where c.id_torneo=".$idTorneo;
			$sql.=" and ct.id_torneos_equipos=".$idEquipoTorneo;
			$sql.=" order by c.jornada";
		}else{
			$sql="select e.id_temporada, e.temporada from t_temporadas e, t_torneos o where e.id_temporada=o.temporada and o.id_equipo=".$idEquipo;
			$sql.=" order by id_temporada desc;";
			$rs=$db->get_results($sql,ARRAY_A);
			$idTemporada=$rs[0]['id_temporada'];
			$temporada=$rs[0]['temporada'];
			if($idTemporada!=""){
                            $sql="select distinct nom_torneo, id_torneo from t_torneos ";
                            $sql.=" where id_equipo=".$idEquipo;
                            $sql.=" and temporada=".$idTemporada;
                            $rst=$db->get_results($sql,ARRAY_A);
                            $idTorneo=$rst[0]['id_torneo'];

                            $sql="select count(*) c from t_torneos_equipos where id_torneo=".$idTorneo;
                            $rst=$db->get_results($sql,ARRAY_A);
                            $totalEquipos=$rst[0]['c']+1;

                            $sql="select c.jornada, ct.posicion  ";
                            $sql.=" from t_clasificaciones c join t_clasificaciones_tabla ct on ct.id_clasificacion=c.id_clasificacion";
                            $sql.=" where c.id_torneo=".$idTorneo;
                            $sql.=" and ct.id_torneos_equipos=0";
                            $sql.=" order by c.jornada";
                        }
		}
		$rs=$db->get_results($sql,ARRAY_A);
		$count=sizeof($rs);
		for($i=0;$i<$count;$i++){
			$row=$rs[$i];
			?>
			{jornada:"<?=$row['jornada']?>", posicion:<?=$totalEquipos-$row['posicion']?>}, 
		<?}?>
	    /*{jornada:"2", posicion:(<?=$totalEquipos?>-6)}, 
	    {jornada:"3", posicion:(<?=$totalEquipos?>-5)}, 
	    {jornada:"4", posicion:(<?=$totalEquipos?>-2)}*/
	]; 

	//var myDataSource = new YAHOO.util.DataSource(calculateProfits(dailyFinancials));
	var myDataSource = new YAHOO.util.DataSource(posiciones);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
	myDataSource.responseSchema =
	{
		fields:
		[
			"jornada",
			"posicion"
		]
	};
	
		var seriesDef =  
	[ 
	    { displayName: "Posicion", yField: "posicion", style:
			{
				lineColor:0x79839B,
				borderColor:0x79839B,
				fillColor:0xffffff
			}}, 
	]; 
	
	/*var styleDef = 
	{ 
	    xAxis: 
	    { 
	        majorTicks: 
	        { 
	            display:"inside", 
	            length:3, 
	            size:1 
	        }, 
	        minorTicks: 
	        { 
	            display:"inside", 
	            length:2 
	        }, 
	        labelRotation: -90 
	    }, 
	    yAxis: 
	    { 
	        zeroGridLine: 
	        { 
	            size:2, 
	            color:0xff0000 
	        }, 
	        minorTicks:{display:"none"} 
	    } 
	} */
	
formatMyAxiz = function( value )
	{
		return YAHOO.util.Number.format( <?=($totalEquipos)?>-value, 
	    { 
	        decimalPlaces: 0 
	    }); 
	}
	
	formatMxAxiz = function( value )
	{
		if (value==Math.floor(value)){
			return YAHOO.util.Number.format(value, 
			{ 
				decimalPlaces: 0 
			}); 
		}else
			return '';
	}
	
	var mxAxis = new YAHOO.widget.NumericAxis(); 
	mxAxis.minimum = 1; 
	mxAxis.maximum = <?=($totalEquipos-1)*2?>; 
	mxAxis.labelFunction=formatMxAxiz;
	var myAxis = new YAHOO.widget.NumericAxis(); 
	myAxis.minimum = 0; 
	myAxis.maximum = <?=($totalEquipos-1)?>; 
	myAxis.labelFunction=formatMyAxiz;
	//currencyAxis.labelFunction = YAHOO.example.formatCurrencyAxisLabel; 
	
	YAHOO.example.getDataTipText = function( item, index, series ) 
	{ 
		return '<?=$nomEquipo?> pos:'+(<?=($totalEquipos)?>-item[series.yField]); 
	} 
	
	var mychart = new YAHOO.widget.LineChart("chart", myDataSource,
	{
		series: seriesDef,
		xField: "jornada",
		version: "9.0.115",
		xAxis: mxAxis,
		yAxis: myAxis,
		dataTipFunction:YAHOO.example.getDataTipText,
		//only needed for flash player express install
		expressInstall: "assets/expressinstall.swf"
	});

}
</script>