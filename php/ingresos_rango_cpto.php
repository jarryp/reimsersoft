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
session_start();
require_once "procedimientos.php";
$conn=conectarpg();
if($conn){

$query="select fecha, n_recibo, cod_contri, nom_contri, ser, num, neto, est"
." from vista_03 "
." where fecha between '".$_POST['fecha1']."' and '". $_POST['fecha2'] ."' "
." and des='". $_POST['cmb_cpto'] ."' and cod_prd='".$_SESSION['cod_prd']."' and cod_periodo='".$_SESSION['cod_prd']."'"
." order by fecha, n_recibo";

$result=pg_query($query);
	if($result){
		echo "<center><table bgcolor='white' border='3' name='tabla1' id='tabla1' class='table table-striped table-hover responsive'><tr>";
		echo "<th> Fecha </th>";
		echo "<th> Recibo </th>";
		echo "<th> Cédula/RIF </th>";
		echo "<th> Contribuyente </th>";
		echo "<th> Serie </th>";
		echo "<th> Código </th>";
		echo "<th> Neto </th>";
		echo "<th> Status </th></tr>";
		$acu1=0;
		$acu2=0;
		while ($row=pg_fetch_assoc($result)) {
			echo "<tr>";
			echo "<td>". $row['fecha'] ."</td>";
			echo "<td>". $row['n_recibo'] ."</td>";
			echo "<td>". $row['cod_contri'] ."</td>";
			echo "<td>". $row['nom_contri'] ."</td>";
			echo "<td>". $row['ser'] ."</td>";
			echo "<td>". $row['num'] ."</td>";
			echo "<td align='right'>". number_format($row['neto'],2,',', '.') ."</td>";
			echo "<td>". $row['est'] ."</td>";
			echo "</tr>";
			$acu1=$acu1+1;
			$acu2=$acu2+$row['neto'];
		}
		echo "<tr>";
		echo "<th colspan='6'>Totales (Cant. Registros, Recaudado).....</th>";
		echo "<th>". $acu1 ."</th>";
		echo "<th>". number_format($acu2,2,',', '.') ."</th>";
		echo "</tr>";	
		echo "<tr><td colspan='8' align='center'> ";
		?>	
		<input type="button" onclick="tableToExcel('tabla1', 'Resumen de Ingresos Diario')" value="Exportar a Excel">
		<?php
		echo " </td></tr>";
		echo "</table></center>";
	}
}else{
	echo "Error de Conexión con la Base de Datos";
}
?>
</body>
</html>