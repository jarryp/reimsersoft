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
echo "<div class='form-group'>";
echo "<form method='post' action='php/lista_depositos3.php' target='contenido1'";
echo "<center>";
echo "	<table>";
echo "<tr><th colspan='4'> Resumen Depositos Rango de Fechas </th></tr>";
echo "<tr><td align='center'><input type='date' name='fecha1' id='fecha1' size='12' placeholder='yyyy-mm-dd' required='required' /></td>";
echo "<td align='center'><input type='date' name='fecha2' id='fecha2' size='12' placeholder='yyyy-mm-dd' required='required' /></td>";
echo "<td>Order por: ";
  echo "<select id='cmb_tipo' name='cmb_tipo'>";
    echo "<option>Registro</option>";
    echo "<option>Deposito</option>";
  echo "</select>";
echo "</td>";
echo '<td> <button type="submit" class="btn btn-success">Consultar</button> </td></tr>';
echo "	</table>";
echo "</center>";
echo "</form>";
echo "</div>";

echo "<iframe name='contenido1' id='contenido1' width='100%' height='550'></iframa>";
?>
</body>
</html>