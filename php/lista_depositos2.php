<!DOCTYPE HTML>
<html lang="es">
<!--
''Software Desarrollado por Palacios Systems
''Consulta Web REIMSER SOFT
''Contacto: jarry.palacios@hotmail.com / 0416-1738065 
''Portal Web: www.palaciossystems.com / jpalacios@palaciossystems.com
-->
<head>
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<script type="text/javascript">
		var tableToExcel = (function() {
  		var uri = 'data:application/vnd.ms-excel;base64,'
    		, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    		, base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    		, format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  		return function(table, name) {
    		if (!table.nodeType) table = document.getElementById('tabla1')
    			var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    			window.location.href = uri + base64(format(template, ctx))
  			}
		})()
	</script>
</head>
<body>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<?php
require_once "procedimientos.php";
$conn=conectarpg();
if($conn){
	if ($_POST['cmb_tipo']=='Registro') {
		$query="select v.deta,"
		." v.n_recibo,"
		." v.cod_contri,"
		." v.nom_contri,"
		." v.nom_ban, v.num_cuen, v.fecha_d, v.fecha, dt.monto "
		." from vista_04 v "
		." left join recb_deta_2 dt on dt.deta=v.deta"
		." where v.fecha between '". $_POST['fecha1'] ."' and '". $_POST['fecha2'] ."' "
		." and v.nom_ban='". $_POST['cmb_banco'] ."'"
		." order by v.fecha";
	}else{
		$query="select v.deta,"
		." v.n_recibo,"
		." v.cod_contri,"
		." v.nom_contri,"
		." v.nom_ban, v.num_cuen, v.fecha_d, v.fecha, dt.monto "
		." from vista_04 v "
		." left join recb_deta_2 dt on dt.deta=v.deta"
		." where v.fecha_d between '". $_POST['fecha1'] ."' and '". $_POST['fecha2'] ."' "
		." and v.nom_ban='". $_POST['cmb_banco'] ."'"
		." order by v.fecha_d";
	}
	$result=pg_query($query);
	if($result){
		echo "<table class='table table-striped table-hover' id='tabla1' name='tabla1'>";
		echo "<font size='2'>";
		echo "<tr><td colspan='9'>Listado de Depositos ". $_POST['cmb_banco'] ." del ". $_POST['fecha1'] ." al ". $_POST['fecha2'] ."</td></tr>";
		echo "<tr>";
		echo "<th>Nro. Recibo</th>";
		echo "<th>Fec. Registro</th>";
		echo "<th>Nro. Deposito</th>";
		echo "<th>Fec. Deposito</th>";
		echo "<th>Cédula / RIF</th>";
		echo "<th>Contribuyente</th>";
		echo "<th>Banco</th>";
		echo "<th>Nro. Cuenta</th>";
		echo "<th>Monto</th>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='3'> Cantidad de Resultados: ". pg_num_rows($result) ."</td>";
		echo "<td colspan='6'>";
		?>
		<strong>
		<input type="button" onclick="tableToExcel('tabla1', 'Resumen de Ingresos Diario')" value="Exportar a Excel">
		</strong>
		<?php
		echo "</td>";
		echo "</tr>";
		while ($row=pg_fetch_assoc($result)) {
			echo "<tr>";
			echo "<td>". $row['n_recibo'] ."</td>";
			echo "<td>". $row['fecha'] ."</td>";
			echo "<td>". $row['deta'] ."</td>";
			echo "<td>". $row['fecha_d'] ."</td>";
			echo "<td>". $row['cod_contri'] ."</td>";
			echo "<td>". $row['nom_contri'] ."</td>";
			echo "<td>". $row['nom_ban'] ."</td>";
			echo "<td>". $row['num_cuen'] ."</td>";
			echo "<td align='right'>". number_format($row['monto'],2,',', '.') ."</td>";
			echo "</tr>";
		}
		echo "</font>";
		echo "</table>";
	}
}else{
	echo "Error de Conexión a la Base de Datos";
}
?>
</body>
</html>