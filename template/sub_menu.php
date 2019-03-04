<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target=".navbar-ex1-collapse">
			<span class="sr-only">Desplegar navegación</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="#"><img src="img/iconos/ico_ps2.ico" width="120" height="40" /></a>
	</div>
<div id="menu_principal">
	<div class="collapse navbar-collapse navbar-ex1-collapse">
	<ul id="hmenu"  class="nav navbar-nav">
		<li  class="active"><a href="principal.php">Inicio</a></li>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Archivo <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="javascript:pmenu_am()">Archivos Maestros</a></li>
			</ul>
		</li>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Procesos<b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="javascript:print_pag('form_ticket')">Tickets -- Cola Virtual </a></li>
				<li><a href="javascript:cargarContenidos('php/form_solvencia.php')">Verificar Solvencia</a></li>
				 <li role="presentation" class="divider"></li>
				 <li><a href="javascript:cargarContenidos('php/form_prefac.php')">Pre-Facturación</a></li>
				 <li role="presentation" class="divider"></li>
				<li><a href="javascript:cargarContenidos('php/form_con_cierre_diario.php')">Cierre Diario</a></li>
			</ul>
		</li>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Reportes<b class="caret"></b></a>
		    <ul  class="dropdown-menu">
				<li><a href="javascript:cargarContenidos('php/form_con_cierre_diario.php')">Cierre Diario</a></li>
				<li><a href="javascript:print_pag('form_rpt_pre_ingresos')">Presupuesto de Ingresos</a></li>
				<li><a href="javascript:cargarContenidos('php/form_rpt_rango_cpto.php')">Detallado de Recibos por Cajas</a></li>
				<li><a href="javascript:cargarContenidos('php/form_rpt_rango_cpto.php')">Detallado de Recibos por Conceptos</a></li>
				<li><a href="javascript:cargarContenidos('php/form_rpt_resumen_mensual_gral.php')">Resumen Mensual General</a></li>
				<li><a href="javascript:cargarContenidos('php/form_rpt_resumen_mensual_cpto.php')">Resumen Mensual por Conceptos</a></li>
				<li><a href="javascript:cargarContenidos('php/form_rpt_avance.php')">Avance de Recaudación</a></li>
				<li><a href="javascript:cargarContenidos('php/form_rpt_resumen_diario.php')">Resumen Diario</a></li>
				<li><a href="javascript:cargarContenidos('php/form_rpt_depositos.php')">Depositos</a></li>
				<li><a href="javascript:cargarContenidos('php/form_rpt_depositos2.php')">Depositos por Bancos</a></li>
			   <li><a href="javascript:cargarContenidos('php/form_rpt_resumen_depositos.php')">Resumen Depositos</a></li>
			   <li><a href="javascript:print_pag('rpt_trimestre')">Ingresos Trimestrales</a></li>
            <li><a href="javascript:print_pag('rpt_sb1')">Sabana Mensual de Ingresos por Concepto</a></li>
            <li><a href="javascript:print_pag('rpt_sb2')">Sabana Mensual de Ingresos por Cuentas de Ingresos</a></li>
			</ul>
		</li>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown"><?php echo "Usuario: ".$_SESSION['nom_usu']; ?><b class="caret"></b></a>
			<ul  class="dropdown-menu">
				<li><a href="#">Cambiar Contraseña</a></li>
				<li><a data-toggle='modal' data-target='#acercade' >Acerca de...</a></li>
				<li><a href="php/cerrar_sesion.php"><img src="img/iconos/cs.jpg" height="28" width="28" title="Cerrar Sesión">Cerrar Sesión</a></li>
			</ul>
		</li> 
	</ul>
	</div>
</div>
</nav>