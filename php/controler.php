
<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
require_once "procedimientos.php";
$conn=conectarpg();
if($conn){
//llenar combo de periodos en formulario de acceso
	if($_GET['xcontrol_op']=="llenar_cmb_prd"){
		$query="select cod_prd, des_prd from periodo where cod_ente='".$_GET['xcod_ente']."' order by cod_prd desc";
		if($result=pg_query($query)){
			while ($row=pg_fetch_assoc($result)) {
				echo "<option value='".trim($row['cod_prd'])."'>".$row['des_prd']."</option>";
				}
			}
		}
//fin llenar combo de periodos en formulario de acceso

 	if($_POST['xcontrol_op']=="busca_cting1"){
 		$query="select i.cod_spa || ' -*- ' || i.des_spa as dato"
 		." from concepto c "
 		." left join spartida i on i.cod_spa=c.cod_spa"
 		." where c.des='". $_POST['xcpto']."' and c.cod_prd='".$_SESSION['cod_prd']."' and cod_ente='".$_SESSION['cod_ente']."'";
 		$result=pg_query($query);
 		if($result){
 			$row=pg_fetch_assoc($result);
 			echo $row['dato'];
 		}
 	}

 	if($_POST['xcontrol_op']=="act_cpting"){
 		$query="update concepto set cod_spa='".$_POST['xcod_spa']."' "
 		." where des='". $_POST['xp_cpt'] ."' and cod_prd='".$_SESSION['cod_prd']."' and cod_ente='".$_SESSION['cod_ente']."'";
 		if($result=pg_query($query)){
 			echo "1";
 		}else{
 			echo "2";
 		}
 	}

//RUTINA PARA AGREGAR DATOS DE TIPOS DE TRAMITE

if($_GET['xcontrol_op']=="act_tt"){
	$xnsq=$_GET['xsq'];
	$xvsq=$_GET['xvsq'];
		$query="select * from cl002_tipotramite where id_tipo_tramite='".$_GET['xcod']."'";

	$result=pg_query($query);

	if(pg_num_rows($result)<=0){

		$query="insert into cl002_tipotramite (id_tipo_tramite,descripcion,id_priod,id_estatus,secuencia) "
		     ." values ('".$_GET['xcod']."', "
		     ."'".$_GET['xdes']."', "
		     ."'".$_GET['xcb']."', "
		     ."'".$_GET['xce']."', "
		     ."'".$xnsq."')";

		     $xmen="Registro Agregado Satisfactoriamente";
	}else{
		$query="update cl002_tipotramite set descripcion='".$_GET['xdes']."', "
		     ." id_priod='".$_GET['xcb']."', "
		     ." id_estatus='".$_GET['xce']."', "
		     ." secuencia='".$xnsq."' "
		     ." where id_tipo_tramite='".$_GET['xcod']."'";
			 $xmen="Registro Actualizado Satisfactoriamente";
	}

	$query2="select last_value from ".$xnsq;
	if($result2=pg_query($query2)){
		if($result2=pg_query("alter sequence ".$xnsq." restart with ".$xvsq.";")){
			$xmen=$xmen." - Secuencia Actualizada.";
		}else{
			$xmen=$xmen." - Error: Secuencia NO Actualizada!";
		}
	}else{
		if($result2=pg_query("create sequence ".$xnsq." start ".$xvsq.";")){
			$xmen=$xmen." - Secuencia Actualizada.";
		}else{
			$xmen=$xmen." - Error: Secuencia NO Actualizada!";
		}
	}

	if($result1=pg_query($query)){
		echo $xmen;
	}else{
		echo "Error de Manipulación de Datos";
	}
}

//FIN RUTINA PARA AGREGAR DATOS DE TIPOS DE TRAMITE


// RUTINA PARA MOSTRAR NOMBRE DEL CONTRIBUYENTE EN FORM TICKET
if($_GET['xcontrol_op']=="busc_tic_ct"){
	$query="select c.nom_contri, t.des_tpc, t.cod_tpc from contribuyente c"
	     ." left join tip_contri t on c.tip_contri = t.cod_tpc "
		 ." where c.cod_contri='".$_GET['xcod']."'";
	if($result=pg_query($query)){
		$row=pg_fetch_assoc($result);
		echo trim($row['nom_contri'])."#".trim($row['cod_tpc']);
	}else{
		echo "Error de Manipulación de Datos";
	}
}
// RUTINA PARA MOSTRAR NOMBRE DEL CONTRIBUYENTE EN FORM TICKET


 	if($_POST['xcontrol_op']=="refresca_listaconceptos"){

 		echo lista_conceptos();

 	}


 	function lista_conceptos(){
 		$query="select c.*, sp.des_spa from concepto c "
 		." left join spartida sp on sp.cod_spa=c.cod_spa and sp.cod_periodo='".$_SESSION['cod_prd']."'
		where c.cod_prd='".$_SESSION['cod_prd']."' and c.cod_ente='".$_SESSION['cod_ente']."' order by cod";
 		$result=pg_query($query);
 		if($result){
 			echo "<br>";
 			echo "<table class='table table-hover'>";
 			echo "<tr bgcolor='lightsteelblue'>";
 			echo "<th colspan='7'><center>Relación de Conceptos de Cobro con Cuentas Presupuestarias de Ingresos</center></th>";
 			echo "</tr>";
 			echo "<tr bgcolor='lightsteelblue'>";
 			echo "<th>#</th>";
 			echo "<th>Código</th>";
 			echo "<th>Denominación</th>";
 			echo "<th>Serie</th>";
 			echo "<th>Numero</th>";
 			echo "<th>Cuenta Ingresos</th>";
 			echo "<th>Nombre Cuenta</th>";
 			echo "</tr>";
 			$cont=0;
 			while ($row=pg_fetch_assoc($result)) {
 				$cont=$cont+1;
 				echo "<tr>";
 				echo "<td bgcolor='lightsteelblue'>".$cont."</td>";
 				echo "<td>".$row['cod']."</td>";
 				echo "<td>".$row['des']."</td>";
 				echo "<td>".$row['ser']."</td>";
 				echo "<td>".$row['num']."</td>";
 				echo "<td>".$row['cod_spa']."</td>";
 				echo "<td>".$row['des_spa']."</td>";
 				echo "</tr>";
 			}
 			echo "</table>";
 		}
 	}


//RUTINA PARA IMPRIMIR EN PDF CIERRE DIARIO
 	function imp_cd($xfecha){
 		$query="select distinct des, count(*) as nro, sum(neto) as total"
				." from vista_03 "
				." where fecha='".$_GET['xfecha']."'"
				." group by des"
				." order by des";
 		$html="";
 		$html=$html."<div align='center' class='row'>";
  		$html=$html."<table align='center' class='table'><tr><td>Alcaldía de Bolivar estado Táchira</td></tr>";
  		$html=$html."<tr><td>Reporte de Ingresos Diarios: ".$_GET['xfecha']."</td></tr>";
  		$html=$html."<tr><td> <img src='../img/logos/banner.jpg'></td></tr>";
  		if($result=pg_query($query)){
  			$html=$html."<tr>";
  			$html=$html."<td>";
  			 $html=$html."<table class='table'>";
  			 $html=$html."<tr>";
  			 $html=$html."<th> Concepto </th>";
  			 $html=$html."<th> Nro Recibos</th>";
  			 $html=$html."<th> Monto Recaudado (Bs.) </th>";
  			 $html=$html."</tr>";
  			 $acu1=0;
			 $acu2=0;
			 while ($row=pg_fetch_assoc($result)) {
			 	 $html=$html."<tr>";
				 $html=$html."<td>". $row['des'] ."</td>";
				 $html=$html."<td align='right'>". $row['nro'] ."</td>";
				 $html=$html."<td align='right'>". number_format($row['total'],2,',', '.') ."</td>";
				 $html=$html."</tr>";
				 $acu1=$acu1+$row['nro'];
				 $acu2=$acu2+$row['total'];
			 }
			 $html=$html."<tr>";
			 $html=$html."<th>Totales.....</th>";
			 $html=$html."<th align='right'>". $acu1 ."</th>";
			 $html=$html."<th align='right'>". number_format($acu2,2,',', '.') ."</th>";
			 $html=$html."</tr>";
  			 $html=$html."</table>";
  			 $html=$html."</td>";
  			 $html=$html."</tr>";
  			 //PASA A MOSTRAR RESUMEN DE BANCOS
  			$html=$html."<tr><td>Resumen de Bancos";
  			$html=$html."</td></tr>";
  			$html=$html."<tr>";
  			$html=$html."<td>";
  				$html=$html."<table>";
  				$query="select distinct nom_ban, num_cuen, sum(monto) as total, count(*) as cant"
						." from vista_04"
						." where fecha='". $_GET['xfecha'] ."'"
						." group by nom_ban, num_cuen"
						." order by nom_ban, num_cuen";

						$result1=pg_query($query);
						if($result1){
							$html=$html."<tr><td>";
							$html=$html."<table>";
							$html=$html."<tr>";
							$html=$html."<th> Banco </th>";
							$html=$html."<th> N. Cuenta </th>";
							$html=$html."<th> Cant </th>";
							$html=$html."<th> Monto </th>";
							$html=$html."</tr>";
							$acu3=0;
							$acu4=0;
							while ($row1=pg_fetch_assoc($result1)) {
								$acu3=$acu3+$row1['cant'];
								$acu4=$acu4+$row1['total'];
								$html=$html."<tr>";
								$html=$html."<td>". $row1['nom_ban'] ."</td>";
								$html=$html."<td>". $row1['num_cuen'] ."</td>";
								$html=$html."<td>". $row1['cant'] ."</td>";
								$html=$html."<td align='right'>". number_format($row1['total'],2,',', '.') ."</td>";
								$html=$html."</tr>";
							}
							$html=$html."<tr>";
							$html=$html."<th> *-* </th>";
							$html=$html."<th> Totales...</th>";
							$html=$html."<td><b>". $acu3 ."</b></td>";
							$html=$html."<td align='right'><b>". number_format($acu4,2,',', '.') ."</b></td>";
							$html=$html."</tr>";
							$html=$html."<tr>";
							$html=$html."<th colspan='4'>Resumen Por Caja</th>";
							$html=$html."</tr>";
							$query="select a.*,"
       								." (select count(*) from recibos "
       								."  where est='A' and fecha='". $_GET['xfecha'] ."' and cod_caja=a.cod_caja) as activos,"
       								."  (select count(*) from recibos "
       								."    where est='N' and fecha='". $_GET['xfecha'] ."' and cod_caja=a.cod_caja) as nulos"
									." from "
									." 	(select distinct cod_caja,"
            				    	." 	 sum(total) as total"
  									." 	from "
									." 		(select cod_caja, "
									." 			case est"
									." 				when 'A' then neto"
									." 				when 'N' then 0"
									." 			end as total"
									." 		from recibos r	"
									." where r.fecha='". $_GET['xfecha'] ."') as a"
									." group by cod_caja"
									." order by cod_caja) as a";
							$result2=pg_query($query);
							if($result2){
								$html=$html."<tr>";
								$html=$html."<th>Nro. Caja</th>";
								$html=$html."<th>Total Procesado (Bs.)</th>";
								$html=$html."<th>Recibos Activos</th>";
								$html=$html."<th>Recibos Nulos</th>";
								$html=$html."</tr>";
								$acu5=0;
								$acu6=0;
								$acu7=0;
								while ($row2=pg_fetch_assoc($result2)) {
										$html=$html."<tr>";
										$html=$html."<td align='center'>". $row2['cod_caja'] ."</td>";
										$html=$html."<td align='right'>". number_format($row2['total'],2,',','.') ."</td>";
										$html=$html."<td align='center'>". $row2['activos'] ."</td>";
										$html=$html."<td align='center'>". $row2['nulos'] ."</td>";
										$html=$html."</tr>";
										$acu5=$acu5+$row2['total'];
										$acu6=$acu6+$row2['activos'];
										$acu7=$acu7+$row2['nulos'];
								}
								$html=$html."<tr>";
								$html=$html."<strong>";
								$html=$html."<td align='right'>Totales...</td>";
								$html=$html."<td align='right'>". number_format($acu5,2,',','.') ."</td>";
								$html=$html."<td align='right'>". number_format($acu6,0,',','.') ."</td>";
								$html=$html."<td align='right'>". number_format($acu7,0,',','.') ."</td>";
								$html=$html."</strong>";
								$html=$html."</tr>";
							}
							$html=$html."<tr><td colspan='4'>";
							if(($acu2-$acu4)==0){
								$html=$html."<div class='alert alert-success'>";
								$html=$html." Operaciones del Día guardan positiva relación entre la sumatoria procesada y soportes (Depositos y Flujo de Caja)";
								$html=$html."</div>";
							}else{
								$html=$html."<div class='alert alert-danger closed'> Diferencia de procesamiento: ". number_format($acu2-$acu4,2,',', '.') ."</div>";
							}
							$html=$html."</td></tr>";
							$html=$html."</table>";
							$html=$html."</td></tr>";
		}

  				$html=$html."</table>";
  			$html=$html."</td>";
  			$html=$html."</tr>";
  			 //FIN PASA A MOSTRAR RESUMEN DE BANCOS
  		}
  		$html=$html."</table>";
  		$html=$html."</div>";
 		return $html;
 	}
//FIN RUTINA PARA IMPRIMIR EN PDF CIERRE DIARIO

//PRINTAR MENU DE OPCIONES PARA ARCHIVOS MAESTROS
if($_GET['xcontrol_op']=='pmenu_am'){
   session_start();
    ?>
	<center>
	<div class="panel panel-primary" style="width:70%;">
	<div class="panel-heading">
		<h3>CONFIGURACIÓN DE ARCHIVOS MAESTROS</h3>
	</div>
	<div class="panel-body">
	<table width='90%' boder='1' id="tabla1">
	<tr><td colspan='3' align='center' bgcolor='#E6E6E6'>PARAMETRIZACIÓN REIMSER SOFT</td></tr>
	<tr>
	<td><a href="javascript:print_pag('form_entidad')" >Entidad</a></td>
	<td>Periodos Fiscales</td>
	<td><a href="javascript:print_pag('form_dpto')"  >Departamentos</a></td>
	</tr>
	<tr>
	<td><a href="javascript:print_pag('form_bancos')"  >Bancos</a></td>
	<td><a href="javascript:print_pag2('form_cuentasb')" >Cuentas Bancarias</a></td>
	<td>Cajas de Registro</td>
	</tr>
	<tr>
	<td><a href="javascript:print_pag('form_spartida')">Cuentas de Ingresos</a></td>
	<td><a href="javascript:print_pag('form_conceptos')" >Conceptos</a></td>
	<td><a href="javascript:cargarContenidos('php/form_reg_contribuyente.php')">Contribuyentes</a></td>
	</tr>
	<tr>
	<td>Formas de Pago</td>
	<td><a href="javascript:print_pag('form_act_ut')">Unidad Triburaria</a></td>
	<td><a href="javascript:cargarContenidos('php/form_reg_concepto2.php')">Relación de Conceptos de cobro con cuentas del presupuesto de ingresos</a></td>
	</tr>
	<tr><td colspan='3' align='center' bgcolor='#E6E6E6'>PATENTE DE VEHICULOS</td></tr>
	<tr>
	<td>Tipos de Vehiculos</td>
	<td>Marcas</td>
	<td>Usos</td>
	</tr>
	<tr>
	<td>Vehículo por Contribuyentes</td>
	<td></td>
	<td></td>
	</tr>
	<tr><td colspan=3 align='center' bgcolor='#E6E6E6'>PATENTES DE INDUSTRIA Y COMERCIO</td></tr>
	<tr>
	<td>Rubros de Patentes</td>
	<td>Patente por Contribuyentes</td>
	<td></td>
	</tr>
	<tr><td colspan=3 align='center' bgcolor='#E6E6E6'>CONTROL DE COLAS</td></tr>
	<tr>
	<td><a href="javascript:print_pag('form_priod')">Prioridad</a></td>
	<td><a href="javascript:print_pag('form_ttramite')">Tipo de Tramite</a></td>
	<td>Usuarios</td>
	</tr>
	</table>
		<button type="button" onclick="javascript:imprime_2()">Imprimir</button>
	</div>
		</div>
	</center>
<?php }
//PRINTAR MENU DE OPCIONES PARA ARCHIVOS MAESTROS

//LISTADO DE PRIORIDADES

function lista_priod(){

	$query="select id_priod, descripcion, "
		  ." case id_estatus "
		  ." when '1' then 'Activo' "
		  ." when '2' then 'Inactivo' "
		  ." end as estatus, "
		  ." case id_nivel_priod "
		  ." when '1' then 'Primer Orden' "
		  ." when '2' then 'Segundo Orden' "
		  ." when '3' then 'Normal' "
		  ." end as nivel "
		  ." from cl001_prioridad "
		  ." order by id_priod ";
	$result=pg_query($query);
	if($result){
		echo "<table class='table table-hover'>";
		echo "<tr bgcolor='lightsteelblue'>";
		echo "<td>Código</td>";
		echo "<td>Descripción</td>";
		echo "<td>Estatus</td>";
		echo "<td>Nivel</td>";
		echo "</tr>";
		while ($row=pg_fetch_assoc($result)) {
			echo "<tr>";
			$var=$row['id_priod']."#".$row['descripcion']."#".$row['estatus']."#".$row['nivel'];
			?>
			<td onclick="javascript:pasarcod_priod('<?php echo $var; ?>')"> <?php echo $row['id_priod']; ?></td>
			<?php
			echo "<td>".$row['descripcion']."</td>";
			echo "<td>".$row['estatus'].    "</td>";
			echo "<td>".$row['nivel'].      "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
}

//FIN DE LISTADO DE PRIORIDADES

//LISTADO DE TIPOS DE TRAMITES
function lista_tt(){
	$query="select tt.id_tipo_tramite, "
       		."tt.descripcion as tramite, "
       		."trim(tt.id_priod) as id_priod, "
       		."p.descripcion as prioridad, "
       		." tt.id_estatus, "
       		." case tt.id_estatus "
       		."	when '1' then 'Activo'"
       		."	when '2' then 'Inactivo'"
       		."end as estatus, tt.secuencia "
  		." from cl002_tipotramite tt "
		." left join cl001_prioridad p on p.id_priod=tt.id_priod "
		." order by tt.id_tipo_tramite ";
	if($result=pg_query($query)){
	 echo "<table width='70%' class='table table-hover'>";
	 echo "<tr bgcolor='lightsteelblue'>";
	 echo "<td><strong>CÓDIGO</strong></td>";
	 echo "<td><strong>TRAMITE</strong></td>";
     echo "<td><strong>PRIORIDAD</strong></td>";
	 echo "<td><strong>ESTATUS</strong></td>";
	 echo "<td><strong>CONTROL AVANCE</strong></td>";
	 echo "<td><strong>NUMERO</strong></td>";
	 echo "</tr>";
		while ($row=pg_fetch_assoc($result)) {
			$query2="select last_value from ".$row['secuencia'];
			if($result2=pg_query($query2)){
				$row2=pg_fetch_assoc($result2);
				$num=$row2['last_value'];
			}else{
				$num=0;
			}
			$var=$row['id_tipo_tramite']."#".$row['tramite']."#".$row['id_priod']."#".$row['id_estatus']."#".$row['secuencia']."#".$num;
			?>
			<tr onclick="javascript:pasarcod_tt('<?php echo $var; ?>')">
			<?php
			echo "<td>".$row['id_tipo_tramite']."</td>";
			echo "<td>".$row['tramite']."</td>";
			echo "<td>".$row['prioridad']."</td>";
			echo "<td>".$row['estatus']."</td>";
			echo "<td>".$row['secuencia']."</td>";
			echo "<td>".$num."</td>";
			echo "</tr>";
		}
	 echo "</table>";
	}
}
//FIN LISTADO DE TIPOS DE TRAMITES

//procedimiento de busqueda de prioridad
if($_GET['xcontrol_op']=="busca_priod"){
	$query="select * from cl001_prioridad where id_priod='".$_GET['xid_priod']."'";
	if($result=pg_query($query)){
      if(pg_num_rows($result)>0){
      	echo "1";
      }else{
      	echo "2";
      }
	}else{
		echo "3";
	}
}
//fin procedimiento de busqueda de prioridad

//agregar registro de prioridad a la base de datos
if($_GET['xcontrol_op']=="act_priod"){

 if($_GET['xacc']=="agrega"){
  $query="insert into cl001_prioridad values('".$_GET['xid']."',"
  	                                       ."'".$_GET['xdes']."',"
  	                                       ."'".$_GET['xst']."', "
  	                                       ."'".$_GET['xnl']."')";

  $men="Registro Agregado Satisfactoriamente";
 }

 if($_GET['xacc']=="modifica"){
 	$query="update cl001_prioridad set descripcion='".$_GET['xdes']."', "
 	                                ." id_estatus='".$_GET['xst']."',"
 	                                ." id_nivel_priod='".$_GET['xnl']."' "
 	                        		." where id_priod='".$_GET['xid']."' ";

 	$xmen="Registro Actualizado Satisfactoriamente";
 }
 echo $result;
 if($result=pg_query($query)){
 	echo $men;
 }else{
 	echo "Error de Manipulación de Datos...";
 }
}
//fin agregar registro de prioridad a la base de datos

//RUTINA DE BUSQUEDA TIPO DE TRAMITE
if($_GET['xcontrol_op']=='busca_tt'){
	$query="select tt.id_tipo_tramite, "
       		."tt.descripcion as tramite, "
       		."trim(tt.id_priod) as id_priod, "
       		."p.descripcion as prioridad, "
       		." tt.id_estatus, "
       		." case tt.id_estatus "
       		."	when '1' then 'Activo'"
       		."	when '2' then 'Inactivo'"
       		."end as estatus, tt.secuencia "
  		." from cl002_tipotramite tt "
		." left join cl001_prioridad p on p.id_priod=tt.id_priod "
		." where tt.id_tipo_tramite='".$_GET['xcod_tt']."' "
		." order by tt.id_tipo_tramite ";
	if($result=pg_query($query)){
	  $row=pg_fetch_assoc($result);
	  if($result2=pg_query("select last_value from ".$row['secuencia'])){
	  	$row2=pg_fetch_assoc($result2);
	  	$num=$row2['last_value'];
	  }else{
	  	$num=1;
	  }
	  echo $row['tramite']."#".$row['id_priod']."#".$row['id_estatus']."#".$row['secuencia']."#".$num;
	}
}
//FIN RUTINA DE BUSQUEDA TIPO DE TRAMITE

//FORMULARIO PARA REGISTRAR PRIORIDAD
if($_GET['xcontrol_op']=='form_priod'){
	?>
	<center>
	<br>
	<div class="panel panel-primary">
		<div class="panel-heading" align="center">
      		<h3>Registro de Prioridad para Control de Colas</h3>
    	</div>
    	<div class="panel-body">
    		<div class="form-group">
    			<table width="70%">
    				<tr>
    					<td>Código</td>
    					<td> <input type="text" id="xcod_priod" name="xcod_priod" maxlength="3" class="form-control" size="3" required="required" /> </td>
    				</tr>
    				<tr>
    					<td>Descripción: </td>
    					<td> <input type="text" id="xdes_priod" name="xdes_priod" maxlength="50" size="50" class="form-control" required /> </td>
    				</tr>
    				<tr>
    					<td>Estado: </td>
    					<td>
    						<select id="cmb_est" name="cmb_est" class="form-control">
    							<option value="Seleccione...">Seleccione...</option>
    							<option value="1">Activo</option>
    							<option value="0">Inactivo</option>
    						</select>
    					</td>
    				</tr>
    				<tr>
    					<td>Nivel: </td>
    					<td>
    						<select id="cmb_nivel" name="cmb_nivel" class="form-control">
    							<option value="Seleccione...">Seleccione...</option>
    							<option value="1">Primer Orden</option>
    							<option value="2">Segundo Orden</option>
    							<option value="3">Normal</option>
    						</select>
    					</td>
    				</tr>
    				<tr>
    					<td colspan="2" align="center">
    						<br>
    						<button type="button" class="btn btn-info" onclick="javascript:act_priod()">Guardar</button>
    						<button type="button" class="btn btn-success" onclick="javascript:limp_priod()">Cancelar</button>
    					</td>
    				</tr>
    				<tr>
    					<td colspan="2" align="center">
    					<br>
    						<div class="row" id="contenedor1" name="contenedor1">
    							<?php
    								lista_priod();
   							  	?>
    						</div>
    					</td>
    				</tr>
    			</table>
    		</div>
    	</div>
	</div>
	</center>
	<?php
}
//FIN FORMULARIO PARA REGISTRAR PRIORIDAD

//FORMULARIO PARA REGISTRAR TIPOS DE TRAMITES
if($_GET['xcontrol_op']=='form_ttramite'){
	?>
	<center>
	<br>
		<div class="panel panel-primary" style=" width: 70%; ">
			<div class="panel-heading" align="center">
				<h3>Registro de Tipos de Tramites (Control de Colas)</h3>
			</div>
			<div class="panel-body" align="center">
				<div class="form-group">
					<table>
						<tr>
							<td>Código:</td>
							<td> <input type="text"
							            id="xcod_tt"
							            name="xcod_tt"
							            maxlength="3"
							            size="3"
							            class="form-control"
							            onblur="javascript:busca_tt()"
							            onkeyup="javascript:busca_tt_enter()"
							            required
							            placeholder="002 - Ingrese Consecutivo" /> </td>
						</tr>
						<tr>
							<td>Descripción: </td>
							<td> <input type="text" id="xdes_tt" name="xdes_tt" size="50" maxlength="50" class="form-control" required /> </td>
						</tr>
						<tr>
							<td>Prioridad: </td>
							<td>
								<select id="cmb_priod" name="cmb_priod" class="form-control">
									<option value="Seleccione...">Seleccione...</option>
									<?php
									 $query="select * from cl001_prioridad order by id_priod";
									 if($result=pg_query($query)){
									 	while ($row=pg_fetch_assoc($result)) {
									 		echo "<option value='".trim($row['id_priod'])."'>".$row['descripcion']."</option>";
									 	}
									 }
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>Estatus: </td>
							<td>
								<select id="cmb_est" name="cmb_est" class="form-control">
									<option value="Seleccione...">Seleccione...</option>
									<option value="1">Activo</option>
									<option value="2">Inactivo</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>Nombre de Secuencia: </td>
							<td>
								<input type="text" class="form-control" size="40" maxlength="40" id="text_nsq" name="text_nsq" required />
							</td>
						</tr>
						<tr>
							<td>Valor Inicio/Acumulado: </td>
							<td>
								<input type="number" id="text_valsq" class="form-control" />
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<br>
								<button type="button" class="btn btn-info" onclick="javascript:act_tt()">Guardar</button>
								<button type="button" class="btn btn-success" onclick="javascript:limp_tt()">Cancelar</button>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="row" id="contenedor1" name='contenedor1' align="center">
								<br>
									<?php lista_tt(); ?>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</center>
	<?php
}
//FIN DE FORMULARIO PARA REGISTRAR TIPOS DE TRAMITES

//FORMULARIO PARA REGISTRO DE DEPARTAMENTOS

if($_GET['xcontrol_op']=='form_dpto'){
	?>
    <center>
    <br>
		<div class="panel panel-primary" align="center" style=" width: 60%; ">
			<div class="panel-heading" align="center">
				<h3>Registro de Departamentos</h3>
			</div>
			<div class="panel-body" align="center">
				<table>
					<tr>
						<td>Código: </td>
						<td> <input type="text" id="xcod_dpto" name="xcod_dpto" size="3" maxlength="3" class="form-control" required/> </td>
					</tr>
					<tr>
						<td>Nombre: </td>
						<td> <input type="text" id="xnom_dpto" name="xnom_dpto" size="50" maxlength="50" class="form-control" required /> </td>
					</tr>
					<tr>
						<td>Organo / Ente: </td>
						<td>
							<select id="cmb_ente" name="cmb_ente" class="form-control">

							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
						<br>
							<button type="button" class="btn btn-info">button</button>
							<button type="button" class="btn btn-success">button</button>
						<br>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="row well" id="contenedor1" name="contenedor1">

							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</center>
<?php
}
//FIN DE FORMULARIO PARA REGISTRO DE DEPARTAMENTOS

//FORMULARIO PARA OTORGAR TIKETCS
if($_GET['xcontrol_op']=='form_ticket'){
	?>
	<br>
	<center>
	<div class="row">
		<div class="panel panel-primary" style="width: 70%;">
			<div class="panel-heading">
				<h2>Registro de Tickets (Cola Virtual)</h2>
			</div>
			<div class="panel-body">
				<div class="form-group">
				<br>
				<form id="form_ticket" action="" method="">
					<table>
						<tr>
							<td>Código Contribuyente: </td>
							<td>
								<select id="cmbtc2" name="cmbtc2">
									<option value="...">...</option>
									<?php
									$query="select * from tip_contri";
									if($result=pg_query($query)){
										while ($row=pg_fetch_assoc($result)) {
											echo "<option value='".$row['cod_tpc']."'>". trim($row['des_tpc']) ."</option>";
										}
									}
									?>
								</select>
								<input type="text" class="form-control" id="cod_ct" name="cod_ct" onblur="busc_tic_ct()" >
							</td>
						</tr>
						<tr>
							<td>Contribuyente: </td>
							<td>
								<input type="text" maxlength="80" size="80" id="text_n" name="text_n" class="form-control">
							</td>
						</tr>
						<tr>
							<td>Tipo de Tramite</td>
							<td>
								<select id="cmb_tt" name="cmb_tt" class="form-control">
									<option value="Seleccione...">Seleccione...</option>
									<?php
									 $query="select * from cl002_tipotramite order by descripcion";
									 if($result=pg_query($query)){
									 	while ($row=pg_fetch_assoc($result)) {
									 		echo "<option value='".$row['id_tipo_tramite']."'>".$row['descripcion']."</option>";
									 	}
									 }
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
							<br>
								<button type="button"
								        class="btn btn-info"
								        onclick="f_ticket()">Asignar</button>
								<button type="button" class="btn btn-success" onclick="limp_tic_ct()">Cancelar</button>
							</td>
						</tr>
					</table>
					</form>
				</div>
			</div>
		</div>
	</div>
	</center>
	<?php
}
//FIN DE FORMULARIO PARA ORTORGAR TICKETS


//FORM REPORTE POR CUENTA DE INGRESOS
if($_GET['xcontrol_op']=="form_rpt_pre_ingresos"){
	?>
	<div class="row" style=" width:90%; " align="center">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h4>Reporte de Ingresos Por Cuentas Presupuestarias</h4>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<form>
						<table>
						<tr>
							<td>Cta. Ingresos: </td>
							<td>
								<select class="form-control" id="cmb_ing" name="cmb_ing" onblur="desh_ch1()">
									<option value='Seleccione...'>Seleccione...</option>
									<option value="todos">Todas las Cuentas</option>
									<?php
									$query="select cod_spa, des_spa from spartida where cod_periodo='".$_SESSION['cod_prd']."' order by cod_spa";
									if($result=pg_query($query)){
										while ($row=pg_fetch_assoc($result)) {
											echo "<option value='".trim($row['cod_spa'])."'>".$row['cod_spa']."*-*".ucfirst(trim(strtolower($row['des_spa'])))."</option>";
										}
									}
									?>
								</select>
							</td>
							<td> Origen: </td>
							<td>
								<input type="date" id="f1" class="form-control" required placeholder="yyyy-mm-dd"  value="<?php echo date('Y').'-01-01'?>"/>
							</td>
							<td> Final: </td>
							<td>
								<input type="date" id="f2" required class="form-control" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d')?>" />
							</td>
							<td width="20%" align="center">
								<input type="checkbox" id="est_r" />X Recaudar <br>
								<button type="button" class="btn btn-info" onclick="rpt_ing()">Generar-</button>
								<button type="button" class="btn btn-success">Cancelar</button>
							</td>
						</tr>
						<tr>
							<td colspan="7">
								<div class="row" id="contenedor1">

								</div>
							</td>
						</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php
}
//FIN DE REPORTE DE CUENTA DE INGRESOS

//codigo para generar reporte de cuentas de ingresos
if($_GET['xcontrol_op']=="rpt_ing"){
	if($_GET['xcod_ing']=="todos"){
		echo "<br>";
		?>

		<?php
		if($_GET['xest_r']=="N"){
		$query="select coalesce(c.cod_spa,c.cod_spa,'NR') as codigo, "
       			." coalesce(ci.des_spa,ci.des_spa,'Sin Registro o asociación de cuenta presupuestaria de ingresos') as denominacion, "
       			." coalesce(sum(r.neto),sum(r.neto),0) as monto "
				." from recb_deta_1 r "
 				." left join recibos rc on rc.n_recibo=r.n_recibo "
 				." left join concepto c on c.cod=r.cod_c "
 				." left join spartida ci on ci.cod_spa=c.cod_spa "
 				." where rc.est<>'N' and rc.fecha between '".$_GET['xfec_ini']."' and '".$_GET['xfec_fin']."' "
				." and ci.cod_periodo='".$_SESSION['cod_prd']."' and c.cod_prd='".$_SESSION['cod_prd']."' "
				." and c.cod_ente='".$_SESSION['cod_ente']."' "
 				." group by c.cod_spa, ci.des_spa "
 				." order by c.cod_spa, ci.des_spa ";
 		if($result=pg_query($query)){

 			echo "<table class='table table-hover' id='tabla1' name='tabla1'>";
 			echo "<tr bgcolor='lightsteelblue'>";
 			echo "<td>#</td>";
 			echo "<td>Código</td>";
 			echo "<td>Denominación</td>";
 			echo "<td>Monto Liquidado</td>";
 			echo "</tr>";
 			$acu=0;
 			$cont=0;
 			while ($row=pg_fetch_assoc($result)) {
 				$acu=$acu+$row['monto'];
 				$cont=$cont+1;
 				echo "<tr>";
 				echo "<td bgcolor='lightsteelblue'>".$cont."</td>";
 				echo "<td>".$row['codigo']."</td>";
 				echo "<td>".ucfirst($row['denominacion'])."</td>";
 				echo "<td align='right'>".number_format($row['monto'],2,',','.')."</td>";
 				echo "</tr>";
 			}
 			    echo "<tr>";
 			    echo "<td colspan='4' align='right'> Total Liquidado: ".number_format($acu,2,',','.')."</td>";
 			    echo "</tr>";
 			    echo "<tr>";
 			    echo "<td colspan='4' align='center'>";
 			    ?>
 			    	<button class="btn btn-success">Imprimir</button>
 			    	<button type="button" class="btn btn-default" onclick="tableToExcel('tabla1','Presupuesto de Ingresos')">Exportar a Excel</button>
 			    <?php
 			    echo "</td>";
 			    echo "</tr>";
 			echo "</table>";
 		}else{
 			echo "Error de Manipulación de Datos";
 		}
 		}else{
 			 	$query="select * from (select sp.cod_periodo as cod_prd, sp.cod_spa as codigo, "
       				."	sp.des_spa as denominacion, "
       ."	sp.estimado, "
       ."	a.monto as liquidado, "
       ."	sp.estimado-coalesce(a.monto,a.monto,0) as x_recaudar "
       ."	from "
	."	spartida sp "
	."	left join "
	."	(select coalesce(c.cod_spa,c.cod_spa,'NR') as codigo, "
    ."	   			 coalesce(ci.des_spa,ci.des_spa,'Sin Registro o asociación de cuenta presupuestaria de ingresos') as denominacion,"
    ."	   			 coalesce(sum(r.neto),sum(r.neto),0) as monto "
	."				 from recb_deta_1 r "
 	."				 left join recibos rc on rc.n_recibo=r.n_recibo "
 	."				 left join concepto c on c.cod=r.cod_c "
 	."				 left join spartida ci on ci.cod_spa=c.cod_spa "
 	."				 where rc.est<>'N' and rc.fecha between '".$_GET['xfec_ini']."' and '".$_GET['xfec_fin']."' "
	." 																		 and c.cod_ente='".$_SESSION['cod_ente']."' "
        ."                               and  ci.cod_periodo='".$_SESSION['cod_prd']."' and c.cod_prd='".$_SESSION['cod_prd']."' "
 	."				 group by c.cod_spa, ci.des_spa "
 	."				 order by c.cod_spa) as a "
 	."				 on a.codigo=sp.cod_spa and sp.cod_periodo='".$_SESSION['cod_prd']."' order by a.codigo ) as a where cod_prd='".$_SESSION['cod_prd']."' order by codigo";

 			 	if($result=pg_query($query)){
 				echo "<table class='table table-hover' id='tabla1' name='tabla1'>";
 				echo "<tr>";
 				echo "<th colspan='6' align='center'>Presupuesto de Ingresos del ".$_GET['xfec_ini']." al ".$_GET['xfec_fin']."</th>";
 				echo "</tr>";
 				echo "<tr bgcolor='lightsteelblue'>";
 				echo "<td align='center' bgcolor='lightsteelblue'>#</td>";
 				echo "<td align='center'>Código</td>";
 				echo "<td align='center'>Denominación</td>";
 				echo "<td align='center'>Estimado</td>";
 				echo "<td align='center'>Liquidado</td>";
 				echo "<td align='center'>Por Recaudar</td>";
 				echo "</tr>";
 				$acu1=0;
 				$acu2=0;
 				$acu3=0;
 				$cont=0;
 				while ($row=pg_fetch_assoc($result)) {
 					$cont=$cont+1;
 					echo "<tr>";
 					echo "<td bgcolor='lightsteelblue'>".$cont."</td>";
 					echo "<td>".$row['codigo']."</td>";
 					echo "<td>".ucfirst($row['denominacion'])."</td>";
 					echo "<td align='right'>".number_format($row['estimado'],2,',','.')."</td>";
 					echo "<td align='right'>".number_format($row['liquidado'],2,',','.')."</td>";
 					echo "<td align='right'>".number_format($row['x_recaudar'],2,',','.')."</td>";
 					echo "</tr>";
 					$acu1=$acu1+$row['estimado'];
 					$acu2=$acu2+$row['liquidado'];
 					$acu3=$acu3+$row['x_recaudar'];
 				}
 				echo "<tr>";
 				echo "<td colspan='3' align='right'><strong>Totales...</strong></td>";
 				echo "<td align='right'><strong>".number_format($acu1,2,',','.')."</strong></td>";
 				echo "<td align='right'><strong>".number_format($acu2,2,',','.')."</strong></td>";
 				echo "<td align='right'><strong>".number_format($acu3,2,',','.')."</strong></td>";
 				echo "</tr>";
 				echo "<tr>";
 				echo "<td colspan='6' align='center'>";
 				?>
 					<button class="btn btn-success">Imprimir</button>
 					<button type="button" class="btn btn-default" onclick="tableToExcel('tabla1','Presupuesto de Ingresos')">Exportar a Excel</button>
 				<?php
 				echo "</td>";
 				echo "</tr>";
                echo "<tr>
                 <td colspan='6' align='center'>
                    <div class='row'>";
                    ?>
                    <html>

<head>
    <title>Bar Chart</title>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="../js/Chart.bundle.js"></script>
    <style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
    </style>
</head>

<body>
    <div id="container" style="width: 70%;">
        <canvas id="canvas"></canvas>
    </div>

    <script>
        var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        var randomScalingFactor = function() {
            return (Math.random() > 0.5 ? 1.0 : -1.0) * Math.round(Math.random() * 100);
        };
        var randomColorFactor = function() {
            return Math.round(Math.random() * 255);
        };
        var randomColor = function() {
            return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',.7)';
        };

        var barChartData = {
            labels: ["Estimado","Recaudado","Por Recaudar"],
            datasets: [{
                label: 'Cant. (Bs.)',
                backgroundColor: "rgba(160,12,0,0.5)",
                data: [
                    '<?php echo $acu1; ?>',
                    '<?php echo $acu2; ?>',
                    '<?php echo $acu3; ?>'
                ]
            }/*, {
                //hidden: true,
                label: 'Dataset 2',
                backgroundColor: "rgba(151,187,205,0.5)",
                data: [randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor()]
            }, {
                label: 'Dataset 3',
                backgroundColor: "rgba(151,187,205,0.5)",
                data: [randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor()]
            }*/]

        };

        //window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    // Elements options apply to all of the options unless overridden in a dataset
                    // In this case, we are setting the border of each bar to be 2px wide and green
                    elements: {
                        rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Cumplimiento de la Meta de Recaudación'
                    }
                }
            });

        //};
         //window.myBar.update();

    </script>
</body>

</html>

                    <?php
                echo "</div><td><tr>";
 				echo "</table>";
 			}else{
 				echo "Error de Manipulación de Datos...";
 			}
 		}
	}else{
		$query="select r.n_recibo, "
       		."	rc.fecha, rc.cod_contri, ct.nom_contri,"
       		."	c.des as concepto, "
       		."	r.ser, "
       		."	r.num, "
       		."	r.neto "
			."from recb_deta_1 r "
			."	left join recibos rc on r.n_recibo=rc.n_recibo "
			."	left join concepto c on c.cod=r.cod_c "
			."	left join spartida ci on ci.cod_spa=c.cod_spa "
			."  left join contribuyente ct on ct.cod_contri=rc.cod_contri "
			."	where rc.est<>'N' and rc.fecha between '".$_GET['xfec_ini']."' and '".$_GET['xfec_fin']."' and "
			."  c.cod_spa='".$_GET['xcod_ing']."' and ci.cod_periodo='".$_SESSION['cod_prd']."' and c.cod_prd='".$_SESSION['cod_prd']."' "
			." and c.cod_ente='".$_SESSION['cod_ente']."' "
			."  order by rc.fecha desc, rc.n_recibo desc ";
		if($result=pg_query($query)){
			echo "<br>";
			echo "<table class='table table-hover' id='tabla1' name='tabla1'>";
			echo "<tr><td colspan='9'>Reporte Ingresos cuenta: ".$_GET['xcod_ing']." del ".$_GET['xfec_ini']." al ".$_GET['xfec_fin']." | Cantidad de Resultados: ".number_format(pg_num_rows($result),2,',','.')."</td></tr>";
			echo "<tr bgcolor='lightsteelblue'>";
			echo "<td align='center'>#</td>";
			echo "<td>N° Recibo</td>";
			echo "<td>Fecha</td>";
			echo "<td>Cedula / RIF</td>";
			echo "<td>Contribuyente</td>";
			echo "<td>Concepto</td>";
			echo "<td>Serie</td>";
			echo "<td>Control</td>";
			echo "<td>Monto</td>";
			echo "</tr>";
			$cont=0;
			while ($row=pg_fetch_assoc($result)) {
				$cont=$cont+1;
				echo "<tr>";
				echo "<td align='center' bgcolor='lightsteelblue'>".number_format($cont,0,',','.')."</td>";
				echo "<td>".$row['n_recibo']."</td>";
				echo "<td>".$row['fecha']."</td>";
				echo "<td>".$row['cod_contri']."</td>";
				echo "<td>".$row['nom_contri']."</td>";
				echo "<td><font size='2'>".$row['concepto']."</font></td>";
				echo "<td>".$row['ser']."</td>";
				echo "<td>".$row['num']."</td>";
				echo "<td align='right'>".number_format($row['neto'],2,',','.')."</td>";
				echo "</tr>";
			}
				?>
 					<tr><td colspan="9" align="center">
 					<button class="btn btn-success">Imprimir</button>
 					<button type="button" class="btn btn-default" onclick="tableToExcel('tabla1','Presupuesto de Ingresos')">Exportar a Excel</button>
 					</td></tr>
 				<?php
			echo "</table>";
		}else{
			echo "Error de Manipulación de Datos...";
		}
	}

}
//fin de codigo de generar reporte de cuentas de ingresos

//formulario para actualización del valor de la unidad tributaria
if($_GET['xcontrol_op']=='form_act_ut'){
	?>
	<br>
	<br>
	<br>
	<center>
	<table>
		<tr bgcolor="lightsteelblue">
			<td colspan="2">Formulario de Actualización del Valor de la Unidad Tributaria (UT)</td>
		</tr>
		<tr>
			<td>Unidad Tributaria: </td>
			<td>
			<?php
			 $query="select vut from formulas";
			 if($result=pg_query($query)){
			 	$row=pg_fetch_assoc($result);
			 	$vut=$row['vut'];
			 }else{
			 	$vut=0;
			 }
			?>
				<input type="text" id="t_ut" name="t_ut" required value="<?php echo $vut; ?>">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<button type="buntton" class="btn btn-info" onclick="act_vut()">Actualizar</button>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<div class="row" id="contenedor1">

				</div>
			</td>
		</tr>
	</table>
	</center>
	<?php
}
//fin de formulario para la actualización de la unidad tributaria

//metodo para la actualización de la unidad tributaria
if($_GET['xcontrol_op']=="act_vut"){
	$query="update formulas set vut='".$_GET['xvut']."'";
	if($result=pg_query($query)){
		echo "S";
	}else{
		echo "N";
	}
}
//fin del metodo para la actualización de la unidad tributaria


//lista entidades
if($_GET['xcontrol_op']=="lista_entidad"){
	$query="select * from entidad order by cod_ente";
	if($result=pg_query($query)){
		echo "<table class='table table-hover'>";
		echo "<tr bgcolor='lightsteelblue'>";
		echo "<th>#</th>";
		echo "<th>Código</th>";
		echo "<th>Nombre</th>";
		echo "<th>Cuentadante (1)</th>";
		echo "<th>Cuentadante (2)</th>";
		echo "</tr>";
		$acu1=0;
		while ($row=pg_fetch_assoc($result)) {
			$acu1=$acu1+1;
			echo "<tr>";
			echo "<td>".$acu1."</td>";
			echo "<td>".$row['cod_ente']."</td>";
			echo "<td>".$row['nom_ente']."</td>";
			echo "<td>".$row['resp_1']."</td>";
			echo "<td>".$row['resp_2']."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
}
//fin de lista de entidades

//formulario de entidad
if($_GET['xcontrol_op']=="form_entidad"){
	?>
	<br>
	<div class="row" style="width:90%;">
		<div class="panel panel-info">
			<div class="panel-heading" align="center">
				<h2>Registro de Datos de Entidad</h2>
			</div>
			<div class="panel-body" align="center">
				<table>
					<tr>
						<td>Código:</td>
						<td> <input type="text"
						            maxlength="3"
						            size="3"
						            id="xcod_e"
						            name="xcod_e"
						            class="form-control"
						            onblur="busc_entidad()"
						            onkeyup="busc_entidad2()"
						            placeholder="01">
						<!-- <input type="button" value="***" onclick="busc_entidad()"> -->
						</td>
					</tr>
					<tr>
						<td>Nombre: </td>
						<td> <input type="text" maxlength="100" size="100" id="xnom_e" name="xnom_e" class="form-control"> </td>
					</tr>
					<tr>
						<td>Telefono: </td>
						<td> <input type="text" id="xtel1" name="xtel1" maxlength="15" size="15" placeholder="(0276)762-88-33" class="form-control"> </td>
					</tr>
					<tr>
						<td>Dirección:</td>
						<td>
							<textarea id="t_direc" name="t_direc" cols="100" rows="2" placeholder="Escriba Dirección" class="form-control">

							</textarea>
						</td>
					</tr>
					<tr>
						<td>Cuentadante 1:</td>
						<td> <input type="text" maxlength="50" size="50" id="cdt1" name="cdt1" placeholder="Alcalde / Presidente" class="form-control"> </td>
					</tr>
					<tr>
						<td>Cuentadante 2:</td>
						<td> <input type="text" maxlength="50" size="50" id="cdt2" name="cdt2" placeholder="Vide-Presidente / Dir. de Administración" class="form-control"> </td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<button type="button" class="btn btn-info" onclick="act_entidad()">Guardar</button>
							<button type="button" class="btn btn-success" onclick="limp_entidad()">Cancelar</button>
						</td>
					</tr>
				</table>
				<script type="text/javascript">
					document.getElementById("t_direc").value="";
					document.getElementById("xcod_e").focus();
				</script>
			</div>
		</div>
		<br>
		<div class="row" id="contenedor1">
			<script type="text/javascript">
				lista_entidad();
			</script>
		</div>
	</div>
	<?php
}
//fin de formulario de entidad
////RUTINA DE BUSQUEDA DE DATOS DE ENTIDAD
if($_GET['xcontrol_op']=="busc_entidad"){
	$query="select * from entidad where cod_ente='".$_GET['xcod_e']."'";
	if($result=pg_query($query)){
		if(pg_num_rows($result)>0){
			$row=pg_fetch_assoc($result);
			echo trim($row['nom_ente'])."#".trim($row['tel_ente'])."#".trim($row['dirc_ente'])."#".trim($row['resp_1'])."#".trim($row['resp_2']);
		}else{
			echo "####";
		}
	}else{
		echo "Error de Manipulación de De Datos...";
	}
}
////RUNTINA DE BUSQUEDA DE DATOS DE ENTIDAD

//rutina de listado de bancos registradas
if($_GET['xcontrol_op']=="lista_bancos"){
	$query="select * from banco where cod_ente='".$_SESSION['cod_ente']."' order by cod_ban";
	if($result=pg_query($query)){
		echo "<br>";
		echo "<table class='table table-hover'>";
		echo "<tr bgcolor='lightsteelblue'>";
		echo "<th> # </th>";
		echo "<th> Código </th>";
		echo "<th> Banco </th>";
		echo "<th> Telefono </th>";
		echo "</tr>";
		if($result=pg_query($query)){
			$cont=0;
			while ($row=pg_fetch_assoc($result)) {
				$cont=$cont+1;
				$var_comp=$row['cod_ban']."#".$row['nom_ban']."#".$row['tel_ban'];
				?>
				<tr onclick="pasard_bancos('<?php echo $var_comp; ?>')">
				<?php
				echo "<td align='center' bgcolor='lightsteelblue'>".$cont."</td>";
				echo "<td>".$row['cod_ban']."</td>";
				echo "<td>".$row['nom_ban']."</td>";
				echo "<td>".$row['tel_ban']."</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
	}else{
		echo "Error de Manipulación de Datos";
	}
}
//fin de rutinas de listado de bancos registradas

//buscar banco
if($_GET['xcontrol_op']=="busc_bancos"){
	$query="select * from banco where cod_ban='".$_GET['xcodb']."' and cod_ente='".$_SESSION['cod_ente']."'";
	if($result=pg_query($query)){
		$row=pg_fetch_assoc($result);
		echo $row['cod_ban']."#".$row['nom_ban']."#".$row['tel_ban'];
	}else{
		echo "no_encuentra";
	}
}
//fin de buscar banco

//rutina para agregar datos de bancos

if($_GET['xcontrol_op']=="act_bancos"){
	$query="select * from banco where trim(cod_ban)='".trim($_GET['xcodb'])."'";
	if($result=pg_query($query)){

		if(pg_num_rows($result)<=0){

			$query="insert into banco (cod_ban,nom_ban,tel_ban,cod_usu,cod_ente) values "
			." ('".$_GET['xcodb']."',"
				."'".$_GET['xnomb']."',"
				."'".$_GET['xtelb']."',"
				."'".trim($_SESSION['cod_usu'])."',"
				."'".$_SESSION['cod_ente']."')";
            $men="Registro Agregado Satisfactoriamente...";
		}else{
			$query="update banco set nom_ban='".$_GET['xnomb']."', tel_ban='".$_GET['xtelb']."', "
			      ."cod_usu='".$_SESSION['cod_usu']."' "
			." where cod_ban='".$_GET['xcodb']."' and cod_ente='".$_SESSION['cod_ente']."'";
			$men="Registro Actualizado Satisfactoriamente...";
		}
		if(pg_query($query)){
			echo $men;
		}else{
			echo "Error de Manipulación de Datos...";
		}
	}
}

//fin de rutina para agregar datos de bancos


// rutina para guardar datos en tabla de cuenta de ingresos
if($_GET['xcontrol_op']=="act_spartida"){
	$query="select * from spartida where cod_ente='".$_SESSION['cod_ente']."' and "
	     ." cod_periodo='".$_SESSION['cod_prd']."' and "
	     ." cod_spa='".$_GET['xcod']."' ";
	if($result=pg_query($query)){
		if(pg_num_rows($result)<=0){
			$query="insert into spartida (cod_spa,des_spa,estimado,recaudado,cod_ente,cod_periodo,cod_usu,fecha) "
			." values ('".$_GET['xcod']."','".$_GET['xnom']."','".$_GET['xest']."',0,'".$_SESSION['cod_ente']."','".$_SESSION['cod_prd']."','".$_SESSION['cod_usu']."',now())";
			$xmen="Registro Agregado Satisfactoriamente...";
		}else{
			$query="update spartida set des_spa='".$_GET['xnom']."', "
			." estimado='".$_GET['xest']."' "
			." where cod_spa='".$_GET['xcod']."' and "
			." cod_ente='".$_SESSION['cod_ente']."' and "
			." cod_periodo='".$_SESSION['cod_prd']."' ";
			$xmen="Registro Actualizado Satisfactoriamente...";
		}
		if($result=pg_query($query)){
			echo $xmen;
		}else{
			echo "Error de Inserción/Acdualización de Datos...";
		}
	}else{
		echo "Error de Manipulación de Datos";
	}
}
// rutina para guardar datos en tabla de cuenta de ingresos

//FORMULARIO BANCOS

if($_GET['xcontrol_op']=="form_bancos"){
	session_start();
 if($_SESSION['cod_ente']<>""){
	?>
	<div class="row" style="width:80%;" align="center">
	<br>
		<div class="panel panel-info">
			<div class="panel-heading">
				<h2>Registro de Bancos</h2>
			</div>
			<div class="panel-body">
				<table>
				<tr>
					<td>Código: </td>
					<td> <input type="text"
								maxlength="3"
								size="3"
								id="t_cod"
								name="t_cod"
								onblur="busc_bancos()"
								onkeyup="busc_bancos2()"> </td>
				</tr>
				<tr>
					<td>Nombre: </td>
					<td> <input type="text" maxlength="60" size="60" id="t_nom" name="t_nom"> </td>
				</tr>
				<tr>
					<td>Telefono: </td>
					<td> <input type="text" maxlength="15" size="15" id="t_tel1" name="t_tel1"> </td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<button type="button"
						        class="btn btn-primary"
						        onclick="act_bancos()">Guardar</button>
						<button type="button"
								class="btn btn-success"
								onclick="limp_bancos()">Cancelar</button>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="row" id="contenedor1" name="contenedor1">

						</div>
					</td>
				</tr>
				<script type="text/javascript">
				 document.getElementById("t_cod").focus();
				 lista_bancos();
				</script>
				</table>
			</div>
		</div>
	</div>
	<?php
}else{
	echo "Debe Iniciar Sesión nuevamente...";
 }
}
//FIN DE FORMULARIO DE BANCOS

// BUSQUEDA DE CUENTAS DE INGRESOS
if($_GET['xcontrol_op']=="busc_spartida"){

	$query="select cod_spa, des_spa, estimado from spartida "
	." where cod_ente='".$_SESSION['cod_ente']."' and cod_periodo='".$_SESSION['cod_prd']."' "
	." and cod_spa='".$_GET['xcod']."' ";

	 if($result=pg_query($query)){
	 	if(pg_num_rows($result)>0){
	 		$row=pg_fetch_assoc($result);
	 		echo $row['cod_spa']."#".$row['des_spa']."#".$row['estimado'];
	 	}else{
	 		echo "##";
	 	}
	 }

}
// FIN DE BUSQUEDA DE CUENTAS DE INGRESOS

// LISTA DE PARTIDAS DE INGRESOS
if($_GET['xcontrol_op']=="lista_spartida"){
	$query="select cod_spa, des_spa, estimado from spartida "
	." where cod_ente='".$_SESSION['cod_ente']."' and cod_periodo='".$_SESSION['cod_prd']."' ";
	echo "<br><br>";
	echo "<table width='80%' class='table table-hover'>";
	echo "<tr bgcolor='lightsteelblue'>";
	echo "<th align='center'>#</th>";
	echo "<th>Código</th>";
	echo "<th>Denominación</th>";
	echo "<th>Estimado (Bs.)</th>";
	echo "</tr>";
	$result=pg_query($query);
	$cont=0;
	while ($row=pg_fetch_assoc($result)) {
		$cont=$cont+1;
		$var_comp=$row['cod_spa']."#".$row['des_spa']."#".$row['estimado'];
		?>
		<tr onclick="pasard_spartida('<?php echo $var_comp; ?>')">
		<?php
		echo "<td bgcolor='lightsteelblue'>".$cont."</td>";
		echo "<td>".$row['cod_spa']."</td>";
		echo "<td>".$row['des_spa']."</td>";
		echo "<td align='right'>".number_format($row['estimado'],2,',','.')."</td>";
		echo "</tr>";
	}
	echo "</table>";
}
// FIN LISTA DE PARTIDAS DE INGRESOS

// FORMULARIO CUENTAS DE INGRESOS
if($_GET['xcontrol_op']=="form_spartida"){
 if($_SESSION['cod_ente']!=""){

	?>
	<br>
	<div class="row" align="center">
		<div class=" panel panel-primary" style="width:70%;">
			<div class="panel-heading">
				<h2>Registro de Cuentas Presupuestarias de Ingresos</h2>
			</div>
			<div class="panel-body">
				<table>
					<tr>
						<td>Código: </td>
						<td>
							<input type="text" maxlength="32" size="32" id="t_cod" name="t_cod"
							       onblur="busc_spartida()">
						</td>
					</tr>
					<tr>
						<td>Denominación: </td>
						<td>
							<input type="text" maxlength="80" size="80" id="t_nom" name="t_nom" >
						</td>
					</tr>
					<tr>
						<td>Estimado: </td>
						<td>
							<input type="text" id="t_estimado" name="t_estimado" >
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<br>
							<button type="button"
							        class="btn btn-primary"
							        onclick="act_spartida()">Guardar</button>
							<button type="button"
									class="btn btn-success"
									onclick="limp_spartida()">Cancelar</button>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div id="contenedor1" class="row">

							</div>
							<script type="text/javascript">
								lista_spartida();
							</script>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<?php
  }else{
  	echo "Debe Iniciar Sesión Nuevamente";
  }
}
// FIN DE CUENTAS DE INGRESOS

if($_GET['xcontrol_op']=="act_conceptos"){
	$query="select * from concepto where cod='".$_GET['cod']."' and cod_prd='".$_SESSION['cod_prd']."' and cod_ente='".$_SESSION['cod_ente']."'";
	if($result=pg_query($query)){
		if(pg_num_rows($result)<=0){
			$query="insert into concepto(cod_prd,cod,ser,num,des,tip,cod_dpto,v1,v2,v3,v4,v5,v6,v7,v8,cod_ente,cod_usu,cod_spa,fecha,ut) values "
			." ('".$_SESSION['cod_prd'] ."', "
			."'".$_GET['cod']."', "
			." '".$_GET['ser']."',"
			."'".$_GET['num']."',"
			."'".$_GET['nom']."',"
			."'".$_GET['tip']."',"
			."'".$_GET['dpto']."',"
			."'".$_GET['t1']."',"
			."'".$_GET['t2']."',"
			."'".$_GET['t3']."',"
			."'".$_GET['t4']."',"
			."'".$_GET['t5']."',"
			."'".$_GET['t6']."',"
			."'".$_GET['t7']."',"
			."'".$_GET['t8']."',"
			."'".$_SESSION['cod_ente']."',"
			."'".$_SESSION['cod_usu']."',"
			."'".$_GET['c_ing']."',now(),"
            ."'".$_GET['ut'].")";
            $xmen="Registro Agregado Satisfactoriamente...";
		}else{
			$query="update concepto set ser='".$_GET['ser']."', num='".$_GET['num']."', "
			." des='".$_GET['nom']."', tip='".$_GET['tip']."', cod_dpto='".$_GET['dpto']."', "
			." v1='".$_GET['t1']."', "
			." v2='".$_GET['t2']."', "
			." v3='".$_GET['t3']."', "
			." v4='".$_GET['t4']."', "
			." v5='".$_GET['t5']."', "
			." v6='".$_GET['t6']."', "
			." v7='".$_GET['t7']."', "
			." v8='".$_GET['t8']."', "
            ." ut='".$_GET['ut']."', "
			." cod_spa='".$_GET['c_ing']."', "
			." fecha=now(), cod_usu='".$_SESSION['cod_usu']."' "
			." where cod='".$_GET['cod']."' and cod_ente='".$_SESSION['cod_ente']."' and cod_prd='".$_SESSION['cod_prd']."'";
			$xmen="Registro Actualizado Satisfactoriamente...";
		}
		if(pg_query($query)){
			echo $xmen;
		}else{
			echo "Error de Inserción / Actualización de Datos...";
		}
	}else{
		echo "Error de Manipulación de Datos...";
	}
}

// BUSQUEDA DE CONCEPTOS
if($_GET['xcontrol_op']=="busc_conceptos"){
	$query="select * from concepto where cod='".$_GET['xcod']."' and cod_prd='".$_SESSION['cod_prd']."' and cod_ente='".$_SESSION['cod_ente']."'";
	if($result=pg_query($query)){
		if(pg_num_rows($result)>0){
			$row=pg_fetch_assoc($result);
			echo "1#".$row['cod']."#".$row['des']."#".$row['ser']."#".$row['num']."#".$row['tip']."#".trim($row['cod_dpto'])."#"
			.$row['v1']."#".$row['v2']."#".$row['v3']."#".$row['v4']."#".$row['v5']."#".$row['v6']."#".$row['v7']."#".$row['v8']."#".trim($row['cod_spa']."#".$row['ut']);
		}else{
			echo "2#";
		}
	}else{
		echo "3#";
	}
}
// FIN DE BUSQUEDAS DE CONCEPTOS

//listado de conceptos

if($_GET['xcontrol_op']=="lista_conceptos"){
	echo "<br>";
	$query="select c.cod, c.des, c.ser, c.num,
       case c.tip
        when '01' then 'Tributo'
        when '02' then 'Servicio'
       end as tip,
       c.cod_spa,
       d.nom_dpto
  from concepto c
  left join dpto d on c.cod_dpto=d.cod_dpto and c.cod_ente=d.cod_ente
  where c.cod_ente='".$_SESSION['cod_ente']."' and c.cod_prd='".$_SESSION['cod_prd']."'";
  if($result=pg_query($query)){
  	echo "<table id='tabla1' class='table table-hover'>";
  	echo "<tr bgcolor='lightsteelblue'>";
  	echo "<th>#</th>";
  	echo "<th>Código</th>";
  	echo "<th>Denominación</th>";
  	echo "<th>Serie</th>";
  	echo "<th>Control</th>";
  	echo "<th>Tipo</th>";
  	echo "<th>Cuenta Ingresos</th>";
  	echo "<th>Departamento Responsable</th>";
  	echo "</tr>";
  	$cont=0;
  	while($row=pg_fetch_assoc($result)){
  		$cont=$cont+1;?>
  		<tr onclick="pasar_conceptos('<?php echo trim($row['cod']); ?>')">
  		<?php
  		echo "<td bgcolor='lightsteelblue'>".$cont."</td>";
  		echo "<td>".$row['cod']."</td>";
  		echo "<td>".strtoupper($row['des'])."</td>";
  		echo "<td>".$row['ser']."</td>";
  		echo "<td>".$row['num']."</td>";
  		echo "<td>".$row['tip']."</td>";
  		echo "<td>".$row['cod_spa']."</td>";
  		echo "<td>".$row['nom_dpto']."</td>";
  		echo "</tr>";
  	}
  	echo "</table>";
  	?>
  	<br>
  	<center>
  	<button type="button"
  	        class="btn btn-success"
  	        onclick="tableToExcel('tabla1','Conceptos de Cobro')">Exportar a Excel</button>
  	</center>
  	<?php
  }
}

//fin listado de conceptos

// FORMULARIO DE CONCEPTOS
if($_GET['xcontrol_op']=="form_conceptos"){
	?>
	<br>
	<center>
	<div class="row" style="width:80%;">
		<div class="panel panel-primary">
			<div class="panel-heading" align="center">
				<h2>Registro de Conceptos de Cobro</h2>
			</div>
			<div class="panel-body">
				<center>
					<table>
						<tr>
							<td>Código: </td>
							<td> <input type="text"
							             maxlength="6"
							             size="6"
							             id="t_cod"
							             name="t_cod"
							             onblur="busc_conceptos()"> </td>
						</tr>
						<tr>
							<td>Serie: </td>
							<td> <input type="text" maxlength="2" size="2" id="t_ser" name="t_ser"> </td>
						</tr>
						<tr>
							<td>Control: </td>
							<td> <input type="number" id="t_num" name="t_num"> </td>
						</tr>
						<tr>
							<td>Denominación:</td>
							<td>  <input type="text" id="t_nom" name="t_nom" maxlength="80" size="80"> </td>
						</tr>
						<tr>
							<td>Tipo:</td>
							<td>
								<select class="form-control" id="t_tip" name="t_tip">
									<option value="Seleccione...">Seleccione...</option>
									<option value="01">Tributo</option>
									<option value="02">Servicio</option>
								</select>
							</td>
						</tr>
                        <tr>
                            <td>Unidades Tributarias: </td>
                            <td>
                                <input type="text" id="ut" size="8" maxlength="8" name="ut" />
                            </td>
                        </tr>
						<tr>
							<td>Departamento:</td>
							<td>
								<select class="form-control" id="t_dpto" name="t_dpto">
									<option value="Seleccione...">Seleccione...</option>
									<?php
									$query="select cod_dpto, nom_dpto from dpto "
									." where cod_ente='".$_SESSION['cod_ente']."' order by nom_dpto";
									if($result=pg_query($query)){
										while ($row=pg_fetch_assoc($result)) {
											echo "<option value='".trim($row['cod_dpto'])."'>".$row['nom_dpto']."</option>";
										}
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>Titulo (1):</td>
							<td>  <input type="text" id="t_tit1" name="t_tit1" maxlength="30" size="30"> </td>
						</tr>
						<tr>
							<td>Titulo (2):</td>
							<td>  <input type="text" id="t_tit2" name="t_tit2" maxlength="30" size="30"> </td>
						</tr>
						<tr>
							<td>Titulo (3):</td>
							<td>  <input type="text" id="t_tit3" name="t_tit3" maxlength="30" size="30"> </td>
						</tr>
						<tr>
							<td>Titulo (4):</td>
							<td>  <input type="text" id="t_tit4" name="t_tit4" maxlength="30" size="30"> </td>
						</tr>
						<tr>
							<td>Titulo (5):</td>
							<td>  <input type="text" id="t_tit5" name="t_tit5" maxlength="30" size="30"> </td>
						</tr>
						<tr>
							<td>Titulo (6):</td>
							<td>  <input type="text" id="t_tit6" name="t_tit6" maxlength="30" size="30"> </td>
						</tr>
						<tr>
							<td>Titulo (7):</td>
							<td>  <input type="text" id="t_tit7" name="t_tit7" maxlength="30" size="30"> </td>
						</tr>
						<tr>
							<td>Titulo (8):</td>
							<td>  <input type="text" id="t_tit8" name="t_tit8" maxlength="30" size="30"> </td>
						</tr>
						<tr>
							<td>Cuenta de Ingresos: </td>
							<td>
								<select id="t_ing" name="t_ing" class="form-control">
									<option value="Seleccione...">Seleccione...</option>
									<?php
									$query="select cod_spa, des_spa from spartida "
									." where cod_ente='".$_SESSION['cod_ente']."' and cod_periodo='".$_SESSION['cod_prd']."' "
									." order by cod_spa";
									if($result=pg_query($query)){
										while ($row=pg_fetch_assoc($result)) {
											echo "<option value='".trim($row['cod_spa'])."'>".$row['cod_spa']." *-* ".ucfirst(strtolower(trim($row['des_spa'])))."</option>";
										}
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<br>
								<button type="button"
								        class="btn btn-primary"
								        onclick="act_conceptos()">Guardar</button>
								<button type="button"
								        class="btn btn-success"
								        onclick="limp_conceptos()">Cancelar</button>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="row" id="contenedor1" name="contenedor1">

								</div>
								<script type="text/javascript">
									lista_conceptos();
								</script>
							</td>
						</tr>
					</table>
				</center>
			</div>
		</div>
	</div>
	</center>
	<?php
}
// FIN DE FORMULARIO DE CONCEPTOS

//datos para ticket
if($_GET['xcontrol_op']=="asigna_ticket"){

	$query="select cod_contri, nom_contri from contribuyente where cod_contri='".$_GET['xcodt']."'";
	if($result=pg_query($query)){
      if(pg_num_rows($result)>0){
      	$row=pg_fetch_assoc($result);
      	$contribuyente="Código: ".$_GET['xcodt']."<br> Nombre/Razón Social: ".$row['nom_contri'];
      }else{
      	$contribuyente="Contribuyente (".$_GET['xcodt'].") No Registrado";
      }
	}else{
		$contribuyente="Error de Consulta de Datos de Contribuyente";
	}
	$query="select descripcion, secuencia from cl002_tipotramite where id_tipo_tramite='".$_GET['xctt']."'";
	if($result=pg_query($query)){
		if(pg_num_rows($result)>0){
			$row=pg_fetch_assoc($result);
			$tramite="Tipo Tramite: <strong>".$row['descripcion']."</strong>";

			$query2="SELECT nextval('".$row['secuencia']."') as numero";
			if($result2=pg_query($query2)){
				$row2=pg_fetch_assoc($result2);
				$num="<b> Numero: ".$row2['numero'];
			}

		}else{
			$tramite="Tipo de Tramite no Encontrado...";
			$num="<br>Numero: ********";
		}
	}else{
		$tramite="Error de Consulta de Datos de Tipo de Tramites";
		$num="<br>Numero: ********";
	}
	echo $contribuyente."#".$tramite."#".$num;

}
//fin de datos para ticket

//emerge ticket
if($_GET['xcontrol_op']=="ticket"){

	?>
	<!DOCTYPE html>
	<html lang="es">
	<head>
		<meta charset="utf-8">
		<title>Cola Virtual</title>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/bootstrap.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="../css/menu_p.css">
	</head>
	<body>
	<?php
	echo "<center>";
	echo "<br>";
    echo "<div id='recibo_ticket'>";
	echo "<table border='0'>";
	echo "<tr>";
	echo "<td>Alcaldía de Miranda Edo. Carabobo</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".$_GET['contri']."</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".$_GET['tramite']."</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".$_GET['xnum']."</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "</center>";

	?>
	<br>
	<center>
	<button class="btn btn-default" onclick="imprimir_recb()"><strong>Imprimir Ticket</strong></button>
	</center>
	</body>
	</html>

	<?php

}
//fin emerge ticket

//LISTADO DE INGRESOS POR TRIMESTRES
if($_GET['xcontrol_op']=="rpt_trimestre"){
	$query="select * from trimestres('".$_SESSION['cod_prd']."',to_char(date_part('year',now()),'9999'))";
	$result=pg_query($query);
	echo "<div class='row' style='width:95%;'>";
	echo "<div class='panel-body panel'>";
	echo "<table class='table table-hover table-striped' id='tabla1'>";
	echo "<tr><td bgcolor='lightsteelblue' colspan='10' align='center'><strong>REPORTE DE INGRESOS TRIMESTRAL</strong></td></tr>";
	echo "<tr>";
	echo "<th>Código</th>";
	echo "<th>Denominación</th>";
	echo "<th>Trimestre 1</th>";
	echo "<th>Nro. T1</th>";
	echo "<th>Trimestre 2</th>";
	echo "<th>Nro. T2</th>";
	echo "<th>Trimestre 3</th>";
	echo "<th>Nro. T3</th>";
	echo "<th>Trimestre 4</th>";
	echo "<th>Nro. T4</th>";
	echo "</tr>";
	while ($row=pg_fetch_assoc($result)) {
	echo "<tr>";
	echo "<td>".$row['cod_spa']."</td>";
	echo "<td>".$row['des_spa']."</td>";
	echo "<td align='right'>".$row['trimestre1']."</td>";
	echo "<td align='center'>".$row['nro_trimestre1']."</td>";
	echo "<td align='right'>".$row['trimestre2']."</td>";
	echo "<td align='center'>".$row['nro_trimestre2']."</td>";
	echo "<td align='right'>".$row['trimestre3']."</td>";
	echo "<td align='center'>".$row['nro_trimestre3']."</td>";
	echo "<td align='right'>".$row['trimestre4']."</td>";
	echo "<td align='center'>".$row['nro_trimestre4']."</td>";
	echo "</tr>";
	}
	echo "<tr>";
	?>
	<td colspan='10' align='center'>
	<button  onclick="tableToExcel('tabla1', 'Resumen de Ingresos Trimentral')">Exportal a Excel</button></td>
	<?php
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "</div>";
}
//FIN DE LISTADO DE INGRESOS POR TRIMESTRES

if($_GET['xcontrol_op']=="rpt_sb1"){
    echo "<div id='contendor_p1' class='row' style='background-color:white; width:97%'>";
        echo "<form>";
        echo "<table id='tabla1'>";
            echo "<tr>";
            echo "<td>Año:</td>";
            echo "<td>";
            echo "<select id='cmb_agno' class='form-control'>";
            llena_cmb_agnos();
            echo "</select>";
            echo "</td>";
            echo "<td>";
            echo "<button type='button' class='btn btn-default' onclick='rpt_sb1()'>Ver...</button>";
            echo "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td colspan='3' align='center'><div id='contenedor_2' style='width: 90%;
                                                                overflow: auto;
                                                                scrollbar-base-color:#ffeaff;'>
                                                                </div></td>";
            echo "</tr>";
        echo "</table>";
        echo "</form>";
    echo "</div>";
}

if($_GET['xcontrol_op']=="prpt_sb1"){
     $query="select * from rptxmescp1(".$_GET['xc_agno'].",'".$_SESSION['cod_prd']."') ";
     if($result=pg_query($query)){
         echo "<table id='tabla1' class='table table-striped table-hover'>";
         echo "<tr><td colspan='16' align='center'><strong>Reporte de Ingresos por Conceptos de Cobro y Por Mes Año: ".$_GET['xc_agno']."</strong></td></tr>";
         echo "<tr>";
         echo "<td align='center'>Cod_spa</td>";
         echo "<td align='center'>Cod_cpt</td>";
         echo "<td align='center' width='25%'>Descripción</td>";
         echo "<td align='center'>Enero</td>";
         echo "<td align='center'>Febrero</td>";
         echo "<td align='center'>Marzo</td>";
         echo "<td align='center'>Abril</td>";
         echo "<td align='center'>Mayo</td>";
         echo "<td align='center'>Junio</td>";
         echo "<td align='center'>Julio</td>";
         echo "<td align='center'>Agosto</td>";
         echo "<td align='center'>Septiembre</td>";
         echo "<td align='center'>Octubre</td>";
         echo "<td align='center'>Noviembre</td>";
         echo "<td align='center'>Diciembre</td>";
         echo "<td align='center'>Total</td>";
         echo "</tr>";
         $enero=0;
         $febrero=0;
         $marzo=0;
         $abril=0;
         $mayo=0;
         $junio=0;
         $julio=0;
         $agosto=0;
         $septiembre=0;
         $octubre=0;
         $noviembre=0;
         $diciembre=0;
         $total=0;
         while($row=pg_fetch_assoc($result)){
             $enero=$enero+$row['enero'];
             $febrero=$febrero+$row['febrero'];
             $marzo=$marzo+$row['marzo'];
             $abril=$abril+$row['abril'];
             $mayo=$mayo+$row['mayo'];
             $junio=$junio+$row['junio'];
             $julio=$julio+$row['julio'];
             $agosto=$agosto+$row['agosto'];
             $septiembre=$septiembre+$row['septiembre'];
             $octubre=$octubre+$row['octubre'];
             $noviembre=$noviembre+$row['noviembre'];
             $total=$total+$row['total'];
             echo "<tr>";
             echo "<td style='font-seze:8px;'>".$row['cod_spa']   ."</td>";
             echo "<td style='font-seze:8px;'>".$row['cod']       ."</td>";
             echo "<td style='font-seze:8px;'>".$row['des']       ."</td>";
             echo "<td align='right'>".number_format($row['enero'] ,2,',','.')     ."</td>";
             echo "<td align='right'>".number_format($row['febrero'] ,2,',','.')   ."</td>";
             echo "<td align='right'>".number_format($row['marzo'] ,2,',','.')     ."</td>";
             echo "<td align='right'>".number_format($row['abril'] ,2,',','.')     ."</td>";
             echo "<td align='right'>".number_format($row['mayo'] ,2,',','.')      ."</td>";
             echo "<td align='right'>".number_format($row['junio'] ,2,',','.')     ."</td>";
             echo "<td align='right'>".number_format($row['julio'] ,2,',','.')     ."</td>";
             echo "<td align='right'>".number_format($row['agosto'] ,2,',','.')    ."</td>";
             echo "<td align='right'>".number_format($row['septiembre'] ,2,',','.')."</td>";
             echo "<td align='right'>".number_format($row['octubre'] ,2,',','.')   ."</td>";
             echo "<td align='right'>".number_format($row['noviembre'] ,2,',','.') ."</td>";
             echo "<td align='right'>".number_format($row['diciembre'] ,2,',','.') ."</td>";
             echo "<td align='right'>".number_format($row['total'] ,2,',','.')     ."</td>";
             echo "</tr>";
         }
             echo "<tr>
                   <td></td>
                   <td></td>
                   <td></td>
                   <td align='right'>". number_format($enero,2,',','.')       . "</td>
                   <td align='right'>". number_format($febrero,2,',','.')     . "</td>
                   <td align='right'>". number_format($marzo,2,',','.')       . "</td>
                   <td align='right'>". number_format($abril,2,',','.')       . "</td>
                   <td align='right'>". number_format($mayo,2,',','.')        . "</td>
                   <td align='right'>". number_format($junio,2,',','.')       . "</td>
                   <td align='right'>". number_format($julio,2,',','.')       . "</td>
                   <td align='right'>". number_format($agosto,2,',','.')      . "</td>
                   <td align='right'>". number_format($septiembre,2,',','.')  . "</td>
                   <td align='right'>". number_format($octubre,2,',','.')     . "</td>
                   <td align='right'>". number_format($noviembre,2,',','.')   . "</td>
                   <td align='right'>". number_format($diciembre,2,',','.')   . "</td>
                   <td align='right'>". number_format($total,2,',','.')       . "</td>
                   </tr>
                   ";
         echo "<tr>";
         echo "<td colspan='16' align='center'>";
         ?>
            <button type="button" class="btn btn-default" onclick="tableToExcel('tabla1','Presupuesto de Ingresos')">Exportar a Excel</button>
        <?php
         echo "</td>";
         echo "</tr>";
         echo "</table>";
     }else{
         echo "Error de Aplicación de Consulta";
     }
  }

if($_GET['xcontrol_op']=="rpt_sb2"){
    echo "<div id='contendor_p1' class='row' style='width: 90%;
                                                    overflow: auto;
                                                    scrollbar-base-color:#ffeaff;'>";
        echo "<form>";
        echo "<table id='tabla1'>";
            echo "<tr>";
            echo "<td>Año:</td>";
            echo "<td>";
            echo "<select id='cmb_agno' class='form-control'>";
            llena_cmb_agnos();
            echo "</select>";
            echo "</td>";
            echo "<td>";
            echo "<button type='button' class='btn btn-default' onclick='rpt_sb2()'>Ver...</button>";
            echo "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td colspan='3'><div id='contenedor_2'></div></td>";
            echo "</tr>";
        echo "</table>";
        echo "</form>";
    echo "</div>";
}

if($_GET['xcontrol_op']=="prpt_sb2"){
     $query="select * from rptxmescp2(".$_GET['xc_agno'].",'".$_SESSION['cod_prd']."') ";
     if($result=pg_query($query)){
         echo "<table id='tabla1' class='table table-striped table-hover'>";
         echo "<tr><td colspan='16' align='center'><strong>Reporte de Ingresos por Cuentas Presupuestarias de Ingresos y Por Mes Año: ".$_GET['xc_agno']."</strong></td></tr>";
         echo "<tr>";
         echo "<td align='center'>Cod_spa</td>";
         echo "<td align='center'>Descripción</td>";
         echo "<td align='center'>Enero</td>";
         echo "<td align='center'>Febrero</td>";
         echo "<td align='center'>Marzo</td>";
         echo "<td align='center'>Abril</td>";
         echo "<td align='center'>Mayo</td>";
         echo "<td align='center'>Junio</td>";
         echo "<td align='center'>Julio</td>";
         echo "<td align='center'>Agosto</td>";
         echo "<td align='center'>Septiembre</td>";
         echo "<td align='center'>Octubre</td>";
         echo "<td align='center'>Noviembre</td>";
         echo "<td align='center'>Diciembre</td>";
         echo "<td align='center'>Total</td>";
         echo "</tr>";
         $enero=0;
         $febrero=0;
         $marzo=0;
         $abril=0;
         $mayo=0;
         $junio=0;
         $julio=0;
         $agosto=0;
         $septiembre=0;
         $octubre=0;
         $noviembre=0;
         $diciembre=0;
         $total=0;
         while($row=pg_fetch_assoc($result)){
             echo "<tr>";
             echo "<td style='font-size=7px;'>".$row['cod_spa']   ."</td>";
             echo "<td style='font-size=7px;'>".$row['des']       ."</td>";
             $enero=$enero+$row['enero'];
             $febrero=$febrero+$row['febrero'];
             $marzo=$marzo+$row['marzo'];
             $abril=$abril+$row['abril'];
             $mayo=$mayo+$row['mayo'];
             $junio=$junio+$row['junio'];
             $julio=$julio+$row['julio'];
             $agosto=$agosto+$row['agosto'];
             $septiembre=$septiembre+$row['septiembre'];
             $octubre=$octubre+$row['octubre'];
             $noviembre=$noviembre+$row['noviembre'];
             $total=$total+$row['total'];
             echo "<td align='right'>".number_format($row['enero'],2,',', '.')     ."</td>";
             echo "<td align='right'>".number_format($row['febrero'],2,',', '.')   ."</td>";
             echo "<td align='right'>".number_format($row['marzo'],2,',', '.')     ."</td>";
             echo "<td align='right'>".number_format($row['abril'],2,',', '.')     ."</td>";
             echo "<td align='right'>".number_format($row['mayo'] ,2,',', '.')     ."</td>";
             echo "<td align='right'>".number_format($row['junio'],2,',', '.')     ."</td>";
             echo "<td align='right'>".number_format($row['julio'],2,',', '.')     ."</td>";
             echo "<td align='right'>".number_format($row['agosto'],2,',', '.')    ."</td>";
             echo "<td align='right'>".number_format($row['septiembre'],2,',', '.')."</td>";
             echo "<td align='right'>".number_format($row['octubre'],2,',', '.')   ."</td>";
             echo "<td align='right'>".number_format($row['noviembre'],2,',', '.') ."</td>";
             echo "<td align='right'>".number_format($row['diciembre'],2,',', '.') ."</td>";
             echo "<td align='right'>".number_format($row['total'] ,2,',', '.')    ."</td>";
             echo "</tr>";
         }
         echo "<tr>";
         echo "<td></td>";
         echo "<td></td>";
         echo "<td align='right'>".number_format($enero,2,',','.')     ."</td>";
         echo "<td align='right'>".number_format($febrero,2,',','.')   ."</td>";
         echo "<td align='right'>".number_format($marzo,2,',','.')     ."</td>";
         echo "<td align='right'>".number_format($abril,2,',','.')     ."</td>";
         echo "<td align='right'>".number_format($mayo,2,',','.')      ."</td>";
         echo "<td align='right'>".number_format($junio,2,',','.')     ."</td>";
         echo "<td align='right'>".number_format($julio,2,',','.')     ."</td>";
         echo "<td align='right'>".number_format($agosto,2,',','.')    ."</td>";
         echo "<td align='right'>".number_format($septiembre,2,',','.')."</td>";
         echo "<td align='right'>".number_format($octubre,2,',','.')   ."</td>";
         echo "<td align='right'>".number_format($noviembre,2,',','.') ."</td>";
         echo "<td align='right'>".number_format($diciembre,2,',','.') ."</td>";
         echo "<td align='right'>".number_format($total,2,',','.')     ."</td>";
         echo "</tr>";
         echo "</font>";
         echo "<td colspan='16' align='center'>";
         ?>
            <button type="button" class="btn btn-default" onclick="tableToExcel('tabla1','Presupuesto de Ingresos')">Exportar a Excel</button>
        <?php
         echo "</td>";
         echo "</tr>";
         echo "</table>";
     }else{
         echo "Error de Aplicación de Consulta";
     }
  }

if($_GET['xcontrol_op']=="busc_cuentasb"){
    $query="select * from cuentas_b where cod_cuen='".trim($_GET['xcod'])."'
           and cod_ente='".$_SESSION['cod_ente']."'";
    if($result=pg_query($query)){
        if(pg_num_rows($result)>0){
            $row=pg_fetch_assoc($result);
            echo "1#".$row['num_cuen']."#".$row['cod_ban']."#".$row['usu_cuen'];
        }else{
            echo "2";
        }
    }
}


if($_GET['xcontrol_op']=="muestra_concepto"){
	$query="select * from concepto where cod='".$_REQUEST['xcod_concepto']."'";
	if($result=pg_query($query)){
		$row=pg_fetch_assoc($result);
		echo "<div class='form-group'>
				<table>";
				if(strlen(trim($row['v1']))>0){
				echo "	<tr>
						<td align='right'>".$row['v1']."</td>
						<td> <input type='text' name='tv1' id='tv1' class='form-control' maxlength='60'> </td>
					</tr>";
				}
				if(strlen(trim($row['v2']))>0){
				echo "
					<tr>
						<td align='right'>".$row['v2']."</td>
						<td> <input type='text' name='tv2' id='tv2' class='form-control' maxlength='60'> </td>
					</tr>";
				}
				if(strlen(trim($row['v3']))>0){
				echo "
					<tr>
						<td align='right'>".$row['v3']."</td>
						<td> <input type='text' name='tv3' id='tv3' class='form-control' maxlength='60'> </td>
					</tr>";
				}
				if(strlen(trim($row['v4']))>0){
				echo "
					<tr>
						<td align='right'>".$row['v4']."</td>
						<td> <input type='text' name='tv4' id='tv4' class='form-control' maxlength='60'> </td>
					</tr>";
				}
				if(strlen(trim($row['v5']))>0){
				echo "
					<tr>
						<td align='right'>".$row['v5']."</td>
						<td> <input type='text' name='tv5' id='tv5' class='form-control' maxlength='60'> </td>
					</tr>";
				}
				if(strlen(trim($row['v6']))>0){
				echo "
					<tr>
						<td align='right'>".$row['v6']."</td>
						<td> <input type='text' name='tv6' id='tv6' class='form-control' maxlength='60'> </td>
					</tr>";
				}
				if(strlen(trim($row['v7']))>0){
				echo "
					<tr>
						<td align='right'>".$row['v7']."</td>
						<td> <input type='text' name='tv7' id='tv7' class='form-control' maxlength='60'> </td>
					</tr>";
				}
				if(strlen(trim($row['v8']))>0){
				echo "
					<tr>
						<td align='right'>".$row['v8']."</td>
						<td> <input type='text' name='tv8' id='tv8' class='form-control' maxlength='60'> </td>
					</tr>";
				}
				echo "
				<tr>
					<td align='right'>U. T.: </td>
					<td> <input type='text' id='ut' name='ut' placeholder='0.00' />  </td>
				</tr>
				<tr>
					<td align='right'>Monto: </td>
					<td> <input type='text' id='monto' name='monto' placeholder='0.00' />  </td>
				</tr>
				<tr>
					<td align='right'>Descuento: </td>
					<td> <input type='text' id='descuento' name='descuento' placeholder='indique porcentaje' />  </td>
				</tr>
				<tr>
					<td align='right'>Neto: </td>
					<td> <input type='text' id='neto' name='neto' placeholder='0.00' />  </td>
				</tr>
				<tr><td align='center'>
				<button type='button'class='btn btn-success'>Agregar</button>
				</td></tr></table>
		     </div>";
	}
}


if($_GET['xcontrol_op']=="busca_contri"){
	$sql="select * from contribuyente where cod_contri='".$_GET['codigo']."' and tip_contri='".$_GET['cod_tpc']."'";
	if($result=pg_query($sql)){
		$row=pg_fetch_assoc($result);
		echo "1#".$row['nom_contri']."#".$row['tel_contri']."#".$row['tcl_contri']."#".$row['dir_contri']."#".$row['email_contri']."#".$row['dir_contri'];
	}
}else{
	echo "2#";
}


if($_GET['xcontrol_op2']=="cargarCmbVehiculoContribuyente"){
	$sql="select v.vh_placa, v.vh_model , u.desv_uso 
			from vh_contri v 
			left join vh_uso u on u.codv_uso = v.cod_uso 
			where v.cod_contri='".$_GET['codigo']."'";
	echo "<option value='S'> -- Seleccione -- </option>";
	if($result=pg_query($sql)){
		while ($row=pg_fetch_assoc($result)) {
			echo "<option value='".$row['vh_placa']."'> ".$row['vh_placa']." - ".$row['vh_model']." - ".$row['desv_uso']."</option>";
		}
	}
}


if($_GET['xcontrol_op']=="caragarCmbFichaCatastral"){
	$sql="select cod_catast, dir_catast, area_catast 
	     from catast_contri 
	     where cod_contri='".$_GET['codigo']."'";
	if($result=pg_query($sql)){
		
	}
}


 }else{
 	echo "Error de Conexión a la Base de Datos";
 }
?>
