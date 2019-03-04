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
session_start();
require_once "procedimientos.php";
$conn=conectarpg();
if($conn){
/*$query="select distinct fecha, count(*) as nro, sum(neto) as total"
." from vista_03 "
." where fecha between '".$_POST['fecha1']."' and '". $_POST['fecha2'] ."'"
." group by fecha"
." order by fecha";*/

$query=" select r.fecha, count(*) as nro, sum(r.neto) as total, "
	   ." (select count(*) from recb_deta_1 d"
       ." left join recibos rcb on rcb.n_recibo=d.n_recibo"
       ." where rcb.fecha=r.fecha) as c_cpt,"
       ." (select sum(d.neto) from recb_deta_1 d"
       ." left join recibos rcb on rcb.n_recibo=d.n_recibo"
       ." where rcb.fecha=r.fecha) as conceptos,"
       ." (select count(*) from recb_deta_2 d"
       ." left join recibos rcb on rcb.n_recibo=d.n_recibo"
       ." where rcb.fecha=r.fecha and monto>0) as c_efec,"
       ." (select sum(d.monto) from recb_deta_2 d"
       ." left join recibos rcb on rcb.n_recibo=d.n_recibo"
       ." where rcb.fecha=r.fecha) as efectos"
       ." from recibos r"
       ." where r.fecha between '".$_POST['fecha1']."' and '". $_POST['fecha2'] ."' and r.est='A'"
       ." group by r.fecha "
       ." order by r.fecha ";

$result=pg_query($query);
	if($result){
		echo "<center><table bgcolor='white' border='3' name='tabla1' id='tabla1' class='table table-striped table-hover'><tr>";
		echo "<th> Concepto </th>";
		echo "<th> Nro Recibos</th>";
		echo "<th> Monto Recaudado (Bs.) </th>";
		echo "<th> Nro. Cpts</th>";
		echo "<th> Coneptos</th>";
		echo "<th> Nro. Efectos</th>";
		echo "<th> Efectos</th>";
		echo "</tr>";
		$acu1=0;
		$acu2=0;
		$acu3=0;
		$acu4=0;
		$acu5=0;
		$acu6=0;
		$cont=0;
		while ($row=pg_fetch_assoc($result)) {
			$cont=$cont+1;
			echo "<tr>";
			echo "<td align='center'>". $row['fecha'] ."</td>";
			echo "<td align='center'>". $row['nro'] ."</td>";
			echo "<td align='right'>". number_format($row['total'],2,',', '.') ."</td>";
			echo "<td align='center'>". $row['c_cpt'] ."</td>";
			echo "<td align='right'>". number_format($row['conceptos'],2,',', '.') ."</td>";
			echo "<td align='center'>". $row['c_efec'] ."</td>";
			echo "<td align='right'>". number_format($row['efectos'],2,',', '.') ."</td>";
			echo "</tr>";
			$acu1=$acu1+$row['nro'];
			$acu2=$acu2+$row['total'];
			$acu3=$acu3+$row['c_cpt'];
			$acu4=$acu4+$row['conceptos'];
			$acu5=$acu5+$row['c_efec'];
			$acu6=$acu6+$row['efectos'];
			
		}
		echo "<tr>";
		echo "<th align='right'>Totales.....</th>";
		echo "<th align='right'>". $acu1 ."</th>";
		echo "<th align='right'>". number_format($acu2,2,',', '.') ."</th>";
		echo "<th align='right'>". $acu3 ."</th>";
		echo "<th align='right'>". number_format($acu4,2,',', '.') ."</th>";
		echo "<th align='right'>". $acu5 ."</th>";
		echo "<th align='right'>". number_format($acu6,2,',', '.') ."</th>";
		echo "</tr>";	
		echo "<tr><td colspan='7' align='center'> ";
		?>
		<strong>
		<input type="button" onclick="tableToExcel('tabla1', 'Resumen de Ingresos Diario')" value="Exportar a Excel">
		</strong>	
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
