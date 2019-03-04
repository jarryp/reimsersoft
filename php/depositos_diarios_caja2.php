<html>
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
		<meta charset="UTF-8" >
	</head>
	<body>
		<?php
error_reporting(E_ALL ^ E_NOTICE);
			require_once "procedimientos.php";
			if($conn=conectarpg()){
				$query="select * from vista_04 where cod_caja='".$_GET['xcod_caja']."' and fecha='".$_GET['xfecha']."' order by n_recibo desc";
				if($result=pg_query($query)){
					echo "<table class='table' id='tabla1' name='tabla1'>";
					echo "<tr><th colspan='10'>Listado de Depositos (Todos) Caja #: ".$_GET['xcod_caja']." de Fecha: ". $_GET['xfecha'] ."</th></tr>";
					echo "<tr>";
					echo "<th>Nro. Recibo</th>";
					echo "<th>Fecha</th>";
					echo "<th>Fecha Deposito</th>";
					echo "<th>Forma de Pago</th>";
					echo "<th>Banco</th>";
					echo "<th>Nro. Cuenta</th>";
					echo "<th>Código</th>";
					echo "<th>Monto</th>";
					echo "<th>Código Contribuyente</th>";
					echo "<th>Contribuyente</th>";
					echo "</tr>";
					while ($row=pg_fetch_assoc($result)) {
						echo "<tr>";
						echo "<td> <font size='2'>". $row['n_recibo'] ."</font></td>";
						echo "<td> <font size='2'>". $row['fecha'] ."</font></td>";
						echo "<td> <font size='2'>". $row['fecha_d'] ."</font></td>";
						echo "<td> <font size='2'>". $row['nom_fp'] ."</font></td>";
						echo "<td> <font size='2'>". $row['nom_ban'] ."</font></td>";
						echo "<td> <font size='2'>". $row['num_cuen'] ."</font></td>";
						echo "<td> <font size='2'>". $row['deta'] ."</font></td>";
						echo "<td> <font size='2'>". number_format($row['monto'],2,',','.') ."</font></td>";
						echo "<td> <font size='2'>". $row['cod_contri'] ."</font></td>";
						echo "<td> <font size='2'>". $row['nom_contri'] ."</font></td>";
						echo "</tr>";
					}

					echo "<tr>";
					echo "<td colspan='10'> ";
					?>
					<strong>
    				<input type="button" onclick="tableToExcel('tabla1', 'Resumen de Ingresos Diario')" value="Exportar a Excel">
    				</strong> 
					<?php
					echo "</td>";
					echo "</tr>";
					echo "</table>";
				}
			}else{
				echo "Error de Conexión a la base de datos";
			}
		?>
	</body>	
</html>
