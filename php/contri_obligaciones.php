
<?php

require_once "procedimientos.php";

if($conn=conectarpg()){

	echo "<table class='table'>";
	echo "<tr>";
	echo "<th colspan='2'> Resumen de Pagos desde Enero 2013</th>";
	echo "</tr>";
	$query="select distinct des, count(*) as nro"
	." from vista_03"
	." where cod_contri='". $_POST['codigo'] ."'"
	." group by des"
	." order by nro desc, des";
	$result2=pg_query($query);
	if($result2){
		echo "<th>Concepto de Pago</th>";
		echo "<th>Cantidad</th>";
		while ($row2=pg_fetch_assoc($result2)) {
			echo "<tr>";
			echo "<td>". $row2['des'] ."</td>";
			echo "<td>". $row2['nro'] ."</td>";
			echo "</tr>";
		}
	}else{
		echo "Error de Consulta de Historia de Conceptos de Pago...";
	}
    echo "</table>";
	echo "<table class='table'>";
	$query="select cod_catast, dirc_catast, area_catast, est, date(fecha) as fecha "
	." from catast_contri "
	." where cod_contri='". $_POST['codigo'] ."'"
	." order by fecha desc ";
	$result=pg_query($query);
	echo "<tr>";
	echo "<th colspan='5'>Información de Registro Catastral</th>";
	echo "</tr><tr>";
	echo "<th>Código</th>";
	echo "<th>Dirección</th>";
	echo "<th>Área</th>";
	echo "<th>Status</th>";
	echo "<th>Fecha de Registro</th>";
	if($result){
     while ($row=pg_fetch_assoc($result)) {
     	echo "<tr>";
     	echo "<td>". $row['cod_catast'] ."</td>";
     	echo "<td>". $row['dir_catast'] ."</td>";
     	echo "<td>". $row['area_catast'] ."</td>";
     	echo "<td>". $row['est'] ."</td>";
     	echo "<td>". $row['fecha'] ."</td>";
     	echo "</tr>";
     }
	}else{
		echo "Posible error de consulta...";
	}
	echo "</tr>";
	echo "</table>";
	$query="select des_tvh, vh_model, vh_placa, vh_color, vh_semotor, vh_secar"
	." from vista_08"
	." where cod_contri='". $_POST['codigo'] ."'";
	$result1=pg_query($query);
	if($result1){
		echo "<table class='table'>";
		echo "<tr><th colspan='6'>Registro de Vehiculos</th></tr>";
		echo "<tr>";
		echo "<th>Tipo</th>";
		echo "<th>Modelo</th>";
		echo "<th>Placa</th>";
		echo "<th>Color</th>";
		echo "<th>Serial Motor</th>";
		echo "<th>Serial Carroceria</th>";
		echo "</tr>";
		while ($row1=pg_fetch_assoc($result1)) {
			echo "<tr>";
			echo "<td>". $row1['des_tvh'] ."</td>";
			echo "<td>". $row1['vh_model'] ."</td>";
			echo "<td>". $row1['vh_placa'] ."</td>";
			echo "<td>". $row1['vh_color'] ."</td>";
			echo "<td>". $row1['vh_semotor'] ."</td>";
			echo "<td>". $row1['vh_secar'] ."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}else{
		echo "Posible error de consulta de vehiculos";
	}
}else{
	echo "Error de Conexión a la base de Datos...";
}

?>