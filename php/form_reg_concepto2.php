<center>
<br>
	<div class="panel panel-primary"  style="width: 90%;">
		<div class="panel-heading">
			<h3>Actualización de Cuentas de Ingresos por Conceptos</h3>
		</div>
		<div class="panel-body">
      		<div class="form-group">
      			<form method="post" name="form1" id="form1" action="">
      				<table>
      					<tr>
      						<td width="40%" align="right">Concepto</td>
      						<td width="60%">
      							<select id="cmb_cpt" name="cmb_cpt" onClick="javascript:ver_cting()" class="form-control">
      								<option>Seleccione...</option>
      								<?php
      									session_start();
      									require_once "procedimientos.php";
      									require_once "controler.php";
      									$conn=conectarpg();
      									$query="select * from concepto order by des";
      									$result=pg_query($query);
      									while ($row=pg_fetch_assoc($result)) {
      										echo "<option>".$row['des']."</option>";
      									}
      								?>
      							</select>
      							<br>
      						</td>
      					</tr>
      					<tr>
      						<td colspan="2"></td>
      					</tr>
      					<tr>
      						<td align="right">Cuenta Presupuesto de Ingresos</td>
      						<td>
      							<br>
      							<select id="cmb_cting" name="cmg_cting" class="form-control">
      								<option>Seleccione...</option>
      								<?php
      									$query="select * from spartida where cod_periodo='".$_SESSION['cod_prd']."' order by cod_spa";
      									$result2=pg_query($query);
      									while ($row1=pg_fetch_assoc($result2)) {
      										echo "<option>".$row1['cod_spa']." -*- ".$row1['des_spa']."</option>";
      									}
      								?>
      							</select>
      						</td>
      					</tr>
      					<tr>
      						<td colspan="2" align="center">
      							<br>
      							<button type="button" class="btn btn-info" onclick="act_ingcpt()">Actualizar</button>
      							<button type="button" class="btn btn-success">Regresar</button>
      						</td>
      					</tr>
      					<tr>
      						<td colspan="2">
      							<div id="contenedor1" name="contenedor1">
      								<?php lista_conceptos(); ?>
      							</div>
      						</td>
      					</tr>
      				</table>
      			</form>
      		</div>
      	</div>
	</div>

</center>
