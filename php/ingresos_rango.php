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
        , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
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
$query="select distinct des, count(*) as nro, sum(neto) as total"
." from vista_03 "
." where fecha between '".$_POST['fecha1']."' and '". $_POST['fecha2'] ."'"
." and cod_prd='".$_SESSION['cod_prd']."' and cod_periodo='".$_SESSION['cod_prd']."'"
." group by des"
." order by des";

$result=pg_query($query);
	if($result){
		echo "<center><table bgcolor='white' border='3' name='tabla1' id='tabla1' class='table table-striped table-hover'><tr>";
		echo "<th> Concepto </th>";
		echo "<th> Nro Recibos</th>";
		echo "<th> Monto Recaudado (Bs.) </th></tr>";
		$acu1=0;
		$acu2=0;
		while ($row=pg_fetch_assoc($result)) {
			echo "<tr>";
			echo "<td>". strtoupper(trim($row['des'])) ."</td>";
			echo "<td>". $row['nro'] ."</td>";
			echo "<td align='right'>". number_format($row['total'],2,',', '.') ."</td>";
			echo "</tr>";
			$acu1=$acu1+$row['nro'];
			$acu2=$acu2+$row['total'];
		}
		echo "<tr>";
		echo "<th>Totales.....</th>";
		echo "<th>". $acu1 ."</th>";
		echo "<th>". number_format($acu2,2,',', '.') ."</th>";
		echo "</tr>";	
		echo "<tr><td colspan='3' align='center'> ";
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