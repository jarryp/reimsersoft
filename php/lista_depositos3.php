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
	  	if($_POST['cmb_tipo']=='Registro'){
	         $query="select distinct nom_ban, num_cuen, sum(monto) as monto, count(*) as nro "
	          ." from vista_04 where fecha between '". $_POST['fecha1'] ."' and '". $_POST['fecha2'] ."' "
	          ." group by nom_ban, num_cuen "
	          ." order by nom_ban";
	      }else
	      {
	      	$query="select distinct nom_ban, num_cuen, sum(monto) as monto, count(*) as nro "
	          ." from vista_04 where fecha_d between '". $_POST['fecha1'] ."' and '". $_POST['fecha2'] ."' and monto>0"
	          ." group by nom_ban, num_cuen "
	          ." order by nom_ban";
	      }
         $result=pg_query($query);
         if($result){
         	echo "<center><table width='65%'>";
         	echo "<tr><td>";
         		echo "<table id='tabla1' name='tabla1' class='table table-striped table-hover'>";
         		echo "<tr><th colspan='4'>Resumen Depositos del ".$_POST['fecha1']." al ". $_POST['fecha2'] ."</th></tr>";
         		echo "<tr>";
         		echo "<th>Banco</th>";
         		echo "<th>Nro. Cuenta</th>";
         		echo "<th>Total</th>";
         		echo "<th>Nro. Registros</th>";
         		echo "</tr>";
         		$acu1=0;
         		$acu2=0;
         		while ($row=pg_fetch_assoc($result)) {
         			echo "<tr>";
         			echo "<td>". $row['nom_ban'] ."</td>";
         			echo "<td>". $row['num_cuen'] ."</td>";
         			echo "<td align='right'>". number_format($row['monto'],2,',','.') ."</td>";
         			echo "<td align='right'>". number_format($row['nro'],0,',','.') ."</td>";
         			echo "</tr>";
         			$acu1=$acu1+$row['monto'];
         			$acu2=$acu2+$row['nro'];
         		}
         		echo "<tr>";
         		echo "<th colspan='2'>Totales...</th>";
         		echo "<td align='right'><strong>". number_format($acu1,2,',','.') ."</strong></td>";
         		echo "<td align='right'><strong>". number_format($acu2,2,',','.') ."</strong></td>";
         		echo "</tr>";
         		?>
         		<tr>
         		<td colspan="4">
				<strong>
				<input type="button" onclick="tableToExcel('tabla1', 'Resumen de Ingresos Diario')" value="Exportar a Excel">
				</strong>
				</td>
				</tr>	
				<?php
         		echo "</table>";
         	echo "</td></tr>";
         	echo "</table></center>";
         }else{
         	echo "No Resulta";
         }
	   }else{
	   	echo "Error de Conexión a la Base de Datos";
	   }
	?>
</body>
</html>