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
error_reporting(E_ALL ^ E_NOTICE);
require_once "procedimientos.php";
$conn=conectarpg();
if($conn){

$query="select distinct date_part('year',fecha) as f_year,"
      ."         date_part('month',fecha) as f_month,"
      ."          sum(neto) as neto,"
      ."          count(*) as nro"
." from vista_03"
." where fecha between '". $_POST['fecha1'] ."' and '". $_POST['fecha2'] ."' and "
."      des='". $_POST['cmb_cpto'] ."' and cod_prd='".$_SESSION['cod_prd']."' and cod_periodo='".$_SESSION['cod_prd']."'"
." group by date_part('year',fecha),"
."         date_part('month',fecha)"
." order by date_part('year',fecha),"
."         date_part('month',fecha)";

$result=pg_query($query);
	if($result){
		echo "<center>";
		echo "<table width='75%'><tr><td>";
		echo "<table bgcolor='white' name='tabla1' id='tabla1' class='table table-striped table-hover responsive'>";
		echo "<tr><th colspan='4'>Resumen Mensual ". $_POST['cmb_cpto'] ." del ". $_POST['fecha1'] ." al ". $_POST['fecha2'] ." </th></tr>";
		echo "<tr>";
		echo "<th>Año</th>";
		echo "<th>Mes</th>";
		echo "<th>Liquidado</th>";
		echo "<th>Nro. Registros</th>";
		echo "</tr>";
		$acu1=0;
		$acu2=0;
		$acu3=0;
		while ($row=pg_fetch_assoc($result)) {
			echo "<tr>";
			echo "<td>". $row['f_year'] ."</td>";
			echo "<td>". $row['f_month'] ."</td>";
			echo "<td align='right'>". number_format($row['neto'],2,',', '.') ."</td>";
			echo "<td align='right'>". $row['nro'] ."</td>";
			echo "</tr>";
				$acu1=$acu1+1;
				$acu2=$acu2+$row['neto'];
				$acu3=$acu3+$row['nro'];
		}
		echo "<strong>";
        echo "<tr>";
      	echo "<td><b>Totales...</b></td>";
      	echo "<td align='right'><b>Meses: ". $acu1 ."</b></td>";
		echo "<td align='right'><b>Liquidado: ". number_format($acu2,2,',','.') ."</b></td>";
		echo "<td align='right'><b>Cant. Registros: ". number_format($acu3,0,',','.') ."</b></td>";
		echo "</tr>";
		echo "<tr><td colspan='4' align='center'> ";
		echo "</strong>";
		?>
		<strong>
		<input type="button" onclick="tableToExcel('tabla1', 'Resumen de Ingresos Diario')" value="Exportar a Excel">
		</strong>
		<?php
		echo " </td></tr>";
		echo "</table>";
		echo "</td></tr></table></center>";
	}
}else{
	echo "Error de Conexión con la Base de Datos";
}
?>
</body>
</html>
