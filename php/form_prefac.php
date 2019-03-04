
<?php  require_once("procedimientos.php"); 
 session_start();
?>
<div class="row">
	<div class="col-md-6">
		<form role="form">
			<table>
			<tr>
				<td align="right"><label>Tipo</label></td>
				<td>
					<select id="cmbTcontri" class="form-control">
						<option value="S"> -- Seleccione -- </option>
						<?php 
							$sql="select * from tip_contri";
							if($conn=conectarpg()){
								if($result=pg_query($sql)){
									while ($row=pg_fetch_assoc($result)) {
										echo "<option value='".$row['cod_tpc']."'> ".$row['des_tpc']."</option>";
									}
								}
								pg_close($conn);
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align='right'><label>Cédula:</label></td>
				<td><input type="text" name="cedula" id="cedula" placeholder="16233325" maxlength="10"  class="form-control" onblur="busca_contri()"></td>
			</tr>
			<tr>
				<td align='right'><label>Nombre/Razon Social:</label></td>
				<td><input type="text" name="nombre" id="nombre" maxlength="100" class="form-control"></td>
			</tr>
			<tr>
				<td align='right'><label>Email:</label></td>
				<td><input type="email" name="correo" id="correo" maxlength="80" class="form-control"></td>
			</tr>
			<tr>
				<td align='right'><label>Telefono:</label></td>
				<td><input type="text" name="telefono" id="telefono" maxlength="15"  class="form-control"></td>
			</tr>
			<tr>
				<td align='right'><label>Celular:</label></td>
				<td><input type="text" name="celular" id="celular" maxlength="15"  class="form-control"></td>
			</tr>
			<tr>
				<td align='right'><label>Dirección:</label></td>
				<td><textarea rows="4" cols="40" id="direccion" name="direccion"  class="form-control"></textarea></td>
			</tr>
			<tr>
				<td align="right"><label>Vehículos</label></td>
				<td>
					<select id="cmbVehiculos" class="form-control">
						<option value="S"> -- Seleccione -- </option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right"><label>Fichas Catastrales</label></td>
				<td>
					<select id="cmbFcatastral" class="form-control">
						<option value="S"> -- Seleccione -- </option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<br>
					<button class="btn btn-primary">Guardar</button>
				</td>
			</tr>
			</table>
		</form>
	</div>
	<div class="col-md-6">
		<label>Concepto: </label>
		<select id="cmbConcepto" name="cmbConcepto" onchange="presenta()" class="form-control">
			<option value="S"> -- Seleccione -- </option>
		<?php
		   
		    if($conn=conectarpg()){ 
				$query="select cod, des from concepto where cod_prd='".$_SESSION['cod_prd']."' order by des";
				if($result=pg_query($query)){
					while($row=pg_fetch_assoc($result)){
						echo "<option value=".$row['cod']."> ".$row['cod']." - ".ucfirst(strtolower($row['des']))."</option>";
					}
				}
				pg_close($conn);
			}
		?>	
		</select>

		<div id="area_concepto" class="row">
			
		</div>
	</div>
</div>
<div class="row" id="area_seleccion">
	<br>
	<table id="tblDetalle" border="1" width="100%">
		<tr>
			<th>Codigo</th>
			<th>Denominación</th>
			<th>Detalle 1</th>
			<th>Detalle 2</th>
			<th>Detalle 3</th>
			<th>Detalle 4</th>
			<th>Detalle 5</th>
			<th>Detalle 6</th>
			<th>Detalle 7</th>
			<th>Detalle 8</th>
		</tr>
	</table>

	<br>
	<br>
</div>


<script type="text/javascript">
	

function presenta(){
	$.ajax({
		type:"GET",
		url:"php/controler.php",
		data:{xcontrol_op:"muestra_concepto",xcod_concepto:document.getElementById("cmbConcepto").value},
		success:function(response){
			$("#area_concepto").html(response);
		}
	});
}


function busca_contri(){
	var xcod_tpc = document.getElementById("cmbTcontri").value;
	var xcodigo  = document.getElementById("cedula").value;
	if( xcod_tpc.trim()!="" && xcodigo.trim()!="" ){
		$.ajax({
			type:"GET",
			url:"php/controler.php",
			data:{cod_tpc:xcod_tpc,codigo:xcodigo,xcontrol_op:"busca_contri"},
			success:function(response){
				var xdata = response.split("#");
				$("#nombre").val(xdata[1]);
				$("#correo").val(xdata[5]);
				$("#telefono").val(xdata[2]);
				$("#celular").val(xdata[3]);
				$("#direccion").val(xdata[6]);
				cargarCmbVehiculoContribuyente(xcodigo);
				caragarCmbFichaCatastral(xcodigo);
			}
		});
	}
}


function cargarCmbVehiculoContribuyente(xcodigo){
	$.ajax({
		type:"GET",
		url:"php/controler.php",
		data:{xcontrol_op2:"cargarCmbVehiculoContribuyente",codigo:xcodigo},
		success:function(response1){
			$("#cmbVehiculos").html(response1);
		}
	});
}


function caragarCmbFichaCatastral(xcodigo){
	$.ajax({
		type:"GET",
		url: "php/controler.php",
		data:{xcontrol_op:"caragarCmbFichaCatastral",codigo:xcodigo},
		success:function(response){
			$("#cmbFcatastral").html(response);	
		}
	});
}


</script>